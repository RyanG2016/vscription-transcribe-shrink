<?php

namespace Src\TableGateways;

require "accountsFilter.php";

class AccountGateway
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $filter = parseParams(true);

        $statement = "
            SELECT 
                acc_id,
                enabled,
                billable,
                acc_name,
                acc_retention_time,
                acc_creation_date,
                bill_rate1,
                bill_rate1_type,
                bill_rate1_TAT,
                bill_rate1_desc,
                bill_rate1_min_pay,
                bill_rate2,
                bill_rate2_type,
                bill_rate2_TAT,
                bill_rate2_desc,
                bill_rate2_min_pay,
                bill_rate3,
                bill_rate3_type,
                bill_rate3_TAT,
                bill_rate3_desc,
                bill_rate3_min_pay,
                bill_rate4,
                bill_rate4_type,
                bill_rate4_TAT,
                bill_rate4_desc,
                bill_rate4_min_pay,
                bill_rate5,
                bill_rate5_type,
                bill_rate5_TAT,
                bill_rate5_desc,
                bill_rate5_min_pay,
                lifetime_minutes,
                work_types,
                next_job_tally,
                act_log_retention_time,
                job_prefix
            FROM
                accounts
        " . $filter . ";";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION['accID']));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if (isset($_GET['dt'])) {
                $json_data = array(
                    //            "draw"            => intval( $_REQUEST['draw'] ),
                    //            "recordsTotal"    => intval( 2 ),
                    //            "recordsFiltered" => intval( 1 ),
                    "data" => $result
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
                acc_id,
                enabled,
                billable,
                acc_name,
                acc_retention_time,
                acc_creation_date,
                bill_rate1,
                bill_rate1_type,
                bill_rate1_TAT,
                bill_rate1_desc,
                bill_rate1_min_pay,
                bill_rate2,
                bill_rate2_type,
                bill_rate2_TAT,
                bill_rate2_desc,
                bill_rate2_min_pay,
                bill_rate3,
                bill_rate3_type,
                bill_rate3_TAT,
                bill_rate3_desc,
                bill_rate3_min_pay,
                bill_rate4,
                bill_rate4_type,
                bill_rate4_TAT,
                bill_rate4_desc,
                bill_rate4_min_pay,
                bill_rate5,
                bill_rate5_type,
                bill_rate5_TAT,
                bill_rate5_desc,
                bill_rate5_min_pay,
                lifetime_minutes,
                work_types,
                next_job_tally,
                act_log_retention_time,
                job_prefix
            FROM
                accounts
            WHERE acc_id = ?;
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


    public function generateNewAccountPrefix()
    {

        $statement = "SELECT count(job_prefix) FROM `accounts` where job_prefix like '\?%'";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($concatName));
            if ($statement->rowCount() == 1) {
                $result = $statement->fetch();
                $numbers = array(
                    "next_job_id" => strval($result['next_job_id']),
                    "next_job_num" => strval($result['next_job_num'])
                );
                return json_encode($numbers);

            } else {
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

   /* public function getAccountPrefix()
    {

        $statement = "select job_prefix from accounts where acc_id = " . $_SESSION["accID"];

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
            if ($statement->rowCount() == 1) {
                $result = $statement->fetch();
                return $result['job_prefix'];

            } else {
                return false;
            }
        } catch (\PDOException $e) {
//            exit($e->getMessage());
            return false;
        }

    }*/

    public function insertNewAccount($input)
    {
        $nextAccountID = $input[0];
        $nextNum = $input[1];
        $authorName = $input[2];
        $jobType = $input[3];
        $dictDate = $input[4];
        $speakerType = $input[5];
        $comments = $input[6];
        $orig_accountname = $input[7];
        $account_name = $input[8];
        $account_duration = $input[9];
        $uploadedBy = $_SESSION['uEmail'];

        $jobPrefix = $this->getAccountPrefix();
        if (!$jobPrefix) {
            die("couldn't get job prefix");
//            return false;
        }
        $nextJobNum = $jobPrefix . str_pad($nextNum, 7, "0", STR_PAD_LEFT);

        $statement = "INSERT
                        INTO 
                            accounts 
                            (
                             job_id, 
                             account_author, 
                             account_work_type, 
                             account_date_dict, 
                             account_speaker_type, 
                             account_comment, 
                             job_uploaded_by, 
                             accountname, 
                             orig_accountname, 
                             acc_id, 
                             audio_length
                             ) 
                         VALUES 
                                (?,?,?,?,?,?,?,?,?,?,?)";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($nextJobNum, $authorName, $jobType, $dictDate,
                $speakerType, $comments, $uploadedBy, $account_name, $orig_accountname, $_SESSION["accID"], $account_duration));

            if ($statement->rowCount()) {
                $statement = "UPDATE accounts SET next_job_tally=next_job_tally+1 where acc_id = " . $_SESSION["accID"];

                try {
                    $statement = $this->db->prepare($statement);
                    $statement->execute();
//                                return $statement->rowCount();
                    return true;
                } catch (\PDOException $e) {
                    die($e->getMessage());
//                                return false;
                }
            } else {
                die(print_r($statement->errorInfo(), true));
//                return false;
            }
//            return $statement->rowCount();
        } catch (\PDOException $e) {
            die($e->getMessage());
//            return false;
        }

    }

  /*  public function delete($id)
    {
        $statement = "
            DELETE FROM accounts
            WHERE account_id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }*/
}