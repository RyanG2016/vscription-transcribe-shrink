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


    public function generateNewAccountPrefix($accName)
    {
        $accNameSub = strtoupper(substr($accName, 0,2));
//        $accNameSub = "%";
        // SELECT count(job_prefix) FROM `accounts` where job_prefix like 'XX%'
        // -> if 0 skip

        $statement = "SELECT count(job_prefix) as count FROM `accounts` where `job_prefix` like ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($accNameSub."%"));
            $result = $statement->fetch();
            $count = $result["count"];
            if($count == 0){
                return $accNameSub."-";
            }else{
                return $accNameSub.$count."-";
            }

        } catch (\PDOException $e) {
//            exit($e->getMessage());
            return false;
        }

    }

    public function insertNewAccount()
    {
        if(
            !isset($_POST["enabled"]) ||
            !isset($_POST["billable"]) ||
            !isset($_POST["acc_name"])
        ) {
            return $this->errorOccurredResponse("Invalid Input (1)");
        }

        $accName = $_POST["acc_name"];
        if(
            empty(trim($accName)) ||
            strpos($_POST['acc_name'], '%') !== FALSE || strpos($_POST['acc_name'], '_')
        ){
            return $this->errorOccurredResponse("Invalid Input (2)");
        }


        $accPrefix = $this->generateNewAccountPrefix($accName);
        if(!$accPrefix){
            return $this->errorOccurredResponse("Couldn't generate job prefix");
        }

//        $enabled = $_POST["enabled"];
//        $billable = $_POST["billable"];
        $fields = "";
        $valsQMarks = "";
        $valsArray = array();
//        $i = 0;
//        $len = count($_POST);

        foreach ($_POST as $key=>$value){

            // setting all empty params to 0
            if(empty($input)){
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
                             ".$fields." job_prefix
                             ) 
                         VALUES 
                                (".$valsQMarks."?)";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute($valsArray);

            if($statement->rowCount() > 0)
            {
                return $this->oKResponse("Account Created");
            }else{
                return $this->errorOccurredResponse("Couldn't Create Account");
            }

        } catch (PDOException $e) {
//            die($e->getMessage());
            return false;
        }
    }

    public function oKResponse($msg2 = ""){

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            "error" => false,
            "msg" => $msg2
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