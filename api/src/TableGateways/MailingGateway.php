<?php

namespace Src\TableGateways;

use PDOException;
//use Src\TableGateways\logger;

include_once "common.php";

class MailingGateway
{

    private $db;
//    private $logger;
    private $API_NAME = "Mailing Gateway";

    public function __construct($db)
    {
        $this->db = $db;
//        $this->logger = new logger($db);
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

}