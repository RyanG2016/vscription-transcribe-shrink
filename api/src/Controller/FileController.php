<?php
namespace Src\Controller;

use PHPMailer\PHPMailer\Exception;
use Src\TableGateways\FileGateway;

require_once('../../../../audioParser/getid3/getid3.php');
class FileController {

    private $db;
    private $requestMethod;
    private $fileId;

    private $fileGateway;

    public function __construct($db, $requestMethod, $fileId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->fileId = $fileId;

        $this->fileGateway = new FileGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if(isset($_GET["cancel"])){
                    $response = $this->cancelUpload();
                }else{
                    if ($this->fileId) {
                        $response = $this->getFile($this->fileId);
                    } else {
                        $response = $this->getAllFiles();
                    }
                }
                break;
            case 'POST':
                if(isset($_POST["cancel"])) {
                    $response = $this->cancelUpload();
                }else{
                    $response = $this->uploadFilesFromRequest();
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
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateFile($input)) {
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
                "application/octet-stream" // .ds2

            );
            $nextJobNumbers = $this->fileGateway->getNextJobNumbers();
            if($nextJobNumbers == false){
                return $this->errorOccurredResponse("while generating job number.");
            }
            $nextJobNumbers = json_decode($nextJobNumbers, true);
            $nextFileID = $nextJobNumbers["next_job_id"];
            $nextJobNum = $nextJobNumbers["next_job_num"];
            $authorName = $_POST["authorName"];
            $jobType = $_POST["jobType"];
            $dictDate = $_POST["dictDate"];
            $dictDate = $this->validateAndReturnDate($dictDate);
            if(!$dictDate)
            {
                return $this->errorOccurredResponse("invalid date format.");
            }

            // 1 -> single speaker, 2 -> multiple speaker, otherwise defaults to 1
            $speakerType = $_POST["speakerType"];
            if(!is_numeric($speakerType))
            {
                return $this->errorOccurredResponse("invalid speaker type.");
            }else{
                if($speakerType != 1 && $speakerType != 2){
                    $speakerType = 1; // default
                }
            }
            $comments = isset($_POST["comments"])?$_POST["comments"]:null; // Optional

            foreach ($_FILES as $key=>$fileItem) {
                if($fileItem["error"] != 0)
                {
                    $uploadMsg[] = $this->formatFileResult($key, "File read error", true);
                    continue;
                }
                $file_name = $fileItem['name'];
                $file_tmp = $fileItem['tmp_name'];
                $file_size = $fileItem['size'];
                $file_real_mime_type = mime_content_type($file_tmp);

                // enumerating file names
                $enumName = "F".$nextFileID."_UM".$nextJobNum."_".str_replace(" ","_", $file_name);
                $orig_filename = $file_name;
                $file_name = $enumName;
                $file = $path . $file_name;


                if (!in_array($file_real_mime_type, $allowedMimeTypes)) {
                    $uploadMsg[] = $this->formatFileResult($orig_filename, "upload failed (file type not allowed)", true);
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
                $file_duration = (int)ceil(@$fileInfo['playtime_seconds']);

                //Building demographic array for DB insert function call
                $fileDemos = array($nextFileID, $nextJobNum, $authorName, $jobType, $dictDate, $speakerType, $comments,$orig_filename, $file_name, $file_duration);

                $uplSuccess = move_uploaded_file($file_tmp, $file);
                if ($uplSuccess) {
                    $result = $this->fileGateway->insertUploadedFileToDB($fileDemos);
                    if ($result) {
//                        $uploadMsg[] = "<li>File: $orig_filename - <span style='color:green;'>UPLOAD SUCCESSFUL</span></li>";
                        $uploadMsg[] = $this->formatFileResult($orig_filename, "upload successful", false);
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

            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode(array_values($uploadMsg), JSON_FORCE_OBJECT | JSON_PRETTY_PRINT);;
            return $response;

        }else{
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

    private function formatFileResult($fileName, $status, $error){
        return array(
            "file_name" => $fileName,
            "status" => $status,
            "error" => $error
            );
    }

    private function updateFileFromRequest($id)
    {
        $result = $this->fileGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateFile($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->fileGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteFile($id)
    {
        $result = $this->fileGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->fileGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateFile($input)
    {
        if (! isset($input['firstname'])) {
            return false;
        }
        if (! isset($input['lastname'])) {
            return false;
        }
        return true;
    }

    private function validateAndReturnDate($date)
    {
        // (accepted format: yyyy-mm-dd)
        $dateArr = explode("-",$date);
        if(sizeof($dateArr) == 3 && checkdate($dateArr[1], $dateArr[2], $dateArr[0])) {
            return $dateArr[0]."-".$dateArr[1]."-".$dateArr[2];
        }else{
            return false;
        }

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
