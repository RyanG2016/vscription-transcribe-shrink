<?php

namespace Src\TableGateways;
use Src\TableGateways\CityGateway;
use Src\TableGateways\CountryGateway;
include "common.php";

class SignupGateway
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