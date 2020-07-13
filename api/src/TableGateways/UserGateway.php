<?php

namespace Src\TableGateways;

use PDOException;

require "filters/usersFilter.php";

class UserGateway
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
                   id,
                   first_name,
                   last_name,
                   email,
                   country,
                   country_id,
                   city,
                   city_id,
                   state,
                   registeration_date,
                   last_ip_address,
                   plan_id,
                   account_status,
                   last_login,
                   newsletter,
                   shortcuts,
                   dictionary,
                   email_notification,
                   account,
                   enabled
            FROM
                users
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
                   id,
                   first_name,
                   last_name,
                   email,
                   country,
                   country_id,
                   city,
                   city_id,
                   state,
                   registeration_date,
                   last_ip_address,
                   plan_id,
                   account_status,
                   last_login,
                   newsletter,
                   shortcuts,
                   dictionary,
                   email_notification,
                   account,
                   enabled
            FROM
                users
            WHERE
                id = ?";

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
        if (
            !isset($_POST["enabled"]) ||
            !isset($_POST["billable"]) ||
            !isset($_POST["acc_name"])
        ) {
            return $this->errorOccurredResponse("Invalid Input (1)");
        }

        $accName = $_POST["acc_name"];
        if (
            empty(trim($accName)) ||
            strpos($_POST['acc_name'], '%') !== FALSE || strpos($_POST['acc_name'], '_')
        ) {
            return $this->errorOccurredResponse("Invalid Input (2)");
        }


        $accPrefix = $this->generateNewUserPrefix($accName);
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
            if (empty($input)) {
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
                            users 
                            (
                             " . $fields . " job_prefix
                             ) 
                         VALUES 
                                (" . $valsQMarks . "?)";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute($valsArray);

            if ($statement->rowCount() > 0) {
                return $this->oKResponse($this->db->lastInsertId(), "User Created");
            } else {
                return $this->errorOccurredResponse("Couldn't Create User");
            }

        } catch (PDOException $e) {
//            die($e->getMessage());
            return false;
        }
    }

    public function updateUser($id)
    {
        parse_str(file_get_contents('php://input'), $put);

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
                        users 
                        SET 
                             " . $valPairs . " 
                        WHERE 
                            id = ?";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute($valsArray);

            if ($statement->rowCount() > 0) {
                return $this->oKResponse($id, "User Updated");
            } else {
                return $this->errorOccurredResponse("Couldn't update user or no changes were found to update");
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
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}