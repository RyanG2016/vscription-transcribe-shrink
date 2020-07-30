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
        $filter = parseParams(true);

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
            exit($e->getMessage());
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
                $_SESSION['uid']
            ) );

            if ($statement->rowCount() > 0) {
                $result = $statement->fetch();
                return $this->setDefaultAccess($result['access_id']);
            } else {
                return $this->errorOccurredResponse("You don't have permission for this access token.");
            }

        } catch (PDOException $e) {
//            die($e->getMessage());
            return $this->errorOccurredResponse("Couldn't retrieve access token information.");
        }
    }

    // this is executed after updateDefaultAccess checks if the role & acc IDS match for the current logged in user
    public function setDefaultAccess($defAccessID) {

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
            $statement->execute(array($defAccessID, $_SESSION['uid']));

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
        if(!sqlInjectionCheckPassed($put))
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