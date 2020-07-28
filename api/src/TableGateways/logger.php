<?php


namespace Src\TableGateways;
include_once "common.php";

class logger
{
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    function insertAuditLogEntry($API_NAME, $activity) {
        //INSERT AUDIT LOG DATA

        $statement = "INSERT INTO act_log(username, acc_id, actPage, activity, ip_addr) VALUES(?,?,?,?,?)";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                $_SESSION["uEmail"],
                $_SESSION["accID"]?$_SESSION["accID"]:0,
                $API_NAME . " API",
                $activity,
                getIP()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
//            exit($e->getMessage());
            return false;
        }

    }
}