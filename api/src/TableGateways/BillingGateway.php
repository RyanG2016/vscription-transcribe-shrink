<?php

namespace Src\TableGateways;

//require "testsFilter.php";

use Src\Helpers\common;
use Src\Models\Account;
use Src\Models\BaseModel;
use Src\Models\File;

class BillingGateway implements GatewayInterface
{

    private $db = null;
    private $logger;
    private $API_NAME = "Billing";
//    private $common;
    private $limit = 10;

    public function __construct($db)
    {
        $this->db = $db;
        $this->logger = new logger($db);
//        $this->common = new common();
    }

//        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//        $parts = parse_url($actual_link);
//        parse_str($parts['query'], $query);
//        echo $query['email'];

  /*  public function findAll()
    {

        $statement = "
            SELECT 
                test_id, job_id, acc_id, test_type, original_audio_type, testname, tmp_name, orig_testname, test_author, test_work_type,test_comment,
                   test_speaker_type, test_date_dict, test_status,audio_length, last_audio_position, job_uploaded_by, text_downloaded_date,                  
                   times_text_downloaded_date, job_transcribed_by, test_transcribed_date, typist_comments,isBillable, billed
            FROM
                tests
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }*/

    public function find($orgID)
    {
        $returnBilled = $_GET["return_billed"] ?? 0;
        if($returnBilled == 1)
        {
            $returnBilled = "0 or billed = 1";
        }

        $org = Account::withID($orgID,$this->db);

        $statement = "
            SELECT 
                file_id,
                job_id, 
                file_author, 
                file_work_type, 
                file_date_dict, 
                audio_length, 
                job_upload_date,
                file_transcribed_date,
                file_comment
            FROM 
                files
            WHERE 
                file_status  = '3' AND 
                isBillable = '1' AND
                (billed = $returnBilled) AND 
                acc_id = ? AND
                file_transcribed_date BETWEEN ? AND ?
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($orgID, $_GET["start_date"], $_GET["end_date"]));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

            if(isset($_GET['dt'])){
                $json_data = array(
                    "organization" => $org->getAccName(),
                    "billrate1" => $org->getBillRate1(),
                    "count" => $statement->rowCount(),
                    "data"            => $result
                );
                //        $response['body'] = json_encode($result);
                $result = $json_data;
            }
            return $result;

        } catch (\PDOException $e) {
            $this->logger->insertAuditLogEntry($this->API_NAME,
                "Failed to retrieve client billing report for orgID: " . $orgID . " | " . $e->getMessage());
            if(isset($_GET['dt'])){
                $json_data = array("data" => []);
                return $json_data;
            }
            return [];
        }
    }


    public function findTypistBilling()
    {
        // todo return billed
//        $returnBilled = $_GET["return_billed"] ?? 0;
//        if($returnBilled == 1)
//        {
//            $returnBilled = "0 or billed = 1";
//        }

//        $org = Account::withID($orgID,$this->db);

        $statement = "
            SELECT 
			   	file_id,
				job_id, 
				file_author, 
				file_work_type, 
				file_date_dict, 
				audio_length, 
				file_transcribed_date,
				files.acc_id,
                a.bill_rate1_min_pay,
                a.acc_name,
				file_comment,
                ROUND(((audio_length/60) * a.bill_rate1_min_pay), 2) as bill
			FROM 
				files
            INNER JOIN accounts a on files.acc_id = a.acc_id 
			WHERE 
				file_status  = '3' AND 
				isBillable = '1' AND
				billed = '0' AND 
				job_transcribed_by = ? AND
				file_transcribed_date BETWEEN ? AND ? 
				";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                $_GET["typist_email"],
                $_GET["start_date"],
                $_GET["end_date"],

            ));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $seconds = array_sum(array_column($result, "audio_length"));
            $totalMinutes = number_format($seconds / 60,2);
            $totalPayable = number_format(array_sum(array_column($result, "bill")), 2);
//            $totalPayable = $totalMinutes *
            if(isset($_GET['dt'])){
                $json_data = array(
                    "total_payable" => $totalPayable,
                    "total_minutes" => $totalMinutes,
                    "generated_on" => date("Y-m-d H:i:s"),
                    "total_minutes_str" => sprintf('%02d:%02d:%02d', ($seconds / 3600), ($seconds / 60 % 60), $seconds % 60),
                    "count" => $statement->rowCount(),
                    "data"            => $result
                );
                //        $response['body'] = json_encode($result);
                $result = $json_data;
            }
            return $result;

        } catch (\PDOException $e) {
            $this->logger->insertAuditLogEntry($this->API_NAME,
                "Failed to retrieve typist billing report for typist: " . $_GET["typist_email"] . " | " . $e->getMessage());
            if(isset($_GET['dt'])){
                $json_data = array("data" => []);
                return $json_data;
            }
            return [];
        }
    }

    public function billProcessing($orgID)
    {
        $org = Account::withID($orgID,$this->db);

        $data = $_POST["data"];
        $count = $_POST["count"];
        $invoice_bill = $_POST["invoice_bill"];

        if($count != sizeof($data))
        {
            return [];
        }

        foreach ($data as $job)
        {
            if($job->mark_as_billed)
            {
                File::withID($job->job_id, $this->db)->updateBilled(1);
            }
        }


        /*$statement = "
            SELECT 
                file_id,
                job_id, 
                file_author, 
                file_work_type, 
                file_date_dict, 
                audio_length, 
                job_upload_date,
                file_transcribed_date,
                file_comment
            FROM 
                files
            WHERE 
                file_status  = '3' AND 
                isBillable = '1' AND
                (billed = $returnBilled) AND 
                acc_id = ? AND
                file_transcribed_date BETWEEN ? AND ?
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($orgID, $_GET["start_date"], $_GET["end_date"]));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

            if(isset($_GET['dt'])){
                $json_data = array(
                    "organization" => $org->getAccName(),
                    "count" => $statement->rowCount(),
                    "data"            => $result
                );
                //        $response['body'] = json_encode($result);
                $result = $json_data;
            }
            return $result;

        } catch (\PDOException $e) {
            $this->logger->insertAuditLogEntry($this->API_NAME,
                "Failed to retrieve billing report for orgID: " . $orgID . " | " . $e->getMessage());
            if(isset($_GET['dt'])){
                $json_data = array("data" => []);
                return $json_data;
            }
            return [];
        }*/
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

    public function delete($id)
    {
        /*$statement = "
            DELETE FROM tests
            WHERE test_id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }*/
    }

    public function insertModel(BaseModel $model): int
    {
        // TODO: Implement insertModel() method.
    }

    public function updateModel(BaseModel $model): int
    {
        // TODO: Implement updateModel() method.
    }

    public function deleteModel(int $id): int
    {
        // TODO: Implement deleteModel() method.
    }

    public function findModel($id): array|null
    {
        // TODO: Implement findModel() method.
    }

    public function findAltModel($id): array|null
    {
        // TODO: Implement findAltModel() method.
    }

    public function findAllModel($page = 1): array|null
    {
        // TODO: Implement findAllModel() method.
    }
}