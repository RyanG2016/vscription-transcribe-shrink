<?php

namespace Src\TableGateways;
use Src\TableGateways\CityGateway;
use Src\TableGateways\CountryGateway;
use Src\TableGateways\accessGateway;
use Src\TableGateways\tokenGateway;
use Src\System\Mailer;
include_once "common.php";

class SignupGateway
{

    private $db = null;
    private $cityGateway = null;
    private $CountryGateway = null;
    private $accessGateway = null;
    private $tokenGateway = null;
    private $mailer;

    public function __construct($db)
    {
        $this->db = $db;
        $this->cityGateway = new CityGateway($db);
        $this->CountryGateway = new CountryGateway($db);
        $this->accessGateway = new accessGateway($db);
        $this->tokenGateway = new tokenGateway($db);
        $this->mailer = new Mailer($db);
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
     * Sign-up users from POST request made to signup API endpoint
     * hello world
     * @return string Response with header and body @common.php
     */
    public function signUp()
    {
        $email = $_POST["email"];
        $pass = $_POST["password"];
        $fname = $_POST["fname"];
        $lname = $_POST["lname"];
        $ref = isset($_POST["ref"])?$_POST["ref"]:false;
        $countryID = $_POST["countryID"];
        $stateID = $_POST["stateID"]?$_POST["stateID"]:null;
        $city = isset($_POST["city"])?$_POST["city"]:null;
        $accName = isset($_POST["accname"]) ? (empty(trim($_POST["accname"]))? false: $_POST["accname"]) :false;

        // DEBUG simulate error
//        return generateApiHeaderResponse("You already have an account, login instead?", true,false,301);
//        return generateApiHeaderResponse("Couldn't sign you up, please contact system admin", true);

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
            $lastInsertedUID = $this->db->lastInsertId();
            $count =  $statement->rowCount();
            if($count != 0)
            {
                $this->mailer->sendEmail(5, $email);

                // check if there's a pending typist invite (ref)
                if($ref)
                {
                    // get accID
                    $tokenData = $this->tokenGateway->find($ref);
                    if($tokenData)
                    {
                        $accID = $tokenData["extra1"];

                        // accept invite
                        $this->accessGateway->internalManualInsertAccessRecord(
                            $accID,
                            $lastInsertedUID,
                            $_POST["email"],
                            3);

                        $this->tokenGateway->expireToken($tokenData["id"]);

                        return generateApiHeaderResponse("Signed up successfully, \nTypist invitation accepted, \n\nPlease verify your email address before logging in",
                            false,
                            array("id"=>$lastInsertedUID));
                    }else{
                        // token not found or expired
                        return generateApiHeaderResponse("Signed up successfully, please verify your email address before logging in, couldn't accept typist invitation (Invalid or Expired token)",
                            false,
                            array("id"=>$lastInsertedUID));
                    }
                }

                return generateApiHeaderResponse("Signed up successfully, please verify your email address before logging in",
                    false,
                    array("id"=>$lastInsertedUID));
            }else{
                return generateApiHeaderResponse("Couldn't sign you up, please contact system admin", true);
            }
        } catch (\PDOException $e) {
            return generateApiHeaderResponse("Couldn't sign you up, please contact system admin (2)", true);
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
                "Signup API",
                $activity,
                getIP()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return false;
        }

    }

}