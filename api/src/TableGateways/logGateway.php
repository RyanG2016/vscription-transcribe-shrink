<?php

namespace Src\TableGateways;

use Src\TableGateways\accessGateway;

class logGateway
{

    private $db;
    private $limitPerPage;
    private $accessGateway;

    public function __construct($db)
    {
        $this->db = $db;
        $this->limitPerPage = 50;
        $this->accessGateway = new accessGateway($db);
    }

    /**
     * Retrieves All Logs(sys admin only usage)
     * @param $page int page
     * @return mixed array/false
     */
    public function findAll($page)
    {

        $offset = $this->limitPerPage * $page;
        $statement = "
            SELECT
                act_log_id, username, act_log_date, acc_id, actPage, activity, ip_addr
            FROM
                act_log
            limit :limit offset :offset
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->bindParam(':limit', $this->limitPerPage, \PDO::PARAM_INT);
            $statement->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
//            $res = $statement->fetchAll(\PDO::FETCH_ASSOC);
//            return $res;


        } catch (\PDOException $e) {
            return false;
//            exit($e->getMessage());
        }
    }

    /**
     * Retrieves Logs for a certain account (sys admin only usage)
     * @param $page int page
     * @param $accID int account ID
     * @return mixed array/false
     */
    public function findAllForAcc($page, $accID)
    {

        $offset = $this->limitPerPage * $page;
        $statement = "
            SELECT
                act_log_id, username, act_log_date, acc_id, actPage, activity, ip_addr
            FROM
                act_log
            where acc_id = :acc
            limit :limit offset :offset
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->bindParam(':limit', $this->limitPerPage, \PDO::PARAM_INT);
            $statement->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $statement->bindParam(':acc', $accID, \PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
//            $res = $statement->fetchAll(\PDO::FETCH_ASSOC);
//            return $res;


        } catch (\PDOException $e) {
            return false;
//            exit($e->getMessage());
        }
    }


    public function findClientAll($page)
    {

        $offset = $this->limitPerPage * $page;
        $accID = $_SESSION["accID"];
        if (isset($_GET["acc_id"]) && is_numeric($_GET["acc_id"])) {
            if(!$this->accessGateway->hasAccess($_SESSION["uid"], $_GET["acc_id"], 2)){
                return array();
            }
        }

        $statement = "
            SELECT
                act_log_id, username, act_log_date, acc_id, actPage, activity, ip_addr
            FROM
                act_log
            where acc_id = :acc
            limit :limit offset :offset
        ;";


        try {
            $statement = $this->db->prepare($statement);
            $statement->bindParam(':limit', $this->limitPerPage, \PDO::PARAM_INT);
            $statement->bindParam(':acc', $accID, \PDO::PARAM_INT);
            $statement->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchAll(\PDO::FETCH_ASSOC);


        } catch (\PDOException $e) {
            return false;
//            exit($e->getMessage());
        }
    }

    public function find($id)
    {

        $statement = "
            SELECT 
                *
            FROM
                countries
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            return $statement->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    /*
        public function insertNewlog($name)
        {

            $statement = "INSERT
                            INTO
                                file_speaker_type
                                (
                                 name
                                 )
                             VALUES
                                    (?)";

            try {
                $statement = $this->db->prepare($statement);
                $statement->execute(array($name));

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

        public function delete($id)
        {
            $statement = "
                DELETE FROM countries
                WHERE id = :id;
            ";

            try {
                $statement = $this->db->prepare($statement);
                $statement->execute(array('id' => $id));
                return $statement->rowCount();
            } catch (\PDOException $e) {
                exit($e->getMessage());
            }
        }*/
}