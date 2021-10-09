<?php

namespace Src\TableGateways;

use Src\Enums\ROLES;
use Src\Helpers\common;
use Src\Models\BaseModel;
use Src\Models\Session;

class sessionGateway
{

    private $db;
    private $common;
    private $limit = 10;

    public function __construct($db)
    {
        $this->db = $db;
        $this->common = new common();
    }

    // revoked and expired will not show
    public function findAllSessions()
    {
        $offset = $this->common->getOffsetByPageNumber($_GET['page'] ?? 1, $this->limit);

        $statement = "
            SELECT 
                sessions.*, ssr.`desc` as 'src_text'
            FROM
                sessions
            left join sessions_source_ref ssr on ssr.src = sessions.src
            where
                  uid = :uid and revoked != 1 and (sessions.expire_time > curdate())
            LIMIT :limit OFFSET :offset";


        try {
            $statement = $this->db->prepare($statement);
            $statement->bindParam(':limit', $this->limit, \PDO::PARAM_INT);
            $statement->bindParam(':uid', $_SESSION['uid'], \PDO::PARAM_INT);
            $statement->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if (isset($_GET['dt'])) {
                $json_data = array(
                    //            "draw"            => intval( $_REQUEST['draw'] ),
                    //            "recordsTotal"    => intval( 2 ),
                    "uid" => $_SESSION['uid'],
                    "current" => session_id(),
                    "count" => $statement->rowCount(),
                    "data" => $result
                );
                // $response['body'] = json_encode($result);
                $result = $json_data;
            }
            return $result;
        } catch (\PDOException $e) {
            return array();
        }
    }

    public function findSession($sessionID) {
        $statement = "
            SELECT 
                *
            FROM
                sessions
            where id = ?";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($sessionID));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);

            return $result;
        } catch (\PDOException $e) {
            return array();
        }
    }

    public function findSessionWithPHPSessID($php_sess_id) {
        $statement = "
            SELECT 
                *
            FROM
                sessions
            where php_sess_id = ?";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($php_sess_id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);

            return $result;
        } catch (\PDOException $e) {
            return array();
        }
    }


    public function insertSession(BaseModel|Session $model): int
    {

        $statement = "
            INSERT into sessions
                (
                    uid,
                    php_sess_id,
                    src,
                    login_time,
                    ip_address,
                    expire_time
                )
            VALUES
                (
                    :uid,
                    :php_sess_id,
                    :src,
                    :login_time,
                    :ip_address,
                    :expire_time
                )
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
//                'id' => $model->getId(),
                'uid' => $model->getUid(),
                'php_sess_id' => $model->getPhpSessId(),
                'src' => $model->getSrc(),
                'ip_address' => $model->getIpAddress(),
//                'revoked' => $model->getRevoked(),
//                'revoke_date' => $model->getRevokeDate(),
                'login_time' => $model->getLoginTime(),
                'expire_time' => $model->getExpireTime()
            ));
            if($statement->rowCount())
            {
                return $this->db->lastInsertId();
            }else{
                return 0;
            }
        } catch (\PDOException $e) {
            return 0;
        }
    }
    public function updateSession(BaseModel|Session $model): int
    {
        $statement = "
            UPDATE sessions
            SET
                src = :src,
                revoked = :revoked,
                revoke_date = :revoke_date,
                expire_time = :expire_time
            WHERE
                id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => $model->getId(),

                'src' => $model->getSrc(),
                'revoked' => $model->getRevoked(),
                'revoke_date' => $model->getRevokeDate(),
                'expire_time' => $model->getExpireTime()

            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }
    public function deleteSession(int $session_id): int
    {
        $statement = "
            DELETE FROM sessions
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $session_id));
            return $statement->rowCount();
        } catch (\PDOException) {
            return 0;
        }
    }

}