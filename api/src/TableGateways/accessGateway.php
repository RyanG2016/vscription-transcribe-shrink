<?php

namespace Src\TableGateways;

require "filters/accessFilter.php";

class accessGateway
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
                access.*,
                a.acc_name,
                r.role_name,
                r.role_desc,
                u.email
            FROM
                access
            
            INNER JOIN accounts a on access.acc_id = a.acc_id
            INNER JOIN roles r on access.acc_role = r.role_id
            INNER JOIN users u on access.uid = u.id
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

    public function findAllOut()
    {
        $filter = parseParams();


        $statement = "
            SELECT 
                access.*,
                a.acc_name,
                r.role_name,
                r.role_desc,
                u.email
            FROM
                access
            
            INNER JOIN accounts a on access.acc_id = a.acc_id
            INNER JOIN roles r on access.acc_role = r.role_id
            INNER JOIN users u on access.uid = u.id
            where access.uid = ?
            " . $filter . ";";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION["uid"]));
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


    public function checkAccountAccessPermission($acc_id)
    {

        if (strpos($acc_id, '%') !== FALSE) {
            return $this->errorOccurredResponse("Invalid Input (5-ACG)");
        }


        $statement = "
            SELECT 
                access.*,
                a.acc_name,
                r.role_name,
                r.role_desc,
                u.email
            FROM
                access
            
            INNER JOIN accounts a on access.acc_id = a.acc_id
            INNER JOIN roles r on access.acc_role = r.role_id
            INNER JOIN users u on access.uid = u.id
            where access.uid = ?
            AND access.acc_id = ?
            AND access.acc_role in (1,2) 
            ;";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION["uid"], $acc_id));
//            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $result = $statement->fetch();
            if($statement->rowCount() > 0) {  // user access exists
                return true;
            }else{
                return false;
            }

        } catch (\PDOException $e) {
//            exit($e->getMessage());
            return false;
        }
    }

    public function findAndSetOutAccess()
    {

        // Required Fields
        if (
            !isset($_POST["acc_id"]) ||
            !isset($_POST["acc_role"])
        ) {
            return $this->errorOccurredResponse("Invalid Input, required fields missing (1-OT)");
        }

        if(!sqlInjectionCheckPassed($_POST)){
            return $this->errorOccurredResponse("Invalid Input (505-OT)");
        }


        $statement = "
            SELECT 
                access.*,
                a.acc_name,
                r.role_name,
                r.role_desc,
                u.email
            FROM
                access
            
            INNER JOIN accounts a on access.acc_id = a.acc_id
            INNER JOIN roles r on access.acc_role = r.role_id
            INNER JOIN users u on access.uid = u.id
            where access.uid = ? and access.acc_id = ? and access.acc_role = ? ;";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute( array($_SESSION["uid"], $_POST["acc_id"], $_POST["acc_role"]) );
//            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $result = $statement->fetch();
            if($statement->rowCount() > 0) {  // user access exists check to prevent js altering
                // access exists
                $_SESSION['accID'] = $result["acc_id"];
                $_SESSION['role'] = $result["acc_role"];
                $_SESSION['acc_name'] = $result["acc_name"];
                $_SESSION['role_desc'] = $result["role_desc"];
                $_SESSION['landed'] = true;
                return $this->oKResponse(null, "Role changed successfully");

            }else{
                return $this->errorOccurredResponse("You don't have permission for this role.");
            }
//            return $result;
        } catch (\PDOException $e) {
            return $this->errorOccurredResponse("Couldn't set role (4-OT)");
//            exit($e->getMessage());
        }
    }

    public function find($id)
    {

        $statement = "
            SELECT 
                access.*,
                a.acc_name,
                r.role_name
            FROM
                access
            
            INNER JOIN accounts a on access.acc_id = a.acc_id
            INNER JOIN roles r on access.acc_role = r.role_id
            where access.access_id = ?
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

    public function insertAccessRecord()
    {
        if (
            !isset($_POST["acc_id"]) ||
            !isset($_POST["uid"]) ||
            !isset($_POST["acc_role"])
        ) {
            return $this->errorOccurredResponse("Invalid Input, required fields missing (7)");
        }

        if(!sqlInjectionCheckPassed($_POST))
        {
            return $this->errorOccurredResponse("Invalid Input (7505)");
        }

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

        // insert to DB //
        $statement = "INSERT
                        INTO 
                            access 
                            (
                             " . $fields . "
                             ) 
                         VALUES 
                                (" . $valsQMarks . ")";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute($valsArray);

            if ($statement->rowCount()) {
                return $this->oKResponse($this->db->lastInsertId(), "Access Permission Created");
            } else {
                return $this->errorOccurredResponse("Couldn't Create Permission");
            }
//            return $statement->rowCount();
        } catch (\PDOException $e) {
            return $this->errorOccurredResponse("Couldn't Create Permission (2)");
        }

    }


    public function updateAccess($id)
    {
        parse_str(file_get_contents('php://input'), $put);

        if(!sqlInjectionCheckPassed($put))
        {
            return $this->errorOccurredResponse("Invalid Input (7505)");
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
                        access 
                        SET 
                            " . $valPairs . "  
                        WHERE 
                            access_id = ?";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute($valsArray);

            if ($statement->rowCount() > 0) {
                return $this->oKResponse($id, "Access Updated");
            } else {
                return $this->errorOccurredResponse("Couldn't update or no changes were found to update");
            }

        } catch (PDOException $e) {
            return $this->errorOccurredResponse("Couldn't Update Permission (2)");
//            return false;
        }
    }


    public function delete($id)
      {
          $statement = "
              DELETE FROM access
              WHERE access_id = :id;
          ";

          try {
              $statement = $this->db->prepare($statement);
              $statement->execute(array('id' => $id));
              return $statement->rowCount();
          } catch (\PDOException $e) {
              exit($e->getMessage());
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
}