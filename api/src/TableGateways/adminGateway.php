<?php

namespace Src\TableGateways;

//use Src\TableGateways\AccountGateway;
//require "testsFilter.php";

class adminGateway
{

    private $db = null;
    private $accountGateway;
    private $filesGateway;
    private $srQueueGateway;

    public function __construct($db)
    {
        $this->db = $db;
        $this->accountGateway = new AccountGateway($db);
        $this->filesGateway = new FileGateway($db);
        $this->srQueueGateway = new srQueueGateway($db);
    }

//        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//        $parts = parse_url($actual_link);
//        parse_str($parts['query'], $query);
//        echo $query['email'];

    public function findAll()
    {
        $filter = parseParams(true);

        $statement = "
            SELECT 
                test_id, job_id, acc_id, test_type, original_audio_type, testname, tmp_name, orig_testname, test_author, test_work_type,test_comment,
                   test_speaker_type, test_date_dict, test_status,audio_length, last_audio_position, job_uploaded_by, text_downloaded_date,                  
                   times_text_downloaded_date, job_transcribed_by, test_transcribed_date, typist_comments,isBillable, billed
            FROM
                tests
        " . $filter . ";";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {
        $filter = parseParams();

        $statement = "
            SELECT 
                test_id, job_id, acc_id, test_type, original_audio_type, testname, tmp_name, orig_testname, test_author, test_work_type,test_comment,
                   test_speaker_type, test_date_dict, test_status,audio_length, last_audio_position, job_uploaded_by, text_downloaded_date,
                   job_document_html, job_document_rtf,                  
                   times_text_downloaded_date, job_transcribed_by, test_transcribed_date, typist_comments,isBillable, billed
            
            FROM
                tests
            WHERE test_id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }


   /* public function insert(array $input)
    {
        $statement = "
            INSERT INTO tests 
                (job_id, lastname, firstparent_id, secondparent_id)
            VALUES
                (:firstname, :lastname, :firstparent_id, :secondparent_id);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'firstname' => $input['firstname'],
                'lastname' => $input['lastname'],
                'firstparent_id' => $input['firstparent_id'] ?? null,
                'secondparent_id' => $input['secondparent_id'] ?? null,
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }*/

    /*public function update($id, array $input)
    {
        $statement = "
            UPDATE person
            SET 
                firstname = :firstname,
                lastname  = :lastname,
                firstparent_id = :firstparent_id,
                secondparent_id = :secondparent_id
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int)$id,
                'firstname' => $input['firstname'],
                'lastname' => $input['lastname'],
                'firstparent_id' => $input['firstparent_id'] ?? null,
                'secondparent_id' => $input['secondparent_id'] ?? null,
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }*/

    public function getStatistics()
    {
        // get org count
        $accounts = $this->accountGateway->getCount();

        // get files count
        $files = $this->filesGateway->getCount();

        // get files chart
        $filesChart = $this->filesGateway->getChartData();

        // get sr queue chart
        $srChart = $this->srQueueGateway->getChartData();

        // get access of default account to all orgs count
        $sysOrgAccessCount = $this->accountGateway->getSysAdminAccessCount();

        return array(
            "org_count" => $accounts,
            "sys_org_access_count" => $sysOrgAccessCount,
            "files_count" => $files,
            "files_chart" => $filesChart,
            "sr_chart" => $srChart,
        );

    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM tests
            WHERE test_id = :id;
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