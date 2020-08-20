<?php

namespace Src\Controller;

use Src\TableGateways\FileGateway;
use Src\System\Mailer;

require_once( __DIR__ . '/../../../audioParser/getid3/getid3.php');

class FileController
{

    private $db;
    private $requestMethod;
    private $fileId;
    private $mailer;

    private $fileGateway;

    public function __construct($db, $requestMethod, $fileId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->fileId = $fileId;
        $this->mailer = new Mailer($db);

        $this->fileGateway = new FileGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if (isset($_GET["cancel"])) {
                    $response = $this->cancelUpload();
                } else {
                    if ($this->fileId) {
                        $response = $this->getFile($this->fileId);
                    } else {
                        $response = $this->getAllFiles();
                    }
                }
                break;
            case 'POST':
                if (isset($_POST["cancel"])) {
                    $response = $this->cancelUpload();
                } else {
                    if ($this->fileId) {
                        $response = $this->updateFileFromRequest($this->fileId);
                    } else {
                        $response = $this->uploadFilesFromRequest();
                    }
//                    $response = $this->uploadFilesFromRequest();
                }
                break;
//            case 'PUT':
//                $response = $this->updateFileFromRequest($this->fileId);
//                break;
//            case 'DELETE':
//                $response = $this->deleteFile($this->fileId);
//                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllFiles()
    {
        $result = $this->fileGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getFile($id)
    {
        $result = $this->fileGateway->find($id);
        /*if (! $result) {
//            return $this->notFoundResponse();
        }*/
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createFileFromRequest()
    {
        $input = (array)json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateFile($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->fileGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function cancelUpload()
    {
        $suffix = "job_upload";
        $key = ini_get("session.upload_progress.prefix") . $suffix;
        $_SESSION[$key]["cancel_upload"] = true;
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(array(
            "msg" => "upload cancelled",
            "error" => false
        ));
        return $response;
    }

    private function uploadFilesFromRequest()
    {
        if (isset($_FILES)
            && !empty($_FILES)
            && isset($_POST["authorName"])
            && isset($_POST["jobType"])
            && isset($_POST["dictDate"])
            && isset($_POST["speakerType"])
        ) {
            $uploadMsg = [];
            $path = '../../../../uploads/';

            // prevent wildcards
            foreach ($_POST as $item) {
                if (strpos($item, '%') !== FALSE) {
                    return $this->errorOccurredResponse("Invalid Input (5)");
                }
            }

            $allowedMimeTypes = array(
                "audio/mpeg",       // .mp3
                "audio/x-wav",      // .wav
                "audio/ogg",        // .ogg
                "application/octet-stream" // .ds2

            );

            $authorName = $_POST["authorName"];
            $jobType = $_POST["jobType"];
            $dictDate = $_POST["dictDate"];

            $uf1 = isset($_POST["user_field_1"]) ? $_POST["user_field_1"] : null;
            $uf2 = isset($_POST["user_field_2"]) ? $_POST["user_field_2"] : null;
            $uf3 = isset($_POST["user_field_3"]) ? $_POST["user_field_3"] : null;

            if (!$this->validateAndReturnDate($dictDate)) {
                return $this->errorOccurredResponse("invalid date format.");
            }

            // 1 -> single speaker, 2 -> multiple speaker, otherwise defaults to 1
            $speakerType = $_POST["speakerType"];
            if (!is_numeric($speakerType)) {
                return $this->errorOccurredResponse("invalid speaker type.");
            } else {
                if ($speakerType != 1 && $speakerType != 2) {
                    $speakerType = 1; // default
                }
            }
            $comments = isset($_POST["comments"]) ? $_POST["comments"] : null; // Optional


            // check for permission for the given account id to upload files to or use session \\
//            $acc_id = $_SESSION["accID"];
            $acc_id = null;
            $post_acc_id = null;
            $stopUpload = false;

            if (isset($_POST["set_acc_id"]) && !empty($_POST["set_acc_id"])) {
                $post_acc_id = $_POST["set_acc_id"];
                // curl to check if current user have insert permission to the acc_id passed via the request params
                if(!$this->checkForInsertPermission($_POST["set_acc_id"])) { // no permission
                    $uploadMsg[] = $this->formatFileResult("NA", "You don't have permission to upload to this account", true);
                    $stopUpload = true; // stop the upload
                }else{
                    $acc_id = $post_acc_id;
                }
            } else{ // use current session accID
                if(isset($_SESSION["accID"]))
                {
                    $acc_id = $_SESSION["accID"];
                    if($acc_id == 0) {
                        $uploadMsg[] = $this->formatFileResult("NA", "Account not set", true);
                        $stopUpload = true; // stop the upload
                    }
                } else{
                    $uploadMsg[] = $this->formatFileResult("NA", "Account not set", true);
                    $stopUpload = true; // stop the upload
                }
            }

            $nextJobNumbers = $this->fileGateway->getNextJobNumbers($acc_id);
            if ($nextJobNumbers == false) {
                return $this->errorOccurredResponse("while generating job number.");
            }
            if(!$stopUpload){
                $nextJobNumbers = json_decode($nextJobNumbers, true);
                $nextFileID = $nextJobNumbers["next_job_id"];
                $nextJobNum = $nextJobNumbers["next_job_num"];
            }

            $newFilesAvailable = false;

            foreach ($_FILES as $key => $fileItem) {
                if($stopUpload){break;}
                if ($fileItem["error"] != 0) {
                    $uploadMsg[] = $this->formatFileResult($key, "File read error", true);
                    continue;
                }
                $file_name = $fileItem['name'];
                $file_tmp = $fileItem['tmp_name'];
                $file_size = $fileItem['size'];
                $file_real_mime_type = mime_content_type($file_tmp);

                $jobPrefix = $this->fileGateway->getAccountPrefix($acc_id);
                if(!$jobPrefix)
                {
                    // die("couldn't get job prefix");
                    $uploadMsg[] = $this->formatFileResult($file_name, "couldn't retrieve account prefix", true);
                    unlink($file_tmp); // delete the tmp file.
                    continue;
                }
                // enumerating file names
                $enumName = "F" . $nextFileID . "_" . str_replace("-", "", $jobPrefix) . $nextJobNum . "_" . str_replace(" ", "_", $file_name);
                $orig_filename = $file_name;
                $file_name = $enumName;
                $file = $path . $file_name;


                if (!in_array($file_real_mime_type, $allowedMimeTypes)) {
                    $uploadMsg[] = $this->formatFileResult($orig_filename, "upload failed (file type not allowed - $file_real_mime_type)", true);
                    unlink($file_tmp); // delete the tmp file.
                    continue;
                }
                //Max file upload size is 128MB. PHP is configured for max size of 128MB
                if ($file_size > 134217728) {
//                    $uploadMsg[] = "<li>File: $orig_filename - <span style='color:red;'>UPLOAD FAILED </span>(File size exceeds limit)</li>";
                    $uploadMsg[] = $this->formatFileResult($orig_filename, "upload failed file size exceeds limit", true);
                    continue;
                }

                $getID3 = new \getID3;
                $fileInfo = $getID3->analyze($file_tmp);
                $org_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION)); // dss audio length check - working with DSS & DS2
                $file_duration = (int)ceil(@$fileInfo['playtime_seconds']);
                $dur_received = isset($_POST["dur" . str_replace("file", "", $key)])?$_POST["dur" . str_replace("file", "", $key)]:0;
                if($file_duration == 0 && $dur_received != null && $dur_received != 0)
                {
                    $file_duration = $dur_received;
                }
                //Building demographic array for DB insert function call
                $fileDemos = array(
                    $nextFileID,
                    $nextJobNum,
                    $authorName,
                    $jobType,
                    $dictDate,
                    $speakerType,
                    $comments,
                    $orig_filename,
                    $file_name,
                    $file_duration,
                    $uf1,
                    $uf2,
                    $uf3,
                    $acc_id,
                    $org_ext
                );

                $uplSuccess = move_uploaded_file($file_tmp, $file);
                if ($uplSuccess) {
                    $result = $this->fileGateway->insertUploadedFileToDB($fileDemos);
                    if ($result) {
//                        $uploadMsg[] = "<li>File: $orig_filename - <span style='color:green;'>UPLOAD SUCCESSFUL</span></li>";
                        $uploadMsg[] = $this->formatFileResult($orig_filename, "upload successful", false);
                        $newFilesAvailable = true;
                    } else {
//                        $uploadMsg[] = "<li>'File: ' $orig_filename . ' - FAILED (File uploaded but error writing to database)'<li>";
                        $uploadMsg[] = $this->formatFileResult($orig_filename, "upload failed please contact website administrator (2)", true);
                    }
                } else {
//                    $uploadMsg[] = "<li>'File: ' . $orig_filename . ' - UPLOAD FAILED (An error occurred during upload)'</li>";
                    $uploadMsg[] = $this->formatFileResult($orig_filename, "upload failed please contact website administrator (3)", true);
                }

                $nextFileID++;
                $nextJobNum++;
            }

//            header('Content-Type: application/json');
//            echo json_encode(array_values($uploadMsg), JSON_FORCE_OBJECT | JSON_PRETTY_PRINT);
            if($newFilesAvailable){
                $this->mailer->sendEmail(15, "sales@vtexvsi.com");
            }

            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode(array_values($uploadMsg), JSON_FORCE_OBJECT | JSON_PRETTY_PRINT);;
            return $response;

        } else {
            // todo no files set for upload
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode([
                'error' => true,
                'msg' => 'Invalid input'
            ]);
            return $response;
        }
        // -----------------********----------------- //
//        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
//        if (! $this->validateFile($input)) {
//            return $this->unprocessableEntityResponse();
//        }
//        $this->fileGateway->insert($input);
//        $response['status_code_header'] = 'HTTP/1.1 201 Created';
//        $response['body'] = null;
//        return $response;
    }

    private function checkForInsertPermission($accID)
    {
        $strCookie = 'pvs=' . $_COOKIE['pvs'] . '; path=/';
        session_write_close();

        /*function _isCurl(){
            return function_exists('curl_version');
        }

        if(_isCurl())
        {
            print_r(curl_version());
        }
        else{
            echo "curl not installed";
        }*/

        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => getenv("BASE_LINK").'/api/v1/access?out&account_id='.$accID,
            CURLOPT_USERAGENT => 'Files API',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 2,
        //    CURLOPT_COOKIESESSION => false
            CURLOPT_COOKIE => $strCookie,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            )
        ]);

        // todo disable for production the 2 lines below
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        $jsonArrayResponse = json_decode($resp, true);

        return !$jsonArrayResponse["error"];
    }

    private function formatFileResult($fileName, $status, $error)
    {
        return array(
            "file_name" => $fileName,
            "status" => $status,
            "error" => $error
        );
    }

    private function updateFileFromRequest($id)
    {
        /*$result = $this->fileGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }*/

        $result = $this->fileGateway->update($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = $result;
        return $response;
    }

    private function deleteFile($id)
    {
        $result = $this->fileGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->fileGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateFile($input)
    {
        if (!isset($input['firstname'])) {
            return false;
        }
        if (!isset($input['lastname'])) {
            return false;
        }
        return true;
    }

    private function validateAndReturnDate($date)
    {
        // (accepted format: yyyy-mm-dd hh:mm:ss)
//        $dateArr = explode("-", $date);
        $dateTimeArr = explode(" ", $date);
        // Date check
        $date = $dateTimeArr[0];
        $dateArr = explode("-", $date);
        //                                             |> month - day - year
        $dateValid = sizeof($dateArr) == 3 && checkdate($dateArr[1], $dateArr[2], $dateArr[0]);

        if(sizeof($dateTimeArr) == 1)
        {
            // date only passed
            return true;
        }
        else if(sizeof($dateTimeArr) > 2){
            return false;
        }

        // Time check
        $time = $dateTimeArr[1];
        $timeArr = explode(":", $time);
        $timeValid = sizeof($timeArr) == 3 &&
            $timeArr[0] >= 0 && $timeArr[1] >= 0 && $timeArr[2] >= 0 &&  // not negative
            $timeArr[0] < 24 && $timeArr[1] < 60 && $timeArr[2] < 60;

        return $timeValid && $dateValid;

    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => true,
            'msg' => 'Invalid input'
        ]);
        return $response;
    }

    private function errorOccurredResponse($error_msg = "")
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Error Occurred';
        $response['body'] = json_encode([
            'error' => true,
            'msg' => 'Error Occurred ' . $error_msg
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
