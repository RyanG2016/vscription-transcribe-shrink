<?php

namespace Src\Controller;

use Src\Helpers\common;
use Src\Models\Account;
use Src\Models\SR;
use Src\TableGateways\FileGateway;
use Src\System\Mailer;
use Src\TableGateways\accessGateway;

require_once( __DIR__ . '/../../../audioParser/getid3/getid3.php');

class FileController
{

    private $db;
    private $requestMethod;
    private $fileId;
    private $rawURI;
    private $mailer;
    private $common;

    private $fileGateway;
    private $accessGateway;

    public function __construct($db, $requestMethod, $fileId, $rawURI)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->fileId = $fileId;
        $this->rawURI = $rawURI;
        $this->mailer = new Mailer($db);
        $this->common = new common();

        $this->fileGateway = new FileGateway($db);
        $this->accessGateway = new accessGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if (isset($_GET["cancel"])) {
                    $response = $this->cancelUpload();
                } else {
                    if($this->rawURI[0] == "chart")
                    {
                        $response = $this->getChartData();
                    }
                    else if($this->rawURI[0] == "pending"){
                        $response = $this->getPendingFiles();
                    }
                    else if($this->rawURI[0] == "completed"){
                        $response = $this->getCompletedFiles();
                    }
                    else if ($this->fileId) {
                        $response = $this->getFile($this->fileId);
                    }
                    else {
                        $response = $this->getAllFiles();
                    }
                }
                break;
            case 'POST':
                if (isset($_POST["cancel"])) {
                    $response = $this->cancelUpload();
                } else {
                    if ($this->fileId) {
                        if(isset($this->rawURI[1]) && $this->rawURI[1] == "discard")
                        {
                            $response = $this->discardFile($this->fileId);
                        }else{
                            $response = $this->updateFileFromRequest($this->fileId);
                        }
                    } else {
                        $response = $this->uploadFilesFromRequest(); // used by job uploader and upload app
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

    private function getPendingFiles()
    {
        $result = $this->fileGateway->findPending();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getCompletedFiles()
    {
        $result = $this->fileGateway->findCompleted();
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

    private function getChartData()
    {
        $result = $this->fileGateway->getChartData();
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
                "audio/x-dss",      // .dss
                "application/octet-stream", // .ds2
				"audio/aac", // .m4a
				"audio/alac", // .m4a
				"audio/x-m4a", //.m4a
				"video/mp4"

            );

            $authorName = ucwords($_POST["authorName"]);
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
            if ($speakerType != 1 && $speakerType != 2) {
                $speakerType = 1; // default
            }
            $comments = isset($_POST["comments"]) ? $_POST["comments"] : null; // Optional


            // check for permission for the given account id to upload files to or use session \\
//            $acc_id = $_SESSION["accID"];
            $acc_id = null;
            $post_acc_id = null;
            $stopUpload = false;
            $accName = $_SESSION["acc_name"];

            if (isset($_POST["set_acc_id"]) && !empty($_POST["set_acc_id"])) {
                $post_acc_id = $_POST["set_acc_id"];
                // curl to check if current user have insert permission to the acc_id passed via the request params
                if($_SESSION["role"] == 1)
                {
                    $acc_id = $post_acc_id;
                }else{
                    if(!$this->accessGateway->checkAccountAccessPermission($_POST["set_acc_id"])) {
//                if(!$this->checkForInsertPermission($_POST["set_acc_id"])) { // no permission
                        $uploadMsg[] = $this->formatFileResult("NA", "You don't have permission to upload to this account", true);
                        $stopUpload = true; // stop the upload
                    }else{
                        $acc_id = $post_acc_id;
                        $accName = Account::withID($acc_id, $this->db)->getAccName();
                    }
                }

            }
            else{ // use current session accID
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
                $jobGeneratedNumberForResponse = $jobPrefix .str_pad($nextJobNum, 7, "0", STR_PAD_LEFT);


                if (!in_array($file_real_mime_type, $allowedMimeTypes)) {
                    $uploadMsg[] = $this->formatFileResult($orig_filename, "upload failed (file type not allowed - $file_real_mime_type)", true);
                    unlink($file_tmp); // delete the tmp file.
                    continue;
                }
                //Max file upload size is 350MB. PHP is configured for max size of 128MB
                if ($file_size > 367001600 ) {
//                    $uploadMsg[] = "<li>File: $orig_filename - <span style='color:red;'>UPLOAD FAILED </span>(File size exceeds limit)</li>";
                    $uploadMsg[] = $this->formatFileResult($orig_filename, "upload failed file size exceeds limit", true);
                    continue;
                }

                $getID3 = new \getID3;
                $fileInfo = $getID3->analyze($file_tmp, filesize($file_tmp), $orig_filename);
                $org_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION)); // dss audio length check - working with DSS & DS2
                $file_duration = (float)(@$fileInfo['playtime_seconds']);
                $dur_received = isset($_POST["dur" . str_replace("file", "", $key)])?$_POST["dur" . str_replace("file", "", $key)]:0;
                if($file_duration == 0 && $dur_received != null && $dur_received != 0)
                {
                    $file_duration = $dur_received;
                }

                $fileRoundedDuration = 0;

                // SR Check
                if(isset($_POST["sr_enabled"]) && $_POST["sr_enabled"] === "true")
                {
                    // round minutes and deduct from user
                    $fileRoundedDuration = $this->common->roundUpToAnyIncludeCurrent($file_duration);
                    $sr = SR::withAccID($_SESSION["accID"], $this->db);
                    $remMin = $sr->getSrMinutesRemaining();

                    if(($remMin - $fileRoundedDuration) < 0)
                    {
                        $uploadMsg[] = $this->formatFileResult($orig_filename, "upload failed insufficient SR balance", true);
                        $nextFileID++;
                        $nextJobNum++;

                        continue; // skip current file
                    }

                    // balance OK deduct minutes from user
                    $sr->deductFromMinutesRemaining($fileRoundedDuration);
                    $sr->save();


                    // set correct statuses for files @see insertUploadedFileToDB()


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
                    $org_ext,
                    $fileRoundedDuration
                );

                $uplSuccess = move_uploaded_file($file_tmp, $file);
                if ($uplSuccess) {
                    $result = $this->fileGateway->insertUploadedFileToDB($fileDemos);
                    if ($result === true) {
//                        $uploadMsg[] = "<li>File: $orig_filename - <span style='color:green;'>UPLOAD SUCCESSFUL</span></li>";
                        $uploadMsg[] = $this->formatFileResult($orig_filename, "upload successful", false, $jobGeneratedNumberForResponse);
                        $newFilesAvailable = true;
                    }
/*                    else if ($result === 405)
                    {
                        $uploadMsg[] = $this->formatFileResult($orig_filename, "upload failed insufficient SR balance", true);
                    }*/
                    else {
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
                $this->mailer->sendEmail(15,false, $accName);
            }

            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode(array_values($uploadMsg), JSON_FORCE_OBJECT | JSON_PRETTY_PRINT);;
            return $response;

        } else {
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

    private function formatFileResult($fileName, $status, $error, $jobNo = 0)
    {
        if($jobNo)
        {
            return array(
                "job_no" => $jobNo,
                "file_name" => $fileName,
                "status" => $status,
                "error" => $error
            );
        }

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


    private function discardFile($id)
    {
        $result = $this->fileGateway->discardFile($id);
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
