<?php

namespace Src\TableGateways;

use Src\Helpers\common;
use Src\Models\BaseModel;
use Src\Models\Payment;

class paymentGateway implements GatewayInterface
{
    private $db = null;
    private $common = null;

    // Gateway results limits
    private $limit = 10;

    public function __construct($db)
    {
        $this->db = $db;
        $this->common = new common();
    }



    public function findAllModel($page = 1): array|null
    {

        $offset = $this->common->getOffsetByPageNumber($page, $this->limit);

        $statement = "
            SELECT 
                *
            FROM
                payments
            LIMIT :limit
            OFFSET :offset
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(
                array(
                    'limit' => $this->limit,
                    'offset' => $offset
                )
            );
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException) {
            return null;
//            exit($e->getMessage());
        }
    }

    public function findAllByUid($uid, $page = 1): array|null
    {

        $offset = $this->common->getOffsetByPageNumber($page, $this->limit);

        $statement = "
            SELECT 
                *
            FROM
                payments
            WHERE user_id = :uid
            LIMIT :limit
            OFFSET :offset
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(
                array(
                    'limit' => $this->limit,
                    'uid' => $uid,
                    'offset' => $offset
                )
            );
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            return null;
//            exit($e->getMessage());
        }
    }

    public function findModel($id): array|null
    {
//        $filter = parseParams();

        $statement = "
            SELECT 
                *            
            FROM
                payments
            WHERE payment_id = ?;
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


    public function getLastPurchaseForCurrentUser(): array|null
    {
//        $filter = parseParams();

        $statement = "
            SELECT 
                *            
            FROM
                payments
            where
            user_id = ?
            order by payment_id DESC
Limit 1;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($_SESSION["uid"]));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            if($statement->rowCount())
            {
                return $result;
            }else{
                return null;
            }
        } catch (\PDOException $e) {
            return null;
//            exit($e->getMessage());
        }
    }

    public function insertModel(BaseModel|Payment $model):int
    {

        $statement = "
            INSERT INTO payments 
                (user_id, amount, ref_id,trans_id, payment_json, status, pkg_id)
            VALUES
                (:user_id, :amount, :ref_id, :trans_id, :payment_json, :status, :pkg_id)
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'user_id' => $model->getUserId(),
                'amount' => $model->getAmount(),
                'ref_id' => $model->getRefId(),
                'trans_id' => $model->getTransId(),
                'payment_json' => $model->getPaymentJson(),
                'status' => $model->getStatus(),
                'pkg_id' => $model->getPkgId()
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

// sr_rate = COALESCE(:sr_rate, @sr_rate),
// sr_flat_rate  = COALESCE(:sr_flat_rate, @sr_flat_rate),
// sr_vocab = COALESCE(:sr_vocab, @sr_vocab),
// sr_minutes_remaining = COALESCE(:sr_minutes_remaining, @sr_minutes_remaining)
    public function updateModel(BaseModel|Payment $model): int
    {
        $statement = "
            UPDATE payments
            SET
                user_id = :user_id,
                amount = :amount,
                ref_id = :ref_id,
                payment_json = :payment_json,
                trans_id = :trans_id,
                status = :status,
                pkg_id = :pkg_id
            WHERE
                payment_id = :payment_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'user_id' => $model->getUserId(),
                'amount' => $model->getAmount(),
                'ref_id' => $model->getRefId(),
                'status' => $model->getStatus(),
                'trans_id' => $model->getTransId(),
                'payment_json' => $model->getPaymentJson(),
                'payment_id' => $model->getPaymentId(),
                'pkg_id' => $model->getPkgId()
            ));
            return $statement->rowCount();
        } catch (\PDOException) {
            return 0;
        }
    }

    public function deleteModel(int $id):int
    {
        $statement = "
            DELETE FROM payments
            WHERE payment_id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    /**
     * get payments record by ref_id
     * @param $id string ref_id
     * @return array|null
     */
    public function findAltModel($id): array|null
    {
        $statement = "
            SELECT 
                *            
            FROM
                payments
            WHERE ref_id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            if($statement->rowCount())
            {
                $result = $statement->fetch(\PDO::FETCH_ASSOC);
                return $result;
            }else{
                return null;
            }
        } catch (\PDOException) {
            return null;
        }
    }


    /**
     * get payments record by trans_id
     * @param $trans_id string trans_id
     * @return array|null
     */
    public function findByTransID($trans_id): array|null
    {
        $statement = "
            SELECT 
                *            
            FROM
                payments
            WHERE trans_id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($trans_id));
            if($statement->rowCount())
            {
                $result = $statement->fetch(\PDO::FETCH_ASSOC);
                return $result;
            }else{
                return null;
            }
        } catch (\PDOException) {
            return null;
        }
    }
}