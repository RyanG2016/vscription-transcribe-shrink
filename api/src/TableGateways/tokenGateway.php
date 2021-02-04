<?php

namespace Src\TableGateways;
use PDOException;
use Src\Helpers\common;
use Src\Models\BaseModel;
use Src\TableGateways\accessGateway;

include_once "common.php";


class tokenGateway implements GatewayInterface
{

    private $db;
    private $accessGateway;
    private $common;
    private $limit = 10;

    public function __construct($db)
    {
        $this->db = $db;
        $this->accessGateway = new accessGateway($db);
        $this->common = new common();
    }

    /**
     * @param $token string token to parse
     * @return array PHPArrayResponse -><br><u>codes:</u><br>
     *
     * &nbsp;&nbsp; 0: invalid <br>
     * 498: expired or doesn't exist
     * @internal internally used by accept.php
     */
    public function evaluateToken($token)
    {

        if (empty($token)) {
            return generatePHPArrayResponse("Invalid token", true, 0);
        }

        $row = $this->find($token);
        if (!$row) {
            return generatePHPArrayResponse("Token expired or doesn't exist", true, 498);
        }

        // 6: typist invitation
        switch ($row["token_type"]) {

            case 6:
                $access_id = $row["extra1"];
                $role = $row["extra2"];
                if($this->accessGateway->userAcceptInvitation($access_id, $role))
                {
                    // accepted
                    $this->expireToken($row["id"]);
                    return generatePHPArrayResponse("Invitation accepted, redirecting..", false, 0);
                }else{
                    // expired or revoked
                    return generatePHPArrayResponse("Token expired or doesn't exist", true, 498);
                }

                break;

            default:
                return generatePHPArrayResponse("Invalid token (D-2)", true, 0);
                break;

        }
    }

    /**
     * set token as used
     * @param $id int token id
     * @return boolean success
     */
    public function expireToken($id)
    {

        // update DB //
        $statement = "UPDATE
                        tokens 
                        SET 
                            used = 1  
                        WHERE 
                            id = ?";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));

            return $statement->rowCount();

        } catch (PDOException $e) {
//            return $this->errorOccurredResponse("Couldn't Update Permission (2)");
            return false;
        }
    }

    /**
     * @param $token string token to get its info
     * @return mixed <b>token object</b> if found <br> <b>false</b> if not found or expired (24 hours passed or used)
     */
    public function find($token)
    {

        $statement = "SELECT *
                        FROM tokens 
                        WHERE identifier = ? 
                          AND used = 0  
                          AND DATE_ADD(time, INTERVAL '24:0' HOUR_MINUTE) > NOW();";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($token));
            return $statement->fetch();
        } catch (\PDOException $e) {
            return false;
//            exit($e->getMessage());
        }
    }

    public function findAll()
    {
        $statement = "
            SELECT 
                access.*,
                a.acc_name,
                r.role_name,
                r.role_desc,
                u.email,
                u.def_access_id
            FROM
                access
            
            INNER JOIN accounts a on access.acc_id = a.acc_id
            INNER JOIN roles r on access.acc_role = r.role_id
            INNER JOIN users u on access.uid = u.id
            ;";


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
     * used in landing page drop down selection of current accesses to choose from
     * used where clause to filter out unneeded roles from showing, like: pending invite acceptance
     * */
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
            where access.uid = ? and access.acc_role in (1,2,3)
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

    public function findTypistsForCurLoggedInAccount()
    {
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
            where access.acc_id = ? and access.acc_role in (3,6)
            ;";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION["accID"])); // todo (remember) this gets the accID dynamically not always the current user client admin acc
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
//            return $this->errorOccurredResponse("Invalid Input (5-ACG)");
            return false;
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
            if ($statement->rowCount() > 0) {  // user access exists
                return true;
            } else {
                return false;
            }

        } catch (\PDOException $e) {
//            exit($e->getMessage());
            return false;
        }
    }


    public function getPermCountForCurUser()
    {
        if (isset($_REQUEST["type"]) && strpos($_REQUEST["type"], '%') !== FALSE) {
            return false;
        }

        $type = isset($_REQUEST["type"]) ?
            $_REQUEST["type"]
            : 0;


        $statement = "
            SELECT 
                count(*) as count
            FROM
                access
            
            where uid = ?
            ";

        if (isset($_REQUEST["type"])) {
            $statement .= " and acc_role = $type";
        }

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION["uid"]));
            $result = $statement->fetch();

            return $result;

        } catch (\PDOException $e) {
            return false;
        }
    }

    // used to check for user permission to access a certain acc_id
    // optional $certain_role if user needs to set a role like typist to update the html/rtf files
    // returns the highest role the user has for that account or 0 if no permissions found
    // used in FileGateway once
    public function checkForUpdatePermission($acc_id, $certain_role = 0)
    {

        if (strpos($acc_id, '%') !== FALSE) {
//            return $this->errorOccurredResponse("Invalid Input (5-ACG)");
            return false;
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
            AND access.acc_role in (1,2,3) 
            ;";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION["uid"], $acc_id));
            $resultAll = $statement->fetchAll(\PDO::FETCH_ASSOC);
//            $result = $statement->fetch();
            if ($statement->rowCount() > 0) {  // user access exists
                $match = false;
                $highestRole = 3;
                foreach ($resultAll as $row) {
                    if ($row["acc_role"] < $highestRole) $highestRole = $row["acc_role"];
                    if ($certain_role != 0) {
                        if ($row["acc_role"] == $certain_role) $match = true;
                    }
                }
                if ($certain_role != 0 && $match) {
                    return $certain_role;
                } else if ($certain_role != 0 && !$match) {
                    return 0;
                }
                return $highestRole;
            } else {
                return 0;
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

        if (!sqlInjectionCheckPassed($_POST)) {
            return $this->errorOccurredResponse("Invalid Input (505-OT)");
        }


        $statement = "
            SELECT 
                access.*,
                a.acc_name,
                a.acc_retention_time,
                a.act_log_retention_time,
                r.role_name,
                r.role_desc,
                a.sr_enabled as sr_enabled,
                u.email
            FROM
                access
            
            INNER JOIN accounts a on access.acc_id = a.acc_id
            INNER JOIN roles r on access.acc_role = r.role_id
            INNER JOIN users u on access.uid = u.id
            where access.uid = ? and access.acc_id = ? and access.acc_role = ? ;";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION["uid"], $_POST["acc_id"], $_POST["acc_role"]));
//            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $result = $statement->fetch();
            if ($statement->rowCount() > 0) {  // user access exists check to prevent js altering
                // access exists
                $_SESSION['accID'] = $result["acc_id"];
                $_SESSION['role'] = $result["acc_role"];
                $_SESSION['sr_enabled'] = $result["sr_enabled"];
                $_SESSION['acc_name'] = $result["acc_name"];
                $_SESSION['acc_retention_time'] = $result["acc_retention_time"];
                $_SESSION['act_log_retention_time'] = $result["act_log_retention_time"];
                $_SESSION['role_desc'] = $result["role_desc"];
                $_SESSION['landed'] = true;
                return $this->oKResponse(null, "Role changed successfully");

            } else {
                return $this->errorOccurredResponse("You don't have permission for this role.");
            }
//            return $result;
        } catch (\PDOException $e) {
            return $this->errorOccurredResponse("Couldn't set role (4-OT)");
//            exit($e->getMessage());
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

        if (!sqlInjectionCheckPassed($_POST)) {
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


    /**
     * Inserts a new access permission directly to the current logged in user
     * <br><i>used in create new account client admin form #landing</i>
     * @param $accID int accountID to give client admin permission to
     * @return boolean true -> success | false -> failed to add permission
     */
    public function giveClientAdminPermission($accID)
    {
        if (!$accID) {
            return false;
        }


        // insert to DB //
        $statement = "INSERT
                        INTO 
                            access 
                            (
                             acc_id, uid, username, acc_role
                             ) 
                         VALUES 
                                (?, ?, ?, ?)";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                $accID,
                $_SESSION['uid'],
                $_SESSION['uEmail'],
                2
            ));

            if ($statement->rowCount()) {
                return true;
            } else {
                return false;
            }
//            return $statement->rowCount();
        } catch (\PDOException $e) {
            return false;
        }

    }


    public function updateAccess($id)
    {
        parse_str(file_get_contents('php://input'), $put);

        if (!sqlInjectionCheckPassed($put)) {
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
            return false;
//            exit($e->getMessage());
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

    public function insertModel(BaseModel $model): int
    {
        $statement = "
            INSERT INTO tokens 
                ( email, identifier, time, used, token_type, extra1, extra2)
            VALUES
                (:email, :identifier, :time, :used, :token_type, :extra1, :extra2)
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'email' => $model->getEmail(),
                'identifier' => $model->getIdentifier(),
                'time' => $model->getTime(),
                'used' => $model->getUsed(),
                'token_type' => $model->getTokenType(),
                'extra1' => $model->getExtra1(),
                'extra2' => $model->getExtra2()
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

    public function updateModel(BaseModel $model): int
    {
        $statement = "
            UPDATE tokens
            SET
                email = :email,
                identifier = :identifier,
                time = :time,
                used = :used,
                token_type = :token_type,
                extra1 = :extra1,
                extra2 = :extra2
            WHERE
                id = :id
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => $model->getId(),
                'email' => $model->getEmail(),
                'identifier' => $model->getIdentifier(),
                'time' => $model->getTime(),
                'used' => $model->getUsed(),
                'token_type' => $model->getTokenType(),
                'extra1' => $model->getExtra1(),
                'extra2' => $model->getExtra2()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function deleteModel(int $id): int
    {
        $statement = "
            DELETE FROM tokens
            WHERE id = :id;
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
                *            
            FROM
                tokens
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            return null;
//            exit($e->getMessage());
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
                tokens
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
        } catch (\PDOException $e) {
            return null;
//            exit($e->getMessage());
        }
    }
}