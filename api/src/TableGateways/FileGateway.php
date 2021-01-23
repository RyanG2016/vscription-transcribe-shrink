<?php

namespace Src\TableGateways;

use PDO;
use PDOException;
use Src\Enums\FILE_STATUS;
use Src\Enums\SRQ_STATUS;
use Src\Models\BaseModel;
use Src\Models\File;
use Src\Models\SRQueue;
use Src\TableGateways\conversionGateway;
use Src\TableGateways\accessGateway;
use Src\TableGateways\logger;
use Src\System\Mailer;

require "filesFilter.php";
require_once "common.php";

class FileGateway implements GatewayInterface
{

    private $db;
    private $conversionsGateway;
    private $accessGateway;
    private $logger;
    private $mailer;
    private $API_NAME;

    public function __construct($db)
    {
        $this->db = $db;
        $this->conversionsGateway = new conversionGateway($db);
        $this->accessGateway = new accessGateway($db);
        $this->mailer = new Mailer($db);

        $this->logger = new logger($db);
        $this->API_NAME = "Files";
    }

//        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//        $parts = parse_url($actual_link);
//        parse_str($parts['query'], $query);
//        echo $query['email'];

    public function findAll()
    {
        if(!isset($_SESSION['accID']))
        {return array("error"=> true, "msg" => "please set an account first");}
        $filter = parseFilesParams();

        $statement = "
            SELECT 
                file_id, job_id, acc_id, file_type, org_ext, filename, tmp_name, orig_filename, file_author,
                   file_work_type,file_comment, file_speaker_type, file_date_dict, file_status,audio_length,
                   last_audio_position, job_uploaded_by, job_upload_date, job_transcribed_by, text_downloaded_date,                  
                   times_text_downloaded_date, job_transcribed_by, file_transcribed_date, typist_comments,isBillable,
                   user_field_1, user_field_2, user_field_3,
                   billed,
                   (SELECT j_status_name 
                   From file_status_ref 
                   WHERE file_status_ref.j_status_id=files.file_status ORDER BY file_status LIMIT 1) as file_status_ref
            FROM
                files
            WHERE
                  acc_id = ? 
            AND
                  deleted = 0
        " . $filter . ";";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION['accID']));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            if(isset($_GET['dt'])){
                $json_data = array(
                        //            "draw"            => intval( $_REQUEST['draw'] ),
                        //            "recordsTotal"    => intval( 2 ),
                        //            "recordsFiltered" => intval( 1 ),
                    "data"            => $result
                );
                //        $response['body'] = json_encode($result);
                $result = $json_data;
            }
            return $result;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /* add ?tr to the request to move audio file to tmp directory for transcribe.php usage */
    /**
     * @param $id
     * @return false|string
     */
    public function find($id)
    {

        $statement = "
            SELECT 
                file_id, job_id, acc_id, file_type, org_ext, filename, tmp_name, orig_filename, file_author, file_work_type,file_comment,
                   file_speaker_type, file_date_dict, file_status,audio_length, last_audio_position, job_uploaded_by, text_downloaded_date,
                   job_document_html, job_document_rtf,                  
                   times_text_downloaded_date, job_transcribed_by, file_transcribed_date, typist_comments,isBillable,
                   user_field_1, user_field_2, user_field_3, 
                   billed, has_caption, captions
            
            FROM
                files
            WHERE file_id = ? and acc_id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id, $_SESSION['accID']));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            if(isset($_GET["tr"]))
            {
                // set job start timer
                $_SESSION['timerStart'] = date("Y-m-d H:i:s");
                // load tmp file for transcribe.php
                return $this->loadTmpFile($result);
            }else{
                return $result;
            }

        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * retrieves file total elapsed time of being typed
     * @param $id int file_id
     * @return int job elapsed_time
     */
    public function getElapsed($id)
    {

        $statement = "
            SELECT 
                file_id, elapsed_time
            
            FROM
                files
            WHERE file_id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
//            $result = $statement->fetch();

            if($statement->rowCount() > 0){
                return $statement->fetch()["elapsed_time"];
            }else{
                return false;
            }

        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }


    /**
     * calculates and sets file elapsed time depending on $_SESSION['timerStart'] and current time
     * @param $id int file_id
     * @return bool success
     */
    public function updateElapsed($id)
    {
        $prevElapse = $this->getElapsed($id)?$this->getElapsed($id):0;
//        $sessElapseStart = $_SESSION['timerStart'];
        $elapsedTime = strtotime(date("Y-m-d H:i:s")) -
            strtotime($_SESSION["timerStart"]) + $prevElapse;


        $statement = "
            UPDATE files
            set elapsed_time = :elapsed
            WHERE file_id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(
                array(
                    'id' => $id,
                    'elapsed' => $elapsedTime
                )
            );

            if ($statement->rowCount() > 0) {
                $this->logger->insertAuditLogEntry($this->API_NAME, "Elapsed Time Updated for file_id: " . $id);
                unset($_SESSION["timerStart"]);
                return true;
            } else {
                return false;
//                return generateApiResponse("Couldn't update file or no changes were found to update");
            }
        } catch (PDOException $e) {
            $this->logger->insertAuditLogEntry($this->API_NAME, "Error updating elapsed time for (" . $id.") ");
//            return generateApiResponse("Error occurred while updating file, consult system admin", true);
            return false;
//            exit($e->getMessage());
        }

    }

    public function loadTmpFile($result) {
        $row = $result[0];
        $tmpName = $row['tmp_name'];
        /** checking first if there's already a tmp file for this job on the server */
        if($tmpName != null && $tmpName != "")
        {
            // Temp file name already exists in the db .. check if it's still on the server workingTmp directory
            if($this->checkIfTmpFileExists($tmpName)){

                // pass the old file and exit this case
                $jobDetails = array(
                    "file_id" => $row['file_id'],
                    "job_id" => $row['job_id'],
                    "file_author" => $row['file_author'],
                    "origFilename" => $row['filename'],
                    "suspendedText" => html_entity_decode($row['job_document_html'], ENT_QUOTES),
                    "tmp_name" => $tmpName,  /** REUSING OLD TMP FILE */
                    "file_date_dict" => $row['file_date_dict'],
                    "file_work_type" => $row['file_work_type'],
                    "last_audio_position" => $row['last_audio_position'],
                    "file_status" => $row['file_status'],
                    "file_speaker_type" => $row['file_speaker_type'],
                    "typist_comments" => $row['typist_comments'],
                    "file_comment" => $row['file_comment'],
                    "user_field_1" => $row['user_field_1'],
                    "user_field_2" => $row['user_field_2'],
                    "has_caption" => $row['has_caption'],
                    "captions" => $row['captions'],
                    "user_field_3" => $row['user_field_3']
                );


                if($row['file_status'] == 2 || $row['file_status'] == 0) // if the job was suspended/awaiting update it to being typed status = 1
                {
                    $this->directUpdateFileStatus($row['file_id'], 1, $row["filename"]);
                }

                $this->logger->insertAuditLogEntry($this->API_NAME, "Loading file " . $row["file_id"] . " into player");

                return json_encode($jobDetails);
            }
        }

        /** IF NO TMP FILE AVAILABLE FOR THE JOB CREATE A NEW ONE AND SAVE IT TO DB RECORD */

        $randFileName = random_filename(".mp3");

        // These paths need to be relative to the PHP file making the call....

        $path = __DIR__ . "/../../../uploads/". $row['filename'];
//        $type = pathinfo($path, PATHINFO_EXTENSION);

        if(copy(__DIR__ . '/../../../uploads/' . $row['filename'],
                __DIR__.'/../../../transcribe/workingTemp/' . $randFileName )) {

            // -> file is copied successfully to tmp -> set tmp value to db

            // check if it has caption file and copy it too under the same rand name
//            if($row["has_caption"])
//            {
//                copy(__DIR__ . '/../../../uploads/' . pathinfo($row['filename'], PATHINFO_FILENAME) . ".vtt",
//                    __DIR__.'/../../../transcribe/workingTemp/' .  pathinfo($randFileName, PATHINFO_FILENAME) . ".vtt");
//            }

            $jobDetails = array(
                "file_id" => $row['file_id'],
                "job_id" => $row['job_id'],
                "file_author" => $row['file_author'],
                "origFilename" => $row['filename'],
                "suspendedText" => html_entity_decode($row['job_document_html'], ENT_QUOTES),
                "tmp_name" => $randFileName,  /** REUSING OLD TMP FILE */
                "file_date_dict" => $row['file_date_dict'],
                "file_work_type" => $row['file_work_type'],
                "last_audio_position" => $row['last_audio_position'],
                "file_status" => $row['file_status'],
                "file_speaker_type" => $row['file_speaker_type'],
                "typist_comments" => $row['typist_comments'],
                "file_comment" => $row['file_comment'],
                "user_field_1" => $row['user_field_1'],
                "user_field_2" => $row['user_field_2'],
                "has_caption" => $row['has_caption'],
                "captions" => $row['captions'],
                "user_field_3" => $row['user_field_3']
            );

            // add audit log entry for job file loaded
            $this->logger->insertAuditLogEntry($this->API_NAME, "Loading file " . $row["file_id"] . " into player");

            $statusToUpdate = $row['file_status'];
            // update status
            if($row['file_status'] == 2 || $row['file_status'] == 0) // if the job was suspended/awaiting update it to being typed status = 1
            {
                $statusToUpdate = 1;
                $this->directUpdateFileStatus($row["file_id"], 1, $row["filename"]);
            }

            $statement = "UPDATE FILES SET file_status=?, tmp_name = ? WHERE file_id=?";

            try {
                $statement = $this->db->prepare($statement);
                $statement->execute(
                    array(
                        $statusToUpdate,
                        $randFileName,
                        $row["file_id"]
                    )
                );
//                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                exit($e->getMessage());
            }

            // return the tmp_name & job details back to transcribe
            return json_encode($jobDetails);

        }
        else {
            //echo "Error moving file" . $randFileName . " to working directory..";
//            echo false;

            return false;
        }
    }

    public function checkIfTmpFileExists($tmpName)
    {
        $dir = __DIR__ . "/../../../transcribe/workingTemp/"; // working tmp directory
        return file_exists($dir.$tmpName);
    }


    public function getNextJobNumbers($accID) {

        $statement = "SELECT (SELECT AUTO_INCREMENT FROM information_schema.TABLES 
						WHERE TABLE_SCHEMA = 'vtexvsi_transcribe' AND TABLE_NAME = 'files') AS next_job_id, 
						(SELECT next_job_tally AS num2 FROM accounts WHERE acc_id = ".$accID.") AS next_job_num
						FROM DUAL";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
            if($statement->rowCount() == 1) {
                $result = $statement->fetch();
                $numbers = array(
                    "next_job_id" => strval($result['next_job_id']),
                    "next_job_num" => strval($result['next_job_num'])
                );
                return json_encode($numbers);

            }else{
                $numbers = array(
                    "next_job_id" => "1",
                    "next_job_num" => "1"
                );
                return json_encode($numbers);
            }
        } catch (PDOException) {
            return false;
        }

    }

    public function getAccountPrefix($acc_id) {

        $statement = "select job_prefix from accounts where acc_id = " . $acc_id;

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
            if($statement->rowCount() == 1) {
                $result = $statement->fetch();
                return $result['job_prefix'];

            }else{
                echo "code 5\n job prefix isn't available";
                return false;
            }
        } catch (PDOException $e) {
//            exit($e->getMessage());
            echo "code 4\n " . $e->getMessage();
            return false;
        }

    }

    public function insertUploadedFileToDB($input) {
//        $nextFileID = $input[0];
        $nextNum = $input[1];
        $authorName = $input[2];
        $jobType = $input[3];
        $dictDate = $input[4];
        $speakerType = $input[5];
        $comments = $input[6];
        $orig_filename = $input[7];
        $file_name = $input[8];
        $file_duration = $input[9];
        $user_field_1 = $input[10];
        $user_field_2 = $input[11];
        $user_field_3 = $input[12];
        $acc_id = $input[13];
        $org_ext = $input[14];
        $fileRoundedDuration = $input[15];
        $uploadedBy = $_SESSION['uEmail'];

        $file_status = 0;
        if($org_ext == "ds2")
        {
            $file_status = 8; // Queued for conversion
        }

        if(isset($_POST["sr_enabled"]) && $_POST["sr_enabled"] === "true")
        {
            if($file_status == 8)
            {
                $file_status = FILE_STATUS::QUEUED_FOR_SR_CONVERSION;
            }
            else{
                $file_status = FILE_STATUS::QUEUED_FOR_RECOGNITION;
            }
        }

        echo "if you see this you have correctly pulled the debug version of upload";

        $jobPrefix = $this->getAccountPrefix($acc_id);
        if(!$jobPrefix)
        {
//            die("couldn't get job prefix");
            echo "code 4\n couldn't generate job prefix";
            return false;
        }
        $nextJobNum = $jobPrefix .str_pad($nextNum, 7, "0", STR_PAD_LEFT);
        echo "trying to insert job_id: " . $nextJobNum . "\n";

        $statement = "INSERT
                        INTO 
                            files 
                            (
                             job_id, 
                             file_author, 
                             file_work_type, 
                             file_date_dict, 
                             file_speaker_type, 
                             file_comment, 
                             job_uploaded_by, 
                             filename, 
                             orig_filename, 
                             acc_id, 
                             audio_length,
                             user_field_1,
                             user_field_2,
                             user_field_3,
                             org_ext,
                             file_status
                             ) 
                         VALUES 
                                (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?)";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(
                array(
                    $nextJobNum,
                    $authorName,
                    $jobType,
                    $dictDate,
                    $speakerType,
                    $comments,
                    $uploadedBy,
                    $file_name,
                    $orig_filename,
                    $acc_id,
                    $file_duration,
                    $user_field_1,
                    $user_field_2,
                    $user_field_3,
                    $org_ext,
                    $file_status
                )
            );

            if($statement->rowCount()){
                $file_id = $this->db->lastInsertId();

                // Add to conversion queue
                if($file_status == FILE_STATUS::QUEUED_FOR_CONVERSION || $file_status == FILE_STATUS::QUEUED_FOR_SR_CONVERSION){
                    // Queued for conversion - insert queue entry using curl
//                     vtexCurlPost(getenv("BASE_LINK").'/api/v1/conversions/'.$file_id); // should be sufficient
                    $this->conversionsGateway->insertNewConversion($file_id);
                }


                // Add to recognition queue
                if($file_status == FILE_STATUS::QUEUED_FOR_SR_CONVERSION || $file_status == FILE_STATUS::QUEUED_FOR_RECOGNITION)
                {
                    $srq = new SRQueue($file_id,
                    $file_status == FILE_STATUS::QUEUED_FOR_SR_CONVERSION ? SRQ_STATUS::WAITING_SWITCH_CONVERT : SRQ_STATUS::QUEUED,
                    0,
                    null,
                    $fileRoundedDuration,
                    null,
                    0,
                    null,
                    null,
                    $this->db);

                    $srq->save();
                }


                $statement = "UPDATE accounts SET next_job_tally=next_job_tally+1 where acc_id = ".$acc_id;

                            try {
                                $statement = $this->db->prepare($statement);
                                $statement->execute();
                                $this->logger->insertAuditLogEntry($this->API_NAME, "File ". $orig_filename ." uploaded");
//                                return $statement->rowCount();
                                return true;
                            } catch (PDOException $e) {
//                                die($e->getMessage());
                                echo "code 2\n".$e->getMessage();
                                return false;
                            }
            }else{
                echo "couldn't execute insert statement";
                return false;
            }
//            return $statement->rowCount();
        } catch (PDOException $e) {
//            die($e->getMessage());
            echo "code 3\n".$e->getMessage();
            return false;
        }

    }

    // used in conversionCronJob && @this to update status to being transcribe when file is loaded in transcribe.php
    public function directUpdateFileStatus($file_id ,$new_status, $newName){
        $statement = "UPDATE files
            SET file_status = ?, filename = ?
            WHERE file_id = ?
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                $new_status,
                $newName,
                $file_id
            ));

            if ($statement->rowCount()) {
                return true;
//                return $this->formatResult("Convert Record Updated", false);
            } else {
                return false;
//                return $this->formatResult("Failed to update convert record", true);
            }
//            return $statement->rowCount();
        } catch (\PDOException $e) {
            return false;
//            return $this->formatResult("Failed to update convert record (2)", true);
        }
    }

    public function update($id)
    {
        // -- CHECK FOR PERMISSION TO UPDATE ACCOUNT //
        $acc_id = null;
        $post_acc_id = null;
        $role = null;

        if (isset($_POST["set_acc_id"]) && !empty($_POST["set_acc_id"])) {
            $post_acc_id = $_POST["set_acc_id"];
            // curl to check if current user have insert permission to the acc_id passed via the request params
//            $certain_role = 0;
            (isset($_POST["set_role"]) && $_POST["set_role"] != 0) ? $certain_role = $_POST["set_role"]:$certain_role = 0;
            $role = $this->accessGateway->checkForUpdatePermission($_POST["set_acc_id"], $certain_role);
            if($role == 0) { // no permission
                return generateApiResponse("You don't have permission to update this account", true);
            }else{
                $acc_id = $post_acc_id;
            }
        } else{ // use current session accID
            if(isset($_SESSION["accID"]))
            {
                $acc_id = $_SESSION["accID"];
                if($acc_id == 0) {
                    return generateApiResponse("Account not set", true);
                }

                // role check
                if(isset($_POST["set_role"]) && $_POST["set_role"] != 0){
                    $certain_role = $_POST["set_role"];
                    $role = $this->accessGateway->checkForUpdatePermission($acc_id, $certain_role);
                    if($role == 0) { // no permission
                        return generateApiResponse("You don't have permission to update this account", true);
                    }
                } else{
                    if(isset($_SESSION["role"]))
                    {
                        $role = $_SESSION["role"];
                        if($role == 0) {
                            return generateApiResponse("Role not set", true);
                        }
                    } else{
                        return generateApiResponse("Role not set", true);
                    }
                }

            } else{
                return generateApiResponse("Account not set", true);
            }
        }
        // =====================================
        // ---- Update file
        // =====================================


        // filter params depending on current role
        /*switch ($role){
            case 1:
                // system admin allow all modifications
            case 2:
                // client admin allow some modifications
            case 3:
                // typist allow minimal modifications
        }*/

        $currentFile = $this->find($id);
        $fields = parseFileUpdateParams($role, $currentFile);
        $currentFile = $currentFile[0];

        $statement = "
            UPDATE files
            $fields
            WHERE file_id = :id
            AND acc_id = $acc_id
            ;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(
                array('id' => $id)
            );
//            return $statement->rowCount();
            if ($statement->rowCount() > 0) {
                $this->logger->insertAuditLogEntry($this->API_NAME, "File Updated: " . $id);
                if(isset($_POST["file_status"]))
                {
                    $file_status = $_POST["file_status"];
                    if ($file_status == 5 || $file_status == 3|| $file_status == 11) {
                        $this->mailer->sendEmail(10, false);
                        $this->deleteTmpFile($id, $currentFile["tmp_name"]);
                    }

                    // update elapsed time
                    if($file_status == 2 || $file_status == 3 || $file_status == 4 || $file_status == 5 || $file_status == 11){
                        $this->updateElapsed($id);
                    }
                }
                return generateApiResponse("File $id updated");
            } else {
                return generateApiResponse("Couldn't update file or no changes were found to update");
            }
        } catch (PDOException $e) {
            $this->logger->insertAuditLogEntry($this->API_NAME, "Error updating file: " . $id);
            return generateApiResponse("Error occurred while updating file, consult system admin", true);
//            exit($e->getMessage());
        }

    }

    public function deleteTmpFile($fileID, $tmpName)
    {
        $statement = "
            UPDATE FILES 
            SET tmp_name=null
            WHERE file_id= :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $fileID));

            $dir = realpath(__DIR__ . "/../../../transcribe/workingTemp/"); // working tmp directory
            $file = $dir . "\\" . $tmpName;
            $vttFile = $dir . "\\" . pathinfo($tmpName, PATHINFO_FILENAME) . ".vtt";
            if(file_exists($file)){
                sleep(0.4);
                unlink($file);
            }
            if(file_exists($vttFile))
            {
                unlink($vttFile);
            }

            return $statement->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }

//    public function updateUser($id)
//    {
//        parse_str(file_get_contents('php://input'), $put);
//
//        // Required Fields
//        /*if (
//            !isset($put["first_name"]) ||
//            !isset($put["last_name"]) ||
//            !isset($put["email"]) ||

//            !isset($put["newsletter"]) ||
//            !isset($put["enabled"])
//        ) {
//            return $this->errorOccurredResponse("Invalid Input, required fields missing (31)");
//        }*/
//
//        // Sql Injection Check
//        if(!sqlInjectionCheckPassed($put))
//        {
//            return $this->errorOccurredResponse("Invalid Input (3505)");
//        }
//
//        // Parse post request params/fields
//        $valPairs = "";
//        $valsArray = array();
//
//        $i = 0;
//        $len = count($put);
//
//        foreach ($put as $key => $value) {
//
//            // setting all empty params to 0
//            if (empty($input)) {
//                $input = 0;
//            }
//
//            $valPairs .= "`$key` = ";
//            array_push($valsArray, $value);
//            $valPairs .= "?";
//
//            if ($i != $len - 1) {
////                 not last item add comma
//                $valPairs .= ", ";
//            }
//
//            $i++;
//        }
//
//        if(isset($put['state'])){
//
//            $valPairs .= ", `state_id` = ";
//            array_push($valsArray, null);
//            $valPairs .= "?";
//        }
//        else if(isset($put['state_id'])){
//            $valPairs .= ", `state` = ";
//            array_push($valsArray, null);
//            $valPairs .= "?";
//        }
//
//        array_push($valsArray, $id);
//
//
//        // update DB //
//        $statement = "UPDATE
//                        users
//                        SET
//                             " . $valPairs . "
//                        WHERE
//                            id = ?";
//
//        try {
//            $statement = $this->db->prepare($statement);
//            $statement->execute($valsArray);
//
//            if ($statement->rowCount() > 0) {
//                $this->logger->insertAuditLogEntry($this->API_NAME, "Updated User: " . $id);
//                return $this->oKResponse($id, "User Updated");
//            } else {
//                return $this->errorOccurredResponse("Couldn't update user or no changes were found to update");
////                return $this->errorOccurredResponse("Debug " . print_r($statement->errorInfo()) . "\n <br>"
////                . $statement->queryString);
//            }
//
//        } catch (PDOException $e) {
////            die($e->getMessage());
//            return false;
//        }
//    }

    public function delete($id): int
    {
        $statement = "
            DELETE FROM files
            WHERE file_id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (PDOException) {
            return 0;
        }
    }

    public function insertModel(BaseModel $model): int
    {
        // TODO: Implement insertModel() method.
    }

    public function updateModel(BaseModel|File $model): int
    {
        $statement = "
            UPDATE files
            SET
                job_id = :job_id,
                acc_id = :acc_id,
                file_type = :file_type,
                org_ext = :org_ext,
                filename = :filename,
                tmp_name = :tmp_name,
                orig_filename = :orig_filename,
                job_document_html = :job_document_html,
                job_document_rtf = :job_document_rtf,
                file_tag = :file_tag,
                file_author = :file_author,
                file_work_type = :file_work_type,
                file_comment = :file_comment,
                file_speaker_type = :file_speaker_type,
                file_date_dict = :file_date_dict,
                file_status = :file_status,
                audio_length = :audio_length,
                last_audio_position = :last_audio_position,
                job_upload_date = :job_upload_date,
                job_uploaded_by = :job_uploaded_by,
                text_downloaded_date = :text_downloaded_date,
                times_text_downloaded_date = :times_text_downloaded_date,
                job_transcribed_by = :job_transcribed_by,
                file_transcribed_date = :file_transcribed_date,
                elapsed_time = :elapsed_time,
                typist_comments = :typist_comments,
                isBillable = :isBillable,
                billed = :billed,
                typ_billed = :typ_billed,
                user_field_1 = :user_field_1,
                user_field_2 = :user_field_2,
                user_field_3 = :user_field_3,
                deleted = :deleted
            WHERE
                file_id = :file_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'file_id' => $model->getFileId() ,
                'job_id' => $model->getJobId()  ,
                'acc_id' => $model->getAccId()  ,
                'file_type' => $model->getFileType()  ,
                'org_ext' => $model->getOrgExt()  ,
                'has_caption' => $model->getHasCaption()  ,
                'filename' => $model->getFilename()  ,
                'tmp_name' => $model->getTmpName()  ,
                'orig_filename' => $model->getOrigFilename()  ,
                'job_document_html' => $model->getJobDocumentHtml()  ,
                'job_document_rtf' => $model->getJobDocumentRtf()  ,
                'file_tag' => $model->getFileTag() ,
                'file_author' => $model->getFileAuthor()  ,
                'file_work_type' => $model->getFileWorkType()  ,
                'file_comment' => $model->getFileComment()  ,
                'file_speaker_type' => $model->getFileSpeakerType()  ,
                'file_date_dict' => $model->getFileDateDict()  ,
                'file_status' => $model->getFileStatus()  ,
                'audio_length' => $model->getAudioLength()  ,
                'last_audio_position' => $model->getLastAudioPosition()  ,
                'job_upload_date' => $model->getJobUploadDate()  ,
                'job_uploaded_by' => $model->getJobUploadedBy()  ,
                'text_downloaded_date' => $model->getTextDownloadedDate()  ,
                'times_text_downloaded_date' => $model->getTimesTextDownloadedDate()  ,
                'job_transcribed_by' => $model->getJobTranscribedBy()  ,
                'file_transcribed_date' => $model->getFileTranscribedDate()  ,
                'elapsed_time' => $model-> getElapsedTime() ,
                'typist_comments' => $model->getTypistComments()  ,
                'isBillable' => $model->getIsBillable()  ,
                'billed' => $model->getBilled()  ,
                'typ_billed' => $model->getTypBilled()  ,
                'user_field_1' => $model->getUserField1()  ,
                'user_field_2' => $model->getUserField2()  ,
                'user_field_3' => $model->getUserField3()  ,
                'deleted' => $model->getDeleted()
            ));
            return $statement->rowCount();
        } catch (\PDOException) {
            return 0;
        }
    }

    public function updateFileHTML(BaseModel|File $model, ?int $optional_has_caption = null, ?string $captions = null): int
    {
        $statement = "
            UPDATE files
            SET
                job_document_html = :job_document_html
            WHERE
                file_id = :file_id;
        ";

        if($optional_has_caption != null)
        {
            $statement = "
            UPDATE files
            SET
                job_document_html = :job_document_html,
                has_caption = :has_caption,
                captions = :captions
            WHERE
                file_id = :file_id;
        ";
        }

        try {
            $statement = $this->db->prepare($statement);
            if($optional_has_caption == null)
            {
                $statement->execute(array(
                    'job_document_html' => $model->getJobDocumentHtml(),
                    'file_id' => $model->getFileId()
                ));
            }else{
                $statement->execute(array(
                    'job_document_html' => $model->getJobDocumentHtml()  ,
                    'file_id' => $model->getFileId()  ,
                    'captions' => $captions  ,
                    'has_caption' => $optional_has_caption
                ));
            }
            return $statement->rowCount();
        } catch (\PDOException) {
            return 0;
        }
    }

    public function deleteModel(int $id): int
    {
        return $this->delete($id);
    }

    public function findModel($id): array|null
    {
        // TODO: Implement findModel() method.
    }


    /**
     * Get File by ID with minimal data
     * * used by revai cron job
     * @param $id int fileID
     * @return array|null
     */
    public function findAltModel($id): array|null
    {

        $statement = "
            SELECT 
                file_id, job_id, acc_id, file_type, org_ext, filename, tmp_name, orig_filename, file_author, file_work_type,file_comment,
                   file_speaker_type, file_date_dict, file_status, audio_length,
                  
                   isBillable, billed
            
            FROM
                files
            WHERE file_id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            if($statement->rowCount())
            {
                return $statement->fetch(PDO::FETCH_ASSOC);
            }else{
                return null;
            }
        } catch (PDOException $e) {
            return null;
        }
    }



    public function findAllModel($page = 1): array|null
    {
        // TODO: Implement findAllModel() method.
    }


}