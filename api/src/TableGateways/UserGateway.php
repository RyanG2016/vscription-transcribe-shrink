<?php

namespace Src\TableGateways;

use PDOException;
use Src\TableGateways\logger;

require "filters/usersFilter.php";
include_once "common.php";

class UserGateway
{

    private $db;
    private $logger;
    private $API_NAME;

    public function __construct($db)
    {
        $this->db = $db;
        $this->logger = new logger($db);
        $this->API_NAME = "Users";
    }

    public function findAll()
    {
        $filter = parseUserParams(true);

        $statement = "
            SELECT 
                   users.id,
                    users.first_name,
                    users.last_name,
                    users.email,
                    users.country_id,
                    countries.country,
                    users.city,
                    users.state_id,
                    users.state,
                    users.registeration_date,
                    users.last_ip_address,
                    users.plan_id,
                    users.account_status,
                    users.last_login,
                    users.newsletter,
                    users.shortcuts,
                    users.dictionary,
                    users.email_notification,
                    users.account,
                    users.enabled,
                    users.def_access_id,
                    accounts.acc_name,
                    access.acc_role,
                    cities.city as `state_ref`
                                      
            FROM
                users
            INNER JOIN countries ON users.country_id = countries.id 
            LEFT JOIN cities ON users.state_id = cities.id
            LEFT JOIN access ON users.def_access_id = access.access_id
            LEFT JOIN accounts ON access.acc_id = accounts.acc_id
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


    /**
     * Retrieves typists emails for invitation dropdown for client administrators management screen
     * @return mixed
     */
    public function getTypists()
    {
        /*$statement = "
            SELECT 
                   users.id,
                    users.email,
                    users.plan_id,
                    users.account_status,
                    users.email_notification,
                    accounts.acc_name as 'admin_of',
                    access.acc_role                                      
            FROM
                users
            LEFT JOIN access ON users.id = access.uid
            LEFT JOIN accounts ON users.account = accounts.acc_id
        where users.enabled = 1 
                and account_status = 1
                and access.acc_role != 3
        
        ;"; // todo identify typists ?? plan_id re-utilize maybe?*/

        $statement = "
            select users.id, email
            from users
            where users.account_status = 1 and users.enabled = 1 and
                (
                    select count(access.acc_id) from access where access.acc_id = ? and uid = users.id and (acc_role = 3 OR acc_role = 6)
                ) != 1
            group by users.id order by users.id"; // todo identify typists ?? plan_id re-utilize maybe?
        //group by users.email
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION["accID"]));
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

    /**
     * [Mail] [Mailing List] Retrieves current logged in Client Account's typists emails for mailing list for job updates
     * @return mixed
     */
    public function getCurrentTypistsForJobUpdates()
    {

        $statement = "select u.email
                        FROM access
                    INNER JOIN users u on access.uid = u.id
                    where acc_id = ? and acc_role = 3 and email_notification = 1";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION["accID"]));
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * [Mail] [Mailing List] Retrieves Account Admin Email to inform of job completion
     * @return mixed
     */
    public function getClientAccAdminsEmailForJobUpdates()
    {

        $statement = "select u.email
            from access
            INNER JOIN users u on access.uid = u.id
            where acc_id = ? and acc_role = 2 and email_notification = 1";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION["accID"]));
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {

        $statement = "
            SELECT 
                   users.id,
                    users.first_name,
                    users.last_name,
                    users.email,
                    users.country_id,
                    countries.country,
                    users.city,
                    users.state_id,
                    users.state,
                    users.registeration_date,
                    users.last_ip_address,
                    users.plan_id,
                    users.account_status,
                    users.last_login,
                    users.newsletter,
                    users.shortcuts,
                    users.dictionary,
                    users.email_notification,
                    users.account,
                    users.enabled,
                    users.def_access_id,
                   cities.city as `state_ref`
                                      
            FROM
                users
            INNER JOIN countries ON users.country_id = countries.id
            LEFT JOIN cities ON users.state_id = cities.id
            WHERE
                users.id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            return false;
//            exit($e->getMessage());
        }
    }

    /**
     * retrieves user data
     * @param $email string user email address
     * @return string JSON of user object
     */
    public function getUserByEmail($email)
    {

        $statement = "
            SELECT 
                   users.id,
                    users.first_name,
                    users.last_name,
                    users.email,
                    users.country_id,
                    countries.country,
                    users.city,
                    users.state_id,
                    users.state,
                    users.registeration_date,
                    users.last_ip_address,
                    users.plan_id,
                    users.account_status,
                    users.last_login,
                    users.newsletter,
                    users.shortcuts,
                    users.dictionary,
                    users.email_notification,
                    users.account,
                    users.enabled,
                   cities.city as `state_ref`
                                      
            FROM
                users
            INNER JOIN countries ON users.country_id = countries.id
            LEFT JOIN cities ON users.state_id = cities.id
            WHERE
                users.email = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($email));
//            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $statement->fetch();
//            return $result;
        } catch (\PDOException $e) {
            return false;
//            exit($e->getMessage());
        }
    }
    
    public function insertNewUser()
    {
        // Required Fields
        if (
            !isset($_POST["first_name"]) ||
            !isset($_POST["last_name"]) ||
            !isset($_POST["email"]) ||
            !isset($_POST["country_id"]) ||
            !isset($_POST["newsletter"]) ||
            !isset($_POST["enabled"])
        ) {
            return $this->errorOccurredResponse("Invalid Input, required fields missing (1)");
        }

        // Sql Injection Check
        if(!sqlInjectionCreateCheckPassed($_POST))
        {
            return $this->errorOccurredResponse("Invalid Input (505)");
        }

        // Parse post request params/fields

        $fields = "";
        $valsQMarks = "";
        $valsArray = array();
        $i = 0;
        $len = count($_POST);

        foreach ($_POST as $key => $value) {

            // setting all empty params to 0
            if (empty($input)) {
                $input = 0;
            }

            $fields .= "`$key`";
            array_push($valsArray, $value);
            $valsQMarks .= "?";

            if ($i != $len - 1) {
//             not last item add comma
            $fields .= ", ";
            $valsQMarks .= ", ";
            }

            $i++;
        }

        // Optional Fields Calculations //
        // account_status
        $fields .= ", " . "`account_status`";
        $valsQMarks .= ", ?";
        array_push($valsArray, 5);

        // password
        $fields .= ", " . "`password`";
        $valsQMarks .= ", ?";

        $newPass = $this->getNewPasswordWithHash();
        $newPass["pwd"]; // return with the success response todo
        array_push($valsArray, $newPass["hash"]);

        // ip address
        $fields .= ", " . "`last_ip_address`";
        $valsQMarks .= ", ?";
        array_push($valsArray, getIP());


        // todo remove below
        // plan_id hardcode default to typist
        $fields .= ", " . "`plan_id`";
        $valsQMarks .= ", ?";
        array_push($valsArray, 3);



        // insert to DB //
        $statement = "INSERT
                        INTO 
                            users 
                            (
                             " . $fields . "
                             ) 
                         VALUES 
                                (" . $valsQMarks . ")";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute($valsArray);

            if ($statement->rowCount() > 0) {
                $this->logger->insertAuditLogEntry($this->API_NAME, "Created User: " . $_POST["email"]);
                return $this->oKResponse($this->db->lastInsertId(), "User Created");
            } else {
//                return $this->errorOccurredResponse("Couldn't Create User");
                if(strpos($statement->errorInfo()[2], 'Duplicate entry') !== false){
                    return $this->errorOccurredResponse("User Already Exists");
                }else{
                    return $this->errorOccurredResponse("Couldn't Create User");
//                    return $this->errorOccurredResponse("Couldn't Create User" . print_r($statement->errorInfo()));
                }

            }

        } catch (PDOException $e) {
//            die($e->getMessage());
//            return false;
            return $this->errorOccurredResponse("Couldn't Create User (2)");
        }
    }


    /**
     * Updates `account` field in `users` tbl with the user made client admin account ID
     * and sets session variables with the account data
     * @internal
     * @param $accID int client admin account ID made by the user
     * @param $accName string client admin account name
     * @return boolean true -> success | false -> failed to update
     */
    public function internalUpdateUserClientAdminAccount($accID, $accName) {

        // update user access //
        $statement = "UPDATE
                            users
                        SET  
                            account = ?
                        WHERE
                            id = ?
                        ";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                $accID,
                $_SESSION['uid']
            ));

            if ($statement->rowCount() > 0) {

                // setting session variables to bypass the need of logging out and in again
                $_SESSION["adminAccount"] = $accID;
                $_SESSION["adminAccountName"] = $accName;

            return true;
            } else {
                return false;
            }

        } catch (PDOException $e) {
//            die($e->getMessage());
            return false;
        }
    }

    /**
     * Clears def_access_id to null
     * used if a revoke access is invoked on a access entry which is the default for a user (will fail due to foreign key check)
     * @internal
     * @param $uid int user id
     * @return boolean true -> success | false -> failed to update
     */
    public function internalUpdateUserClearDefaultAccess($uid) {

        // update user access //
        $statement = "UPDATE
                            users
                        SET  
                            def_access_id = null
                        WHERE
                            id = ?
                        ";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                $uid
            ));

            return $statement->rowCount();

        } catch (PDOException $e) {
//            die($e->getMessage());
            return false;
        }
    }

    public function updateDefaultAccess()
    {
        // Required Fields
        if (
            !isset($_POST["acc_id"]) ||
            !isset($_POST["acc_role"])
        ) {
            return $this->errorOccurredResponse("Invalid Input, required fields missing (1)");
        }

        // Sql Injection Check
        if(!sqlInjectionUpdateDefAccessCheckPassed($_POST))
        {
            return $this->errorOccurredResponse("Invalid Input (505-UPDEF)");
        }

        $uid = $_SESSION['uid'];

        // allow admin to set default access to a certain user instead of self
        if(isset($_POST["uid"]) && is_numeric($_POST["uid"])) {
            if($_SESSION["role"] == 1) {
                $uid = $_POST["uid"];
            }
        }

        // insert to DB //
        $statement = "SELECT
            access_id
        FROM
            access
        WHERE
            acc_id = ?
        AND
            acc_role = ?
        AND
              uid = ?
        ";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute( array(
                $_POST["acc_id"],
                $_POST["acc_role"],
                $uid
            ) );

            if ($statement->rowCount() > 0) {
                $result = $statement->fetch();
                return $this->setDefaultAccess($result['access_id'], $uid);
            } else {
                return $this->errorOccurredResponse("You don't have permission for this access token.");
            }

        } catch (PDOException $e) {
//            die($e->getMessage());
            return $this->errorOccurredResponse("Couldn't retrieve access token information.");
        }
    }

    // this is executed after updateDefaultAccess checks if the role & acc IDS match for the current logged in user
    public function setDefaultAccess($defAccessID, $uid) {

        // update user access //
        $statement = "UPDATE
                            users
                        SET  
                            def_access_id = ?
                        WHERE
                            id = ?
                        ";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($defAccessID, $uid));

//            if ($statement->rowCount() > 0) {
                return $this->oKResponse(0, "Default Access set successfully");
//            } else {
//                return $this->errorOccurredResponse("Couldn't set default");
//                return $this->errorOccurredResponse("Couldn't set default" .
//                    print_r($statement->errorInfo())
//                );
//            }

        } catch (PDOException $e) {
//            die($e->getMessage());
            return $this->errorOccurredResponse("Couldn't set default (2)");
        }
    }


    public function updateUser($id)
    {
        parse_str(file_get_contents('php://input'), $put);

        // Required Fields
        /*if (
            !isset($put["first_name"]) ||
            !isset($put["last_name"]) ||
            !isset($put["email"]) ||
            !isset($put["country_id"]) ||
            !isset($put["newsletter"]) ||
            !isset($put["enabled"])
        ) {
            return $this->errorOccurredResponse("Invalid Input, required fields missing (31)");
        }*/

        // Sql Injection Check
        if(!sqlInjectionUserCheckPassed($put))
        {
            return $this->errorOccurredResponse("Invalid Input (3505)");
        }

        // Parse post request params/fields
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

        if(isset($put['state'])){

            $valPairs .= ", `state_id` = ";
            array_push($valsArray, null);
            $valPairs .= "?";
        }
        else if(isset($put['state_id'])){
            $valPairs .= ", `state` = ";
            array_push($valsArray, null);
            $valPairs .= "?";
        }

        array_push($valsArray, $id);


        // update DB //
        $statement = "UPDATE
                        users 
                        SET 
                             " . $valPairs . " 
                        WHERE 
                            id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute($valsArray);

            if ($statement->rowCount() > 0) {
                $this->logger->insertAuditLogEntry($this->API_NAME, "Updated User: " . $id);
                return $this->oKResponse($id, "User Updated");
            } else {
                return $this->errorOccurredResponse("Couldn't update user or no changes were found to update");
//                return $this->errorOccurredResponse("Debug " . print_r($statement->errorInfo()) . "\n <br>"
//                . $statement->queryString);
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
            DELETE FROM users
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            $this->logger->insertAuditLogEntry($this->API_NAME, "Deleted user id: " . $id);
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function getNewPasswordWithHash() {

        $password = $this->generateRandomPassword();
        return array(
            "pwd" => $password,
            "hash" => password_hash($password,PASSWORD_BCRYPT)
        );
    }

    function generateRandomPassword($length = 12){
        $chars = "0123456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ";
        return substr(str_shuffle($chars),0,$length);
    }
}