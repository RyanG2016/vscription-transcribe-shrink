<?php

namespace Src\TableGateways;
use Src\Models\Account;
use Src\TableGateways\CityGateway;
use Src\TableGateways\accessGateway;
use Src\TableGateways\tokenGateway;
use Src\TableGateways\AccountGateway;
use Src\System\Mailer;
include_once "common.php";

class SignupGateway
{

    private $db = null;
    private $cityGateway = null;
    private $accessGateway = null;
    private $tokenGateway = null;
    private $accountGateway = null;
    private $mailer;

    public function __construct($db)
    {
        $this->db = $db;
        $this->cityGateway = new CityGateway($db);
        $this->accessGateway = new accessGateway($db);
        $this->tokenGateway = new tokenGateway($db);
        $this->accountGateway = new AccountGateway($db);
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
        $country = $_POST["country"];
//        $stateID = $_POST["stateID"]?$_POST["stateID"]:null;
//        $city = isset($_POST["city"])?$_POST["city"]:null;
//        $address = $_POST["address"];
//        $address = isset($_POST["address"]) ? (empty(trim($_POST["address"]))? "": $_POST["address"]) :'';
        $accName = isset($_POST["accname"]) ? (empty(trim($_POST["accname"]))? false: $_POST["accname"]) :false;

        // DEBUG simulate error
//        return generateApiHeaderResponse("You already have an account, login instead?", true,false,301);
//        return generateApiHeaderResponse("Couldn't sign you up, please contact system admin", true);

        $statement = "INSERT INTO 
                users(
                  first_name,
                  last_name,
                  email,
                  password,
                  city,
                  country,

                  state,
                  registeration_date,
                  email_notification,
                  enabled,
                  account_status,
                  trials,
                  newsletter,
                  last_ip_address,
                  address
                  ) 
                values (
                    :first_name,
                    :last_name,
                    :email,
                    :password,
                    :city,
                    :country,
                    :state,
                    :registeration_date,
                    :email_notification,
                    :enabled,
                    :account_status,
                    :trials,
                    :newsletter,
                    :last_ip_address,
                    :address
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
                'city' => null,
                'country' => $country,
//                'state_id' => null,
                'state' => null,
                'registeration_date' => date("Y-m-d H:i:s"),
                'email_notification' => 1,
                "enabled" => 1,
                "account_status" => 5,
                "trials" => 0,
                "newsletter" => 1,
                "last_ip_address" => getIP(),
                "address" => ""
            ));
            $lastInsertedUID = $this->db->lastInsertId();
            $count =  $statement->rowCount();
            if($count != 0)
            {
                $this->mailer->sendEmail(5, $email);

                if($accName)
                {
                    $this->createClientAdminAccount($accName, $email, $lastInsertedUID);
                }

                // check if there's a pending typist invite (ref)
                if($ref)
                {
                    // get accID
                    $tokenData = $this->tokenGateway->find($ref);
                    if($tokenData)
                    {
                        $accID = $tokenData["extra1"];
                        $role = $tokenData["extra2"];

                        // accept invite
                        $this->accessGateway->internalManualInsertAccessRecord(
                            $accID,
                            $lastInsertedUID,
                            $_POST["email"],
                            $role);

                        $this->tokenGateway->expireToken($tokenData["id"]);

                        return generateApiHeaderResponse("Signup Successful."
                            ."<br>We have sent an email to ".$email.",<br>please click the link provided to verify your email address.".
                              " <br><br>Invitation for ". Account::withID($accID, $this->db)->getAccName() ." accepted.",
                            false,
                            array("id"=>$lastInsertedUID));
                    }else{
                        // token not found or expired
                        return generateApiHeaderResponse("<br>Signup Successful."
                        ."<br>We have sent an email to ".$email.",<br>please click the link provided to verify your email address.".
                        "<br><br>couldn't accept invitation (Invalid or Expired token)",
                            false,
                            array("id"=>$lastInsertedUID));
                    }
                }
                return generateApiHeaderResponse("Signup Successful."
                    ."<br>We have sent an email to ".$email.",<br>please click the link provided to verify your email address.",
                    false,
                    array("id"=>$lastInsertedUID));
            }else{
                return generateApiHeaderResponse("Couldn't sign you up, please contact system admin", true);
            }
        } catch (\PDOException $e) {
            return generateApiHeaderResponse("Couldn't sign you up, please contact system admin (2) " . $e->getMessage(), true);
        }


    }

    /**
     * @internal used by signup function to create and associate Client Admin Account with a newly created user
     * if the accName is present
     */
    private function createClientAdminAccount($accName, $email, $uid){
        if($accName)
        {
            $_SESSION['uid'] = $uid;
            $_SESSION['uEmail'] = $email;
            $response = $this->accountGateway->createNewClientAdminAccount($accName);
            $debugresponse =1;
            return json_decode($response["body"], true)["error"];
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