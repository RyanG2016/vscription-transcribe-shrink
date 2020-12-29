<?php

namespace Src\TableGateways;

use PDOException;
use Src\Models\SR;
use Src\TableGateways\logger;
use Src\TableGateways\accessGateway;
use Src\TableGateways\UserGateway;
use Src\Constants\Constants;

require "accountsFilter.php";

class AccountGateway
{

    private $db;
    private $logger;
    private $API_NAME;
    private $accessGateway;
    private $userGateway;

    public function __construct($db)
    {
        $this->db = $db;
        $this->logger = new logger($db);
        $this->accessGateway = new accessGateway($db);
        $this->userGateway = new UserGateway($db);
        $this->API_NAME = "Accounts";
    }

    public function findAll()
    {
        $filter = parseAccountParams(true);

        if (isset($_GET['access-model'])) {
            $statement = "
            SELECT 
                acc_id,
                acc_name
            FROM
                accounts
        " . $filter . ";";
        }

        else{
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
                job_prefix,
                sr.sr_minutes_remaining
            FROM
                accounts
            LEFT JOIN speech_recognition sr on accounts.acc_id = sr.account_id                    
        " . $filter . "
        ;";
        }



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
                job_prefix,
                sr_minutes_remaining
            FROM
                accounts
            LEFT JOIN speech_recognition sr on accounts.acc_id = sr.account_id
            WHERE acc_id = ?
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }


    public function findPubAccount($id)
    {
        $statement = "
            SELECT 
                accounts.acc_id,
                enabled,
                billable,
                acc_name,
                acc_retention_time,
                acc_creation_date,
                lifetime_minutes,
                work_types,
                next_job_tally,
                act_log_retention_time,
                job_prefix
            FROM
                accounts
            INNER JOIN access a on accounts.acc_id = a.acc_id
            WHERE accounts.acc_id = ?
            AND a.uid = ?
            AND a.acc_role = 2
;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id, $_SESSION["uid"]));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            return false;
//            exit($e->getMessage());
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
                $nextNum = (int)$matchGroups[2] + 1;
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
            return $this->errorOccurredResponse("Invalid Input (I-1)");
        }

        $accName = $_POST["acc_name"];
        if (
            empty(trim($accName)) ||
            strpos($_POST['acc_name'], '%') !== FALSE || strpos($_POST['acc_name'], '_')
        ) {
            return $this->errorOccurredResponse("Invalid Input (2)");
        }

        if(!accountSqlInjectionCheckPassed($_POST))
        {
            return $this->errorOccurredResponse("Invalid Input (505-CACC)");
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

            $fields .= "`$key`";
            switch ($key){
                case "bill_rate1":
                case "bill_rate1_TAT":
                case "bill_rate1_min_pay":
                case "bill_rate2":
                case "bill_rate2_TAT":
                case "bill_rate2_min_pay":
                case "bill_rate3":
                case "bill_rate3_TAT":
                case "bill_rate3_min_pay":
                case "bill_rate4":
                case "bill_rate4_TAT":
                case "bill_rate4_min_pay":
                case "bill_rate5":
                case "bill_rate5_TAT":
                case "bill_rate5_min_pay":
            }{
                if($value == ""){
                    $value = 0;
                }
            }

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
                $accountID = $this->db->lastInsertId();
                $this->logger->insertAuditLogEntry($this->API_NAME, "Account Created: " . $_POST["acc_name"]);


                $sr = SR::withAccID($accountID, $this->db);
                $sr->addToMinutesRemaining(Constants::COMPLEMENTARY_NEW_ACCOUNT_FREE_STT_MINUTES);
                $sr->save();
                $this->logger->insertAuditLogEntry($this->API_NAME, "Added complementary " . Constants::COMPLEMENTARY_NEW_ACCOUNT_FREE_STT_MINUTES . " minutes to new account: " . $accountID);

                return $this->oKResponse($this->db->lastInsertId(), "Account Created");
            } else {
                return $this->errorOccurredResponse("Couldn't Create Account");
//                return $this->errorOccurredResponse("Couldn't Create Account" . print_r($statement->errorInfo()));
            }

        } catch (PDOException $e) {
//            die($e->getMessage());
            return $this->errorOccurredResponse("Couldn't Create Account (2)");
        }
    }


    /**
     * Creates a client administrator account for the current logged in user
     * <br> <i>(only allowed once per user account)</i>
     * @param string $accName from controller
     * @return mixed
     */
    public function createNewClientAdminAccount($accName)
    {
        $accPrefix = $this->generateNewAccountPrefix($accName);
        if (!$accPrefix) {
            return $this->errorOccurredResponse("Couldn't generate job prefix");
        }

        // insert to DB //
        $statement = "INSERT
                        INTO 
                            accounts 
                            (
                             enabled,
                             billable,
                             acc_name,
                             acc_retention_time,
                             bill_rate1,
                             bill_rate1_type,
                             bill_rate1_TAT,
                             bill_rate1_min_pay,
                             bill_rate1_desc,
                             bill_rate2,
                             bill_rate2_type,
                             bill_rate2_TAT,
                             bill_rate2_min_pay,
                             bill_rate2_desc,
                             bill_rate3,
                             bill_rate3_type,
                             bill_rate3_TAT, 
                             bill_rate3_min_pay,
                             bill_rate3_desc, 
                             bill_rate4, 
                             bill_rate4_type,
                             bill_rate4_TAT,
                             bill_rate4_min_pay,
                             bill_rate4_desc, 
                             bill_rate5, 
                             bill_rate5_type, 
                             bill_rate5_TAT,
                             bill_rate5_min_pay, 
                             bill_rate5_desc, 
                             act_log_retention_time,
                             job_prefix
                             ) 
                         VALUES 
                                (
                                 ?, ?, ?,
                                 ?,
                                 ?, ?, ?, ?,
                                 ?,
                                 ?, ?, ?, ?,
                                 ?,
                                 ?, ?, ?, ?,
                                 ?,
                                 ?, ?, ?, ?,
                                 ?,
                                 ?, ?, ?, ?,
                                 ?,
                                 ?, ?
                                )";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                1, 1, $accName,
                14,// acc_ret,
                0, 0, 0, 0,// br1, br1type, br1tat , br1 min
                0, // br1 desc
                0, 0, 0, 0,// br2, br2type, br2tat , br2 min
                0, // br2 desc
                0, 0, 0, 0,// br3, br3type, br3tat , br3 min
                0, // br3 desc
                0, 0, 0, 0,// br4, br4type, br4tat , br4 min
                0, // br4 desc
                0, 0, 0, 0,// br5, br5type, br5tat , br5 min
                0, // br5 desc
                90, $accPrefix// log retention, job prefix
            ));

            if ($statement->rowCount() > 0) {
                $accountID = $this->db->lastInsertId();
                $this->logger->insertAuditLogEntry($this->API_NAME, "Account Created: " . $accName);

                // add Complementary 30 minutes STT
                $sr = SR::withAccID($accountID, $this->db);
                $sr->addToMinutesRemaining(Constants::COMPLEMENTARY_NEW_ACCOUNT_FREE_STT_MINUTES);
                $sr->save();
                $this->logger->insertAuditLogEntry($this->API_NAME, "Added complementary " . Constants::COMPLEMENTARY_NEW_ACCOUNT_FREE_STT_MINUTES . " minutes to new account: " . $accountID);

                // update account field in user entry and give client admin permission
                if($this->userGateway->internalUpdateUserClientAdminAccount($accountID, $accName)){
                    if($this->accessGateway->giveClientAdminPermission($accountID)){
                        return $this->oKResponse($accountID, "Account Created");
                    }
                }
                return $this->errorOccurredResponse("Couldn't Create Account (ACO-3)");
            } else {
                return $this->errorOccurredResponse("Couldn't Create Account (ACO-1)");
//                return $this->errorOccurredResponse("Couldn't Create Account" . print_r($statement->errorInfo()));
            }

        } catch (PDOException $e) {
//            die($e->getMessage());
            return $this->errorOccurredResponse("Couldn't Create Account (ACO-2)");
        }
    }

    public function updateAccount($id)
    {
        parse_str(file_get_contents('php://input'), $put);
        if(isset($put["update-sr-min"]))
        {
            return $this->addSRminutesToAcc($id, $put["min"]);
        }

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
                $this->logger->insertAuditLogEntry($this->API_NAME, "Account Updated: " . $id);
                return $this->oKResponse($id, "Account Updated");
            } else {
                return $this->errorOccurredResponse("Couldn't update account or no changes were found to update");
            }

        } catch (PDOException $e) {
//            die($e->getMessage());
            return false;
        }
    }

    public function addSRminutesToAcc($id, $minutes)
    {
        // update DB //
        $sr = SR::withAccID($id, $this->db);
        $sr->addToMinutesRemaining(floatval($minutes));
        $sr->save();
        $this->logger->insertAuditLogEntry($this->API_NAME, "Manually added STT mins to account: " . $id . " | minutes: " . $minutes);
        return $this->oKResponse($id, "Minutes Updated");

        /*try {
            $statement = $this->db->prepare($statement);
            $statement->execute($valsArray);

            if ($statement->rowCount() > 0) {
                $this->logger->insertAuditLogEntry($this->API_NAME, "Account Updated: " . $id);
                return $this->oKResponse($id, "Account Updated");
            } else {
                return $this->errorOccurredResponse("Couldn't update account or no changes were found to update");
            }

        } catch (PDOException $e) {
//            die($e->getMessage());
            return false;
        }*/
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
            $this->logger->insertAuditLogEntry($this->API_NAME, "Account Deleted: " . $id);
            if($statement->rowCount())
            {
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['body'] = json_encode([
                    'error' => false,
                    'msg' => 'Account Deleted.'
                ]);
            }else{
                if($statement->errorInfo())
                {
                    $response['status_code_header'] = 'HTTP/1.1 200 OK';
                    $response['body'] = json_encode([
                        'error' => true,
                        'msg' => 'Couldn\'t delete account - '. $statement->errorInfo()[2]
                    ]);
                }else{
                    $response['status_code_header'] = 'HTTP/1.1 200 OK';
                    $response['body'] = json_encode([
                        'error' => true,
                        'msg' => 'Couldn\'t delete account, unknown error occurred'
                    ]);
                }
            }
            return $response;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * Retrieves current account work types for jobtype combobox
     * @param $accID int account ID
     * @return array work types delimited by commas
     * @internal used in transcribe.php
     */
    public function getWorkTypes($accID)
    {
        // Interview,Focus Group,Notes,Letter,Other
        $defaultTypes = array(
            "Interview", "Focus Group","Notes","Letter","Other"
        );
        $statement = "
            SELECT 
                work_types
            FROM
                accounts
            WHERE acc_id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($accID));
            if($statement->rowCount())
            {
                $db_work_types = $statement->fetch()["work_types"];
                if($db_work_types != null)
                {
                    $arr_db_work_types = explode(",", $db_work_types);
                    if(sizeof($arr_db_work_types) > 0)
                    {
                        // we got predefined account work types here
                        return $arr_db_work_types;
                    }
                }
            }

        } catch (\PDOException $e) {
            return $defaultTypes;
//            exit($e->getMessage());
        }
        return $defaultTypes;
    }
}