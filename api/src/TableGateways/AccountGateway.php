<?php

namespace Src\TableGateways;

use PDOException;

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
            $statement->execute();
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


    public function generateNewAccountPrefix($accName)
    {
        $accNameSub = strtoupper(substr($accName, 0, 2));
//        $accNameSub = "%";
        // SELECT count(job_prefix) FROM `accounts` where job_prefix like 'XX%'
        // -> if 0 skip

//        $statement = "SELECT count(job_prefix) as count FROM `accounts` where `job_prefix` like ?";
        $statement = "SELECT job_prefix FROM `accounts` where job_prefix like ? order by job_prefix desc limit 1";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($accNameSub . "%"));
            $count = $statement->rowCount();
            if ($count == 0) {
                return $accNameSub . "-";
            } else {
                $result = $statement->fetch();
                $lastPrefix = $result["job_prefix"];
                $regex = "/(.{2})(.*)-/";
                preg_match($regex, $lastPrefix, $matchGroups);
                $nextNum = $matchGroups[2] + 1;
                return $matchGroups[1] . ($nextNum) . "-";
            }

        } catch (\PDOException $e) {
//            exit($e->getMessage());
            return false;
        }

    }

    public function insertNewAccount()
    {
        if (
            !isset($_POST["enabled"]) ||
            !isset($_POST["billable"]) ||
            !isset($_POST["acc_name"])
        ) {
            return $this->errorOccurredResponse("Invalid Input (1)");
        }

        $accName = $_POST["acc_name"];
        if (
            empty(trim($accName)) ||
            strpos($_POST['acc_name'], '%') !== FALSE || strpos($_POST['acc_name'], '_')
        ) {
            return $this->errorOccurredResponse("Invalid Input (2)");
        }


        $accPrefix = $this->generateNewAccountPrefix($accName);
        if (!$accPrefix) {
            return $this->errorOccurredResponse("Couldn't generate job prefix");
        }

        $fields = "";
        $valsQMarks = "";
        $valsArray = array();
//        $i = 0;
//        $len = count($_POST);

        foreach ($_POST as $key => $value) {

            // setting all empty params to 0
            if (empty($input)) {
                $input = 0;
            }

            $fields .= "`$key`";
            array_push($valsArray, $value);
            $valsQMarks .= "?";

//            if ($i != $len - 1) {
            // not last item add comma
            $fields .= ", ";
            $valsQMarks .= ", ";
//            }

//            $i++;
        }

        array_push($valsArray, $accPrefix);

        // insert to DB //
        $statement = "INSERT
                        INTO 
                            accounts 
                            (
                             " . $fields . " job_prefix
                             ) 
                         VALUES 
                                (" . $valsQMarks . "?)";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute($valsArray);

            if ($statement->rowCount() > 0) {
                return $this->oKResponse($this->db->lastInsertId(), "Account Created");
            } else {
                return $this->errorOccurredResponse("Couldn't Create Account");
//                return $this->errorOccurredResponse("Couldn't Create Account" . print_r($statement->errorInfo()));
            }

        } catch (PDOException $e) {
//            die($e->getMessage());
            return false;
        }
    }

    public function updateAccount($id)
    {
        parse_str(file_get_contents('php://input'), $put);

        if (
            !isset($put["enabled"]) ||
            !isset($put["billable"]) ||
            !isset($put["acc_name"])
        ) {
            return $this->errorOccurredResponse("Invalid Input (1)");
        }

        $accName = $put["acc_name"];
        if (
            empty(trim($accName)) ||
            strpos($put['acc_name'], '%') !== FALSE || strpos($put['acc_name'], '_')
        ) {
            return $this->errorOccurredResponse("Invalid Input (2)");
        }

        $valPairs = "";
        $valsArray = array();

        $i = 0;
        $len = count($put);

        foreach ($put as $key => $value) {

            // setting all empty params to 0
            if (empty($input)) {
                $input = 0;
            }

            $valPairs .= "`$key` = ";
            array_push($valsArray, $value);
            $valPairs .= "?";

            if ($i != $len - 1) {
//                 not last item add comma
                $valPairs .= ", ";
            }

            $i++;
        }

        array_push($valsArray, $id);

        // update DB //
        $statement = "UPDATE
                        accounts 
                        SET 
                             " . $valPairs . " 
                        WHERE 
                            acc_id = ?";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute($valsArray);

            if ($statement->rowCount() > 0) {
                return $this->oKResponse($id, "Account Updated");
            } else {
                return $this->errorOccurredResponse("Couldn't update account or no changes were found to update");
            }

        } catch (PDOException $e) {
//            die($e->getMessage());
            return false;
        }
    }

    public function oKResponse($id, $msg2 = "")
    {

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            "error" => false,
            "msg" => $msg2,
            "id" => $id
        ]);
        return $response;

    }

    private function errorOccurredResponse($error_msg = "")
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Error Occurred';
        $response['body'] = json_encode([
            'error' => true,
            'msg' => $error_msg
        ]);
        return $response;
    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM accounts
            WHERE acc_id = :id;
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