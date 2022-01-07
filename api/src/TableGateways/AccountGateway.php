<?php

namespace Src\TableGateways;

use PDOException;
use Src\Enums\ENV;
use Src\Helpers\common;
use Src\Models\Access;
use Src\Models\Account;
use Src\Models\BaseModel;
use Src\Models\SR;
use Src\Models\User;
use Src\TableGateways\logger;
use Src\TableGateways\accessGateway;
use Src\TableGateways\UserGateway;
use Src\Constants\Constants;

require "accountsFilter.php";

class AccountGateway implements GatewayInterface
{

    private $db;
    private $logger;
    private $API_NAME;
    private $accessGateway;
    private $userGateway;
    private $common;
    private $limit = 10;

    public function __construct($db)
    {
        $this->db = $db;
        $this->logger = new logger($db);
        $this->accessGateway = new accessGateway($db);
        $this->userGateway = new UserGateway($db);
        $this->common = new common();
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
                subscription_type,
                acc_retention_time,
                acc_creation_date,
                bill_rate1,
                bill_rate1_type,
                bill_rate1_tat,
                bill_rate1_desc,
                bill_rate1_min_pay,
                bill_rate2,
                bill_rate2_type,
                bill_rate2_tat,
                bill_rate2_desc,
                bill_rate2_min_pay,
                bill_rate3,
                bill_rate3_type,
                bill_rate3_tat,
                bill_rate3_desc,
                bill_rate3_min_pay,
                bill_rate4,
                bill_rate4_type,
                bill_rate4_tat,
                bill_rate4_desc,
                bill_rate4_min_pay,
                bill_rate5,
                bill_rate5_type,
                bill_rate5_tat,
                bill_rate5_desc,
                bill_rate5_min_pay,
                comp_mins,
                lifetime_minutes,
                work_types,
                next_job_tally,
                act_log_retention_time,
                job_prefix,
                auto_list_refresh,
                auto_list_refresh_interval,
                transcribe_remarks,
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
                subscription_type,
                acc_retention_time,
                acc_creation_date,
                bill_rate1,
                bill_rate1_type,
                bill_rate1_tat,
                bill_rate1_desc,
                bill_rate1_min_pay,
                bill_rate2,
                bill_rate2_type,
                bill_rate2_tat,
                bill_rate2_desc,
                bill_rate2_min_pay,
                bill_rate3,
                bill_rate3_type,
                bill_rate3_tat,
                bill_rate3_desc,
                bill_rate3_min_pay,
                bill_rate4,
                bill_rate4_type,
                bill_rate4_tat,
                bill_rate4_desc,
                bill_rate4_min_pay,
                bill_rate5,
                bill_rate5_type,
                bill_rate5_tat,
                bill_rate5_desc,
                bill_rate5_min_pay,
                comp_mins,
                lifetime_minutes,
                work_types,
                next_job_tally,
                act_log_retention_time,
                job_prefix,
                sr_minutes_remaining,
                auto_list_refresh,
                auto_list_refresh_interval,
                transcribe_remarks

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
                subscription_type,
                acc_retention_time,
                acc_creation_date,
                lifetime_minutes,
                work_types,
                next_job_tally,
                act_log_retention_time,
                job_prefix,
                auto_list_refresh,
                auto_list_refresh_interval,
                transcribe_remarks
                   
            
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
        // todo prepare
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

        /*if(!accountSqlInjectionCheckPassed($_POST))
        {
            return $this->errorOccurredResponse("Invalid Input (505-CACC)");
        }*/

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
                case "bill_rate1_tat":
                case "bill_rate1_min_pay":
                case "bill_rate2":
                case "bill_rate2_tat":
                case "bill_rate2_min_pay":
                case "bill_rate3":
                case "bill_rate3_tat":
                case "bill_rate3_min_pay":
                case "bill_rate4":
                case "bill_rate4_tat":
                case "bill_rate4_min_pay":
                case "bill_rate5":
                case "bill_rate5_tat":
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
    public function createNewClientAdminAccount($accName, $subType)
    {
        $accPrefix = $this->generateNewAccountPrefix($accName);
        if (!$accPrefix) {
            return $this->errorOccurredResponse("Couldn't generate job prefix");
        }

        switch ($subType) {
            case "1":
                $br1 = 9.99;
                $br1Type = 0;
                $br1TAT = 3;
                $br1MinPay = 1.25;
                $br1Desc = "Monthly rate max 1000 minutes";
                $jobRetention = 45;
                $srEnabled = 0;
                break;
            case "2":
                $br1 = 1.65;
                $br1Type = 0;
                $br1TAT = 3;
                $br1MinPay = 0;
                $br1Desc = "Default per minute rate";
                $jobRetention = 14;
                $srEnabled = 0;
                break;
            case "3":
                $br1 = 0;
                $br1Type = 0;
                $br1TAT = 0;
                $br1MinPay = 0;
                $br1Desc = "Prepaid minutes";
                $jobRetention = 14;
                $srEnabled = 1;
                break;
            default:
                $br1 = 0;
                $br1Type = 0;
                $br1TAT = 0;
                $br1MinPay = 0;
                $br1Desc = "Undefined";
                $jobRetention = 14;
                $srEnabled = 0;
        }

        // insert to DB //
        $statement = "INSERT
                        INTO 
                            accounts 
                            (
                             enabled,
                             billable,
                             acc_name,
                             subscription_type,
                             acc_retention_time,
                             bill_rate1,
                             bill_rate1_type,
                             bill_rate1_tat,
                             bill_rate1_min_pay,
                             bill_rate1_desc,
                             bill_rate2,
                             bill_rate2_type,
                             bill_rate2_tat,
                             bill_rate2_min_pay,
                             bill_rate2_desc,
                             bill_rate3,
                             bill_rate3_type,
                             bill_rate3_tat, 
                             bill_rate3_min_pay,
                             bill_rate3_desc, 
                             bill_rate4, 
                             bill_rate4_type,
                             bill_rate4_tat,
                             bill_rate4_min_pay,
                             bill_rate4_desc, 
                             bill_rate5, 
                             bill_rate5_type, 
                             bill_rate5_tat,
                             bill_rate5_min_pay, 
                             bill_rate5_desc, 
                             act_log_retention_time,
                             job_prefix,
                             sr_enabled
                             ) 
                         VALUES 
                                (
                                 ?, ?, ?,
                                 ?,
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
                                 ?, ?,
                                 ?
                                )";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                1, 1, $accName,
                $subType, //Subscription Type
                $jobRetention,// acc_ret,
                $br1, $br1Type, $br1TAT, $br1MinPay, // br1, br1type, br1tat , br1 min
                $br1Desc, // br1 desc
                0, 0, 0, 0,// br2, br2type, br2tat , br2 min
                0, // br2 desc
                0, 0, 0, 0,// br3, br3type, br3tat , br3 min
                0, // br3 desc
                0, 0, 0, 0,// br4, br4type, br4tat , br4 min
                0, // br4 desc
                0, 0, 0, 0,// br5, br5type, br5tat , br5 min
                0, // br5 desc
                90, $accPrefix,// log retention, job prefix
                $srEnabled //SR Enabled
            ));

            if ($statement->rowCount() > 0) {
                $accountID = $this->db->lastInsertId();
                $this->logger->insertAuditLogEntry($this->API_NAME, "Account Created: " . $accName . " with subscription type " . $subType);
                // add Complementary 30 minutes STT
                $sr = SR::withAccID($accountID, $this->db);
                $sr->addToMinutesRemaining(Constants::COMPLEMENTARY_NEW_ACCOUNT_FREE_STT_MINUTES);
                $sr->save();
                $this->logger->insertAuditLogEntry($this->API_NAME, "Added complementary " . Constants::COMPLEMENTARY_NEW_ACCOUNT_FREE_STT_MINUTES . " minutes to new account: " . $accountID);

                // update account field in user entry and give client admin permission
                if($this->userGateway->internalUpdateUserClientAdminAccount($accountID, $accName)){
                    if($this->accessGateway->giveClientAdminPermission($accountID)){

                        // set session variables
                        $account = Account::withID($accountID, $this->db);
                        $_SESSION["userData"]["admin_acc_name"] = $account->getAccName();
                        $_SESSION["userData"]["account"] = $accountID;
                        $_SESSION["userData"]["adminart"] = $account->getAccRetentionTime();
                        $_SESSION["userData"]["adminalrt"] = $account->getActLogRetentionTime();

                        $_SESSION["adminAccountName"] = $account->getAccName();
                        $_SESSION["adminAccLogRetTime"] = $account->getActLogRetentionTime();
                        $_SESSION["adminAccRetTime"] = $account->getAccRetentionTime();
                        $_SESSION["adminJobListRefreshInterval"] = $account->getAccJobRefreshInterval();

                        // give system admin access
                        (new Access(acc_id: $accountID, uid: ENV::ADMIN_UID, username: User::withID(ENV::ADMIN_UID, $this->db)->getEmail(), acc_role: 1, db: $this->db))->save();

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
            if (empty($value)) {
                $value = 0;
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


    // updates user accounts (logged in or owned) from settings page
    public function postUpdateAccount($acc_id, $self = false)
    {
        // validation
        foreach ($_POST as $keyPost => $valuePost) {
            switch ($keyPost)
            {
                case 'organization_name':
                    if(!preg_match("/^[a-z]{1}[a-z0-9_ '()\-&]{2,255}$/i", $valuePost))
                    {
                        return $this->errorOccurredResponse("Invalid Input (VSPT-OR101)");
                    }
                    break;

                case 'act_log_ret_time':
                case 'retention_time':
                    if(!(is_numeric($valuePost) && $valuePost <= 180 && $valuePost > 0))
                    {
                        return $this->errorOccurredResponse("Invalid Input (VSPT-R100)");
                    }
                    break;
                case 'auto_list_ref_interval':
                    if(!(is_numeric($valuePost) && $valuePost <= 300 && $valuePost > 29))
                    {
                        return $this->errorOccurredResponse("Invalid Input (VSPT-R101)");
                    }
                    break;
                default:
                    break;
            }
        }

        // valid data proceed with account update
        $account = Account::withID($acc_id, $this->db);
        $account->setAccName($_POST["organization_name"]);
        $account->setAccRetentionTime($_POST["retention_time"]);
        $account->setActLogRetentionTime($_POST["act_log_ret_time"]);
        $account->setAccJobRefreshInterval($_POST["auto_list_ref_interval"]);

        if ($account->save() > 0) {
            if($self)
            {
                $_SESSION["userData"]["admin_acc_name"] = $account->getAccName();
                $_SESSION["userData"]["adminart"] = $account->getAccRetentionTime();
                $_SESSION["userData"]["adminalrt"] = $account->getActLogRetentionTime();
                $_SESSION["userData"]["account"] = $acc_id;
                $_SESSION["adminAccountName"] = $account->getAccName();
                $_SESSION["adminAccLogRetTime"] = $account->getActLogRetentionTime();
                $_SESSION["adminAccRetTime"] = $account->getAccRetentionTime();
                $_SESSION["adminAccJobRefreshInterval"] = $account->getAccJobRefreshInterval();
            }else{
                $_SESSION["acc_name"] = $account->getAccName();
                $_SESSION["acc_retention_time"] = $account->getAccRetentionTime();
                $_SESSION["act_log_retention_time"] = $account->getActLogRetentionTime();
                $_SESSION["auto_list_refresh_interval"] = $account->getAccJobRefreshInterval();
            }

            $this->logger->insertAuditLogEntry($this->API_NAME, "Organization Updated from settings page: " . $acc_id);
            return $this->oKResponse($acc_id, "Organization Updated");
        } else {
            return $this->errorOccurredResponse("Couldn't update Organization or no changes were found to update");
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
     * Also used in API endpoint /accounts/{id}/worktypes
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

    public function insertModel(BaseModel|Account $model): int
    {
        $statement = "
            INSERT INTO accounts 
                (enabled, billable, acc_name, acc_retention_time, acc_creation_date, bill_rate1, bill_rate1_type, bill_rate1_tat, bill_rate1_desc, bill_rate1_min_pay, bill_rate2, bill_rate2_type, bill_rate2_tat, bill_rate2_desc, bill_rate2_min_pay, bill_rate3, bill_rate3_type, bill_rate3_tat, bill_rate3_desc, bill_rate3_min_pay, bill_rate4, bill_rate4_type, bill_rate4_tat, bill_rate4_desc, bill_rate4_min_pay, bill_rate5, bill_rate5_type, bill_rate5_tat, bill_rate5_desc, bill_rate5_min_pay, lifetime_minutes, work_types, next_job_tally, act_log_retention_time, job_prefix, sr_enabled, auto_list_refresh_interval, transcribe_remarks, profile_id, payment_id)
            VALUES
                (:enabled, :billable, :acc_name, :acc_retention_time, :acc_creation_date, :bill_rate1, :bill_rate1_type, :bill_rate1_tat, :bill_rate1_desc, :bill_rate1_min_pay, :bill_rate2, :bill_rate2_type, :bill_rate2_tat, :bill_rate2_desc, :bill_rate2_min_pay, :bill_rate3, :bill_rate3_type, :bill_rate3_tat, :bill_rate3_desc, :bill_rate3_min_pay, :bill_rate4, :bill_rate4_type, :bill_rate4_tat, :bill_rate4_desc, :bill_rate4_min_pay, :bill_rate5, :bill_rate5_type, :bill_rate5_tat, :bill_rate5_desc, :bill_rate5_min_pay, :lifetime_minutes, :work_types, :next_job_tally, :act_log_retention_time, :job_prefix, :sr_enabled, :auto_list_refresh_interval, :transcribe_remarks, :profile_id, :payment_id)
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'enabled' => $model->getEnabled(),
                'billable' => $model->getBillable(),
                'acc_name' => $model->getAccName(),
                'acc_retention_time' => $model->getAccRetentionTime(),
                'acc_creation_date' => $model->getAccCreationDate(),
                'bill_rate1' => $model->getBillRate1(),
                'bill_rate1_type' => $model->getBillRate1Type(),
                'bill_rate1_tat' => $model->getBillRate1Tat(),
                'bill_rate1_desc' => $model->getBillRate1Desc(),
                'bill_rate1_min_pay' => $model->getBillRate1MinPay(),
                'bill_rate2' => $model->getBillRate2(),
                'bill_rate2_type' => $model->getBillRate2Type(),
                'bill_rate2_tat' => $model->getBillRate2Tat(),
                'bill_rate2_desc' => $model->getBillRate2Desc(),
                'bill_rate2_min_pay' => $model->getBillRate2MinPay(),
                'bill_rate3' => $model->getBillRate3(),
                'bill_rate3_type' => $model->getBillRate3Type(),
                'bill_rate3_tat' => $model->getBillRate3Tat(),
                'bill_rate3_desc' => $model->getBillRate3Desc(),
                'bill_rate3_min_pay' => $model->getBillRate3MinPay(),
                'bill_rate4' => $model->getBillRate4(),
                'bill_rate4_type' => $model->getBillRate4Type(),
                'bill_rate4_tat' => $model->getBillRate4Tat(),
                'bill_rate4_desc' => $model->getBillRate4Desc(),
                'bill_rate4_min_pay' => $model->getBillRate4MinPay(),
                'bill_rate5' => $model->getBillRate5(),
                'bill_rate5_type' => $model->getBillRate5Type(),
                'bill_rate5_tat' => $model->getBillRate5Tat(),
                'bill_rate5_desc' => $model->getBillRate5Desc(),
                'bill_rate5_min_pay' => $model->getBillRate5MinPay(),
                'lifetime_minutes' => $model->getLifetimeMinutes(),
                'work_types' => $model->getWorkTypes(),
                'next_job_tally' => $model->getNextJobTally(),
                'act_log_retention_time' => $model->getActLogRetentionTime(),
                'job_prefix' => $model->getJobPrefix(),
                'sr_enabled' => $model->getSrEnabled(),
                'transcribe_remarks' => $model->getTranscribeRemarks(),
                'auto_list_refresh_interval' => $model->getAccJobRefreshInterval(),
                'payment_id'=>$model->getPaymentId(),
                'profile_id'=>$model->getProfileId()

            ));
            if($statement->rowCount())
            {
                return $this->db->lastInsertId();
            }else{
                return 0;
            }
        } catch (\PDOException) {
            return 0;
        }
    }

    public function updateModel(BaseModel|Account $model): int
    {
        $statement = "
            UPDATE accounts
            SET
                enabled = :enabled,
                billable = :billable,
                acc_name = :acc_name,
                acc_retention_time = :acc_retention_time,
                acc_creation_date = :acc_creation_date,
                bill_rate1 = :bill_rate1,
                bill_rate1_type = :bill_rate1_type,
                bill_rate1_tat = :bill_rate1_tat,
                bill_rate1_desc = :bill_rate1_desc,
                bill_rate1_min_pay = :bill_rate1_min_pay,
                bill_rate2 = :bill_rate2,
                bill_rate2_type = :bill_rate2_type,
                bill_rate2_tat = :bill_rate2_tat,
                bill_rate2_desc = :bill_rate2_desc,
                bill_rate2_min_pay = :bill_rate2_min_pay,
                bill_rate3 = :bill_rate3,
                bill_rate3_type = :bill_rate3_type,
                bill_rate3_tat = :bill_rate3_tat,
                bill_rate3_desc = :bill_rate3_desc,
                bill_rate3_min_pay = :bill_rate3_min_pay,
                bill_rate4 = :bill_rate4,
                bill_rate4_type = :bill_rate4_type,
                bill_rate4_tat = :bill_rate4_tat,
                bill_rate4_desc = :bill_rate4_desc,
                bill_rate4_min_pay = :bill_rate4_min_pay,
                bill_rate5 = :bill_rate5,
                bill_rate5_type = :bill_rate5_type,
                bill_rate5_tat = :bill_rate5_tat,
                bill_rate5_desc = :bill_rate5_desc,
                bill_rate5_min_pay = :bill_rate5_min_pay,
                lifetime_minutes = :lifetime_minutes,
                work_types = :work_types,
                next_job_tally = :next_job_tally,
                act_log_retention_time = :act_log_retention_time,
                job_prefix = :job_prefix,
                sr_enabled = :sr_enabled,
                trial = :trial,
                auto_list_refresh_interval = :auto_list_refresh_interval,
                transcribe_remarks = :transcribe_remarks,
                comp_mins =:comp_mins,
                profile_id=:profile_id,
                payment_id=:payment_id
            WHERE
                acc_id = :acc_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'acc_id' => $model->getAccId(),
                'enabled' => $model->getEnabled(),
                'billable' => $model->getBillable(),
                'acc_name' => $model->getAccName(),
                'acc_retention_time' => $model->getAccRetentionTime(),
                'acc_creation_date' => $model->getAccCreationDate(),
                'bill_rate1' => $model->getBillRate1(),
                'bill_rate1_type' => $model->getBillRate1Type(),
                'bill_rate1_tat' => $model->getBillRate1Tat(),
                'bill_rate1_desc' => $model->getBillRate1Desc(),
                'bill_rate1_min_pay' => $model->getBillRate1MinPay(),
                'bill_rate2' => $model->getBillRate2(),
                'bill_rate2_type' => $model->getBillRate2Type(),
                'bill_rate2_tat' => $model->getBillRate2Tat(),
                'bill_rate2_desc' => $model->getBillRate2Desc(),
                'bill_rate2_min_pay' => $model->getBillRate2MinPay(),
                'bill_rate3' => $model->getBillRate3(),
                'bill_rate3_type' => $model->getBillRate3Type(),
                'bill_rate3_tat' => $model->getBillRate3Tat(),
                'bill_rate3_desc' => $model->getBillRate3Desc(),
                'bill_rate3_min_pay' => $model->getBillRate3MinPay(),
                'bill_rate4' => $model->getBillRate4(),
                'bill_rate4_type' => $model->getBillRate4Type(),
                'bill_rate4_tat' => $model->getBillRate4Tat(),
                'bill_rate4_desc' => $model->getBillRate4Desc(),
                'bill_rate4_min_pay' => $model->getBillRate4MinPay(),
                'bill_rate5' => $model->getBillRate5(),
                'bill_rate5_type' => $model->getBillRate5Type(),
                'bill_rate5_tat' => $model->getBillRate5Tat(),
                'bill_rate5_desc' => $model->getBillRate5Desc(),
                'bill_rate5_min_pay' => $model->getBillRate5MinPay(),
                'lifetime_minutes' => $model->getLifetimeMinutes(),
                'work_types' => $model->getWorkTypes(),
                'next_job_tally' => $model->getNextJobTally(),
                'act_log_retention_time' => $model->getActLogRetentionTime(),
                'job_prefix' => $model->getJobPrefix(),
                'sr_enabled' => $model->getSrEnabled(),
                'trial' => $model->getTrialStatus(),
                'auto_list_refresh_interval' => $model->getAccJobRefreshInterval(),
                'transcribe_remarks' => $model->getTranscribeRemarks(),
                'comp_mins' => $model->getCompMins(),
                'payment_id'=>$model->getPaymentId(),
                'profile_id'=>$model->getProfileId()
            ));
            return $statement->rowCount();
        } catch (\PDOException) {
            return 0;
        }
    }

    public function deleteModel(int $id): int
    {
        $statement = "
            DELETE FROM accounts
            WHERE acc_id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException) {
            return 0;
        }
    }

    public function findModel($id): array|null
    {
        $statement = "
            SELECT 
                   acc_id,
                   enabled,
                   billable,
                   acc_id,
                   enabled,
                   billable,
                   acc_name,
                   acc_retention_time,
                   subscription_type,
                   acc_creation_date,
                   bill_rate1,
                   bill_rate1_type,
                   bill_rate1_tat,
                   bill_rate1_desc,
                   bill_rate1_min_pay,
                   bill_rate2,
                   bill_rate2_type,
                   bill_rate2_tat,
                   bill_rate2_desc,
                   bill_rate2_min_pay,
                   bill_rate3,
                   bill_rate3_type,
                   bill_rate3_tat,
                   bill_rate3_desc,
                   bill_rate3_min_pay,
                   bill_rate4,
                   bill_rate4_type,
                   bill_rate4_tat,
                   bill_rate4_desc,
                   bill_rate4_min_pay,
                   bill_rate5,
                   bill_rate5_type,
                   bill_rate5_tat,
                   bill_rate5_desc,
                   bill_rate5_min_pay,
                   comp_mins,
                   lifetime_minutes,
                   profile_id,
                   payment_id,
                   work_types,
                   next_job_tally,
                   act_log_retention_time,
                   job_prefix,
                   sr_enabled,
                   trial,
                   auto_list_refresh,
                   transcribe_remarks,
                   auto_list_refresh_interval
                                      
            FROM
                accounts
            WHERE
                accounts.acc_id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            if($statement->rowCount() > 0)
            {
                return $result;
            }else{
                return null;
            }
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getCount()
    {
        $statement = "
            SELECT 
                count(acc_id) as 'accounts_count'
            FROM
                accounts where enabled = 1;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
//            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $result = $statement->fetch();
            return $result['accounts_count'];

        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getSysAdminAccessCount($adminUID)
    {
        $statement = "
            select count(*) as 'sys_org_access_count' from access where acc_role = 1 and uid = $adminUID
            ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
//            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $result = $statement->fetch();
            return $result['sys_org_access_count'];

        } catch (\PDOException $e) {
            return false;
        }
    }


    public function getMissingSysAccessOrgIDs($adminUID)
    {
        $statement = "
            select acc_id from accounts where enabled = 1 and acc_id not in(select access.acc_id from access where acc_role = 1 and uid = $adminUID);
            ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
            return array_column($statement->fetchAll(\PDO::FETCH_ASSOC), 'acc_id');
//            $result = $statement->fetch();
//            return $result['sys_org_access_count'];
//            return $result;

        } catch (\PDOException $e) {
            return false;
        }
    }

    public function findAltModel($id): array|null
    {
        return null;
    }

    public function findAllModel($page = 1): array|null
    {

        $offset = $this->common->getOffsetByPageNumber($page, $this->limit);

        $statement = "
            SELECT 
                *
            FROM
                accounts
            LIMIT :limit
            OFFSET :offset
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->bindParam(":limit",$this->limit, \PDO::PARAM_INT);
            $statement->bindParam(":offset",$offset, \PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException) {
            return null;
        }
    }

    public function postUpdateCompMinutes($id,$mins)
    {
        $statement = "
            UPDATE 
                accounts
            SET
                comp_mins =  :comp_mins          
            WHERE acc_id = :acc_id
;
        ";

        try {
            $cmlm = $this->getCompMinutes();
            $updatedCompMins = (($cmlm["comp_mins"] - $mins < 0 || $cmlm["lifetime_minutes"] == '0.00')?0:$cmlm["comp_mins"] - $mins);       
            // error_log("Comp Mins from DB: " . $cmlm["comp_mins"],0);
            // error_log("Passed Mins to deduct: " . $mins,0);
            // error_log("Updated Comp Minutes to be passed to DB and Session: " . $updatedCompMins,0);
            // error_log($cmlm["comp_mins"] - $mins);
            // error_log((($cmlm["comp_mins"] - $mins < 0 || $cmlm["lifetime_minutes"] == '0.00')?0:$cmlm["comp_mins"] - $mins),0);
            $statement = $this->db->prepare($statement);
            $statement->execute(array('comp_mins' => $updatedCompMins, 'acc_id' => $id));
            if ($statement->rowCount() > 0) {
                $_SESSION["userData"]["comp_mins"] = $updatedCompMins;
                $this->logger->insertAuditLogEntry($this->API_NAME, "Comp Mins Updated");
                return $this->oKResponse($id, "Comp Mins Updated");
            } else {
                return $this->errorOccurredResponse("Couldn't update account");
            }
        } catch (\PDOException $e) {
            return false;
//            exit($e->getMessage());
        }
    }

    public function getCompMinutes()
    {
        $statement = "
            SELECT 
                lifetime_minutes,comp_mins
            FROM
                accounts
            WHERE acc_id = ?          
;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION["accID"]));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            return false;
//            exit($e->getMessage());
        }
    }
}