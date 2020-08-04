<?php

namespace Src\TableGateways;

use PDO;
use PDOException;

require "filesFilter.php";
require_once "common.php";

class FileGateway
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

//        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//        $parts = parse_url($actual_link);
//        parse_str($parts['query'], $query);
//        echo $query['email'];

    public function findAll()
    {
        $filter = parseParams();

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

    public function find($id)
    {

        $statement = "
            SELECT 
                file_id, job_id, acc_id, file_type, org_ext, filename, tmp_name, orig_filename, file_author, file_work_type,file_comment,
                   file_speaker_type, file_date_dict, file_status,audio_length, last_audio_position, job_uploaded_by, text_downloaded_date,
                   job_document_html, job_document_rtf,                  
                   times_text_downloaded_date, job_transcribed_by, file_transcribed_date, typist_comments,isBillable,
                   user_field_1, user_field_2, user_field_3, 
                   billed
            
            FROM
                files
            WHERE file_id = ? and acc_id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id, $_SESSION['accID']));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
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
        } catch (PDOException $e) {
//            exit($e->getMessage());
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
                return false;
            }
        } catch (PDOException $e) {
//            exit($e->getMessage());
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
        $uploadedBy = $_SESSION['uEmail'];

        $file_status = 0;
        if($org_ext == "dss" || $org_ext == "ds2")
        {
            $file_status = 8; // Queued for conversion
        }

        $jobPrefix = $this->getAccountPrefix($acc_id);
        if(!$jobPrefix)
        {
//            die("couldn't get job prefix");
            return false;
        }
        $nextJobNum = $jobPrefix .str_pad($nextNum, 7, "0", STR_PAD_LEFT);


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
                if($file_status == 8){
                    // Queued for conversion - insert queue entry using curl
                     vtexCurlPost(getenv("BASE_LINK").'/api/v1/conversions/'.$file_id); // should be sufficient
                }

                $statement = "UPDATE accounts SET next_job_tally=next_job_tally+1 where acc_id = ".$acc_id;

                            try {
                                $statement = $this->db->prepare($statement);
                                $statement->execute();
//                                return $statement->rowCount();
                                return true;
                            } catch (PDOException $e) {
//                                die($e->getMessage());
                                return false;
                            }
            }else{
                return false;
            }
//            return $statement->rowCount();
        } catch (PDOException $e) {
//            die($e->getMessage());
            return false;
        }

    }

    // used in conversionCronJob
    public function directUpdateFileStatus($file_id ,$new_status){
        $statement = "UPDATE files
            SET file_status = ?
            WHERE file_id = ?
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                $new_status,
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

    public function delete($id)
    {
        $statement = "
            DELETE FROM files
            WHERE file_id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}