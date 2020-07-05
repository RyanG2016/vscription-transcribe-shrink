<?php

namespace Src\TableGateways;

require "filesFilter.php";

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
                file_id, job_id, acc_id, file_type, original_audio_type, filename, tmp_name, orig_filename, file_author,
                   file_work_type,file_comment, file_speaker_type, file_date_dict, file_status,audio_length,
                   last_audio_position, job_uploaded_by, job_upload_date, job_transcribed_by, text_downloaded_date,                  
                   times_text_downloaded_date, job_transcribed_by, file_transcribed_date, typist_comments,isBillable,
                   billed, 
                   (SELECT j_status_name 
                   From file_status_ref 
                   WHERE file_status_ref.j_status_id=files.file_status ORDER BY file_status LIMIT 1) as file_status_ref
            FROM
                files
            WHERE
                  acc_id = ?
        " . $filter . ";";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION['accID']));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
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
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {

        $statement = "
            SELECT 
                file_id, job_id, acc_id, file_type, original_audio_type, filename, tmp_name, orig_filename, file_author, file_work_type,file_comment,
                   file_speaker_type, file_date_dict, file_status,audio_length, last_audio_position, job_uploaded_by, text_downloaded_date,
                   job_document_html, job_document_rtf,                  
                   times_text_downloaded_date, job_transcribed_by, file_transcribed_date, typist_comments,isBillable, billed
            
            FROM
                files
            WHERE file_id = ? and acc_id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id, $_SESSION['accID']));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }


    public function getNextJobNumbers() {

        $statement = "SELECT (SELECT AUTO_INCREMENT FROM information_schema.TABLES 
						WHERE TABLE_SCHEMA = 'vtexvsi_transcribe' AND TABLE_NAME = 'files') AS next_job_id, 
						(SELECT next_job_tally AS num2 FROM accounts WHERE acc_id = ".$_SESSION['accID'].") AS next_job_num
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
        } catch (\PDOException $e) {
//            exit($e->getMessage());
            return false;
        }

    }

    public function insertUploadedFileToDB($input) {
        $nextFileID = $input[0];
        $nextNum = $input[1];
        $authorName = $input[2];
        $jobType = $input[3];
        $dictDate = $input[4];
        $speakerType = $input[5];
        $comments = $input[6];
        $orig_filename = $input[7];
        $file_name = $input[8];
        $file_duration = $input[9];
        $uploadedBy = $_SESSION['uEmail'];

        //This is a dirty way to change the job prefix for testing. We will ultimately pull this from
        // the database and a new field has already been added and will be included in the production push
        if ($_SESSION['accID'] == 1) {
            $jobPrefix = "UM-";
        } else if ($_SESSION['accID'] == 2) {
            $jobPrefix = "VT-";
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
                             audio_length
                             ) 
                         VALUES 
                                (?,?,?,?,?,?,?,?,?,?,?)";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($nextJobNum, $authorName, $jobType, $dictDate,
                $speakerType, $comments, $uploadedBy, $file_name, $orig_filename, $_SESSION["accID"], $file_duration));

            if($statement->rowCount()){
                $statement = "UPDATE accounts SET next_job_tally=next_job_tally+1 where acc_id = ".$_SESSION["accID"];

                            try {
                                $statement = $this->db->prepare($statement);
                                $statement->execute();
//                                return $statement->rowCount();
                                return true;
                            } catch (\PDOException $e) {
//                                exit($e->getMessage());
                                return false;
                            }
            }else{
                return false;
            }
//            return $statement->rowCount();
        } catch (\PDOException $e) {
//            exit($e->getMessage());
            return false;
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
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}