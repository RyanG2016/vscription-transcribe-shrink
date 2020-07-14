<?php

namespace Src\TableGateways;
include "common.php";
class LogoutGateway
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }


    public function sessionLogout()
    {
        $sessCopy = $_SESSION;

        // Getting logout time in db
        isset($_SESSION['uEmail']) ? $uemail = $_SESSION['uEmail'] : $uemail = "";
        if (isset($_SESSION['loggedIn'])) {
            $uemail = $_SESSION['uEmail'];

            // log LOGOUT to act_log
            $this->insertAuditLogEntry($sessCopy, "Logout");

            $rmb = false;
            if (isset($_SESSION['remember'])) {
                $rmb = $_SESSION['remember'];
            }
            session_unset();

            if ($rmb) {
                $_SESSION['remember'] = true;
                $_SESSION['uEmail'] = $uemail;
            }

            $_SESSION['msg'] = "Please login to continue";
            return array("error" => false, "msg" => "Logged out successfully.");

        } else { //not even loggedIn

            $rmb = false;
            if (isset($_SESSION['remember'])) {
                $rmb = $_SESSION['remember'];
            }
            session_unset();
            session_destroy();

            if ($rmb) {
                $_SESSION['remember'] = true;
                $_SESSION['uEmail'] = $uemail;
            }
            return array("error" => false, "msg" => "Not logged in.");

        }

    }

    function insertAuditLogEntry($sessCopy, $activity)
    {
        //INSERT AUDIT LOG DATA

        $statement = "INSERT INTO act_log(username, acc_id, actPage, activity, ip_addr) VALUES(?,?,?,?,?)";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                $sessCopy["uEmail"],
                isset($sessCopy["accID"])?$sessCopy["accID"]:0,
                "Logout API",
                $activity,
                getIP()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

    }

}