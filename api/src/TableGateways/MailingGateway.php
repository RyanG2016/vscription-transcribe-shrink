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

        */

        $statement = "
            select users.id, email
            from users
            where users.account_status = 1 and users.enabled = 1 and users.typist = 1 and
                (
                    select count(access.acc_id) from access where access.acc_id = ? and uid = users.id and (acc_role = 3 OR acc_role = 6)
                ) != 1
            group by users.id order by users.id";
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