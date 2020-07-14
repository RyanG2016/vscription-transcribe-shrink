<?php

namespace Src\TableGateways;
include "common.php";
class LoginGateway
{

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function find($email, $pass)
    {
            $cTime = strtotime(date("Y-m-d H:i:s"));

        $statement = "
            SELECT 
                id,first_name, last_name, email, password, plan_id, account_status, last_login, trials, unlock_time, account
            FROM
                users
            WHERE email = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($email));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if(sizeof($result) == 1) // user exists
            {
                $user = $result[0];
                $verified = password_verify($pass, $user["password"]); /** check password */

                /** Check account status **/
                /** -> Account is Active **/
                if($user['account_status'] == 1) { // Active
                    if($verified) {
                        $this->unlockUserAccount($user["id"]);          // to reset trials upon successful login
                        $this->sessionLogin($user);                     // set session variables as logged in + log inside
                        return array("error" => false, "msg" => "Logged In");
                    }
                    else {
                        $this->increaseTrials($user);
                        $extra = "";
                        if($user["trials"]+1 == 5) {
                            $extra = " - Account Locked";
                            $this->insertAuditLogEntry($user['id'], "Account Locked.");
                        }else {
                            $this->insertAuditLogEntry(0, "Incorrect login attempt. (".($user["trials"]+1).")");
                        }
                        return array("error" => true, "msg" => "Incorrect login attempt (" . ($user["trials"]+1) . ")".$extra);
                    }
                }

                /** -> Account is NOT Active **/
                else{
                    switch ($user['account_status'])
                    {
                        case 5: // email verification required
                            $this->insertAuditLogEntry(0, "Failed login - Pending Verification");
                            return array("error" => true, "msg" => "Pending Email Verification.", "code" => 5);
                            break;
                        case 0: // Account is locked

                            $unlockTime = strtotime($user['unlock_time']);
                            if($cTime > $unlockTime){ // unlock time passed
                                // set account status to 1 and reset trials -> check password match
                                $this->unlockUserAccount($user["id"]);
                                $this->insertAuditLogEntry($user["account"], "User account unlocked.");
                                // check for password
                                if($verified){
                                    $this->sessionLogin($user); // set session variables as logged in + audit log inside

                                }else{
                                    return array("error" => true, "msg" => "Incorrect Password"); // first incorrect attempt after account unlock
                                }
                            }

                            return array("error" => true, "msg" => "Account is locked - unlocks on: " . $user['unlock_time']);
                            break;

                    }
                }

            }else{
                return array("error" => true, "msg" => "We couldnt find your account.", "code" => 404);
            }

        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
        return array("error" => true, "msg" => "Incorrect Password");
    }

    public function sessionLogin($row){
        $_SESSION['uid'] = $row['id'];
        $_SESSION['fname'] = $row['first_name'];
        $_SESSION['lname'] = $row['last_name'];
        $_SESSION['uEmail'] = $row["email"];
//        $_SESSION['role'] = $row['plan_id'];
//        $_SESSION['accID'] = $row['account'];

        $_SESSION['loggedIn'] = true;
        $_SESSION['lastPing'] = date("Y-m-d H:i:s");
        isset($_REQUEST['rememberme'])?$_SESSION['remember']=true:$_SESSION['remember']=false;
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
        $timestamp = strtotime(date("Y-m-d H:i:s")) + 60*60;
        $onehourahead = date("Y-m-d H:i:s", $timestamp);

        if($user['trials'] < 4){
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

        }

        else{ // trials == 4 -> update to 5 and lock the account
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

   /* public function insert(array $input)
    {
        $statement = "
            INSERT INTO files 
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

    function insertAuditLogEntry($accId, $activity) {
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
            exit($e->getMessage());
        }

    }

}