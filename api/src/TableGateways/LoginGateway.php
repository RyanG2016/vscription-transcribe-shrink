<?php

namespace Src\TableGateways;
use Src\TableGateways\CityGateway;
use Src\TableGateways\CountryGateway;
include "common.php";

class LoginGateway
{

    private $db = null;
    private $cityGateway = null;
    private $CountryGateway = null;

    public function __construct($db)
    {
        $this->db = $db;
        $this->cityGateway = new CityGateway($db);
        $this->CountryGateway = new CountryGateway($db);
    }

    public function find($email, $pass)
    {
        $cTime = strtotime(date("Y-m-d H:i:s"));

        $statement = "
            SELECT
                id,first_name, last_name, email, password, plan_id, account_status, last_login, trials, unlock_time,
                account, def_access_id, users.enabled, a.acc_role, a.acc_id, r.role_desc, a2.acc_name
            FROM
                users
            LEFT JOIN access a on users.def_access_id = a.access_id
            LEFT JOIN roles r on a.acc_role = r.role_id
            LEFT JOIN accounts a2 on a.acc_id = a2.acc_id
            WHERE email = ?;
        ";

        // $_SESSION['accID'] = $result["acc_id"];
        // $_SESSION['role'] = $result["acc_role"];
        // $_SESSION['acc_name'] = $result["acc_name"];
        // $_SESSION['role_desc'] = $result["role_desc"];
        // $_SESSION['landed'] = true;

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($email));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if (sizeof($result) == 1) // user exists
            {
                $user = $result[0];
                $verified = password_verify($pass, $user["password"]);
                /** check password */

                /** Check account status **/
                if ($user['enabled']) {
                    /** -> Account is Active **/
                    if ($user['account_status'] == 1) { // Active
                        if ($verified) {
                            $this->unlockUserAccount($user["id"]);          // to reset trials upon successful login
                            $this->sessionLogin($user);                     // set session variables as logged in + log inside
                            return array("error" => false, "msg" => "Logged In");
                        } else {
                            $this->increaseTrials($user);
                            $extra = "";
                            if ($user["trials"] + 1 == 5) {
                                $extra = " - Account Locked";
                                $this->insertAuditLogEntry($user['id'], "Account Locked.");
                            } else {
                                $this->insertAuditLogEntry(0, "Incorrect login attempt. (" . ($user["trials"] + 1) . ")");
                            }
                            return array("error" => true, "msg" => "Incorrect login attempt (" . ($user["trials"] + 1) . ")" . $extra);
                        }
                    } /** -> Account is NOT Active **/
                    else {
                        switch ($user['account_status']) {
                            case 5: // email verification required
                                $this->insertAuditLogEntry(0, "Failed login - Pending Verification");
                                return array("error" => true, "msg" => "Pending Email Verification.", "code" => 5);
                                break;
                            case 0: // Account is locked

                                $unlockTime = strtotime($user['unlock_time']);
                                if ($cTime > $unlockTime) { // unlock time passed
                                    // set account status to 1 and reset trials -> check password match
                                    $this->unlockUserAccount($user["id"]);
                                    $this->insertAuditLogEntry($user["account"], "User account unlocked.");
                                    // check for password
                                    if ($verified) {
                                        $this->sessionLogin($user); // set session variables as logged in + audit log inside

                                    } else {
                                        return array("error" => true, "msg" => "Incorrect Password"); // first incorrect attempt after account unlock
                                    }
                                }

                                return array("error" => true, "msg" => "Account is locked - unlocks on: " . $user['unlock_time']);
                                break;

                        }
                    }
                } else {
                    $this->insertAuditLogEntry(0, "Failed login - Disabled");
                    return array("error" => true, "msg" => "Account is disabled.");
                }

            } else {
                return array("error" => true, "msg" => "We couldn't find your account.", "code" => 404);
            }

        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
        return array("error" => true, "msg" => "Incorrect Password");
    }

    public function sessionLogin($row)
    {
        $_SESSION['uid'] = $row['id'];
        $_SESSION['fname'] = $row['first_name'];
        $_SESSION['lname'] = $row['last_name'];
        $_SESSION['uEmail'] = $row["email"];

        if ($row["def_access_id"] != null) {
            $_SESSION['accID'] = $row["acc_id"];
            $_SESSION['role'] = $row["acc_role"];
            $_SESSION['acc_name'] = $row["acc_name"];
            $_SESSION['role_desc'] = $row["role_desc"];
            $_SESSION['landed'] = true;
        }

//        $_SESSION['role'] = $row['plan_id'];
//        $_SESSION['accID'] = $row['account'];

        $_SESSION['loggedIn'] = true;
        $_SESSION['lastPing'] = date("Y-m-d H:i:s");
        isset($_REQUEST['rememberme']) ? $_SESSION['remember'] = true : $_SESSION['remember'] = false;
        $this->insertAuditLogEntry($row['account'], "Login");
    }

    // set account status to 1 and reset trials
    public function unlockUserAccount($id)
    {
        $statement = "
            UPDATE users
            SET 
                unlock_time = null,
                account_status = 1,
                trials = 0,
                last_login = ?
            
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(date("Y-m-d H:i:s"), $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    // set account status to 1 and reset trials
    public function increaseTrials($user)
    {
        $timestamp = strtotime(date("Y-m-d H:i:s")) + 60 * 60;
        $onehourahead = date("Y-m-d H:i:s", $timestamp);

        if ($user['trials'] < 4) {
            $statement = "
                UPDATE users
                SET 
                    unlock_time = null,
                    account_status = 1,
                    trials = trials+1,
                    last_login = ?
                
                WHERE id = ?;
            ";
            try {
                $statement = $this->db->prepare($statement);
                $statement->execute(array(
                    date("Y-m-d H:i:s"),
                    $user["id"]
                ));
                return $statement->rowCount();
            } catch (\PDOException $e) {
                exit($e->getMessage());
            }

        } else { // trials == 4 -> update to 5 and lock the account
            $statement = "
                UPDATE users
                SET 
                    unlock_time = ?,
                    account_status = 0,
                    trials = 5,
                    last_login = ?
                
                WHERE id = ?;
            ";

            try {
                $statement = $this->db->prepare($statement);
                $statement->execute(array(
                    $onehourahead,
                    date("Y-m-d H:i:s"),
                    $user["id"]
                ));

                return $statement->rowCount();
            } catch (\PDOException $e) {
                exit($e->getMessage());
            }
        }
    }

    /**
     * Checks if the given email already exists
     * @param $email string user email address
     * @return bool true -> exist, false -> doesn't exist
     */
    public function userExist($email)
    {

        $statement = "
            SELECT
                id
            FROM
                users
            WHERE email = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($email));
//            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return true;
//            exit($e->getMessage());
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

    /**
     * Sign-up users from POST request made to login API endpoint
     * hello world
     * @return string Response with header and body @common.php
     */
    public function signUp()
    {
        $email = $_POST["email"];
        $pass = $_POST["password"];
        $fname = $_POST["fname"];
        $lname = $_POST["lname"];
        $countryID = $_POST["countryID"];
        $stateID = $_POST["stateID"]?$_POST["stateID"]:null;
        $city = isset($_POST["city"])?$_POST["city"]:null;

        // state check
        if($countryID == 204 || $countryID == 203)
        {
            if($stateID == null) return generateApiHeaderResponse("Incorrect state input", true);
            $stateArr = $this->cityGateway->getCity($stateID);
            if(sizeof($stateArr) == 0)
            {
                return generateApiHeaderResponse("Incorrect state input", true);
            }else{
                if($stateArr["country"] !== $countryID){
                    return generateApiHeaderResponse("State doesn't match given country", true);
                }
            }
            $stateName = $stateArr["city"];
        }else{
            // country check only
            if(!$this->CountryGateway->find($countryID))
            {
                return generateApiHeaderResponse("Invalid Input (C)", true);
            }
            $stateID = null;
            $stateName = null;
        }

        $statement = "INSERT INTO 
                users(
                  first_name,
                  last_name,
                  email,
                  password,
                  city,
                  state_id,
                  country_id,
                  state,
                  registeration_date,
                  email_notification,
                  enabled,
                  account_status,
                  trials,
                  newsletter,
                  last_ip_address,
                  plan_id
                  ) 
                values (
                    :first_name,
                    :last_name,
                    :email,
                    :password,
                    :city,
                    :state_id,
                    :country_id,
                    :state,
                    :registeration_date,
                    :email_notification,
                    :enabled,
                    :account_status,
                    :trials,
                    :newsletter,
                    :last_ip_address,
                    :plan_id
                  )
                  
                  ;";
//                  VALUES(?,?,?,?,?)";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'first_name' => $fname,
                'last_name' => $lname,
                'email' => $email,
                'password' => password_hash($pass,PASSWORD_BCRYPT),
                'city' => $city,
                'state_id' => $stateID,
                'country_id' => $countryID,
                'state' => $stateName,
                'registeration_date' => date("Y-m-d H:i:s"),
                'email_notification' => 1,
                "enabled" => 1,
                "account_status" => 5,
                "trials" => 0,
                "newsletter" => 1,
                "last_ip_address" => getIP(),
                "plan_id" => 2
            ));
            $count =  $statement->rowCount();
            if($count != 0)
            {
                $token = $this->generateToken($email, 5);
                sendEmail(5, $email, $token);

                return generateApiHeaderResponse("Signed up successfully, please verify your email address before trying to login",
                    false,
                    array("id"=>$this->db->lastInsertId()));
            }else{
                return generateApiHeaderResponse("Couldn't sign you up, please contact system admin", true);
            }
        } catch (\PDOException $e) {
            return generateApiHeaderResponse("Couldn't sign you up, please contact system admin (2)", true);
        }


    }

    /**
     * Generates a random 78 length token, inserts it to tokens table
     * @param $reasonCode 5 -> email verification |
     * @return string token or (false) if failed
     */
    public function generateToken($email ,$reasonCode)
    {
        $token = null;

        while(true)
        {
            $token = genToken();
            if($token != 0)
            {
                break;
            }
        }

        $statement = "
        insert into 
            tokens(
                   email,
                   identifier,
                   used,
                   token_type) 
               values(?, ?, ?, ?)
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(
                array(
                    $email,
                    $token,
                    0,
                    $reasonCode
                )
            );
            if($statement->rowCount() > 0)
            {
                return $token;
            }
            return false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Adds a log record to act_log
     * @param $accId
     * @param $activity
     * @return bool (boolean) log recorded
     */
    function insertAuditLogEntry($accId, $activity)
    {
        //INSERT AUDIT LOG DATA

        $statement = "INSERT INTO act_log(username, acc_id, actPage, activity, ip_addr) VALUES(?,?,?,?,?)";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                $_SERVER["PHP_AUTH_USER"],
                $accId,
                "Login API",
                $activity,
                getIP()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return false;
        }

    }

}