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

    /**
     * Adds a log record to act_log
     * @param $API_NAME string api/page name
     * @param $activity string activity to record
     * @return bool (boolean) log recorded
     */
    function insertAuditLogEntry($API_NAME, $activity) {
        //INSERT AUDIT LOG DATA

        $statement = "INSERT INTO act_log(username, acc_id, actPage, activity, ip_addr) VALUES(?,?,?,?,?)";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                isset($_SESSION["uEmail"])?$_SESSION["uEmail"]:"Not Logged In",
                isset($_SESSION["accID"])?$_SESSION["accID"]:0,
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