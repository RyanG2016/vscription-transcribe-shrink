<?php

namespace Src\TableGateways;

use Src\Helpers\common;
use Src\Models\BaseModel;
use Src\Models\SR;

class SRGateway implements GatewayInterface
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


    public function findByAccID($acc_id)
    {

        $statement = "
            SELECT 
                *            
            FROM
                speech_recognition
            WHERE account_id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($acc_id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            return null;
//            exit($e->getMessage());
        }
    }

    public function findAll($page = 1): array|null
    {

        $offset = $this->common->getOffsetByPageNumber($page, $this->limit);

        $statement = "
            SELECT 
                *
            FROM
                speech_recognition
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
        } catch (\PDOException $e) {
            return null;
//            exit($e->getMessage());
        }
    }

    public function find($id): array|null
    {
//        $filter = parseParams();

        $statement = "
            SELECT 
                *            
            FROM
                speech_recognition
            WHERE sr_id = ?;
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

    public function insert(BaseModel|SR $model):int
    {

        $statement = "
            INSERT INTO speech_recognition 
                (account_id, sr_rate, sr_flat_rate, sr_vocab, sr_minutes_remaining)
            VALUES
                (:account_id, :sr_rate, :sr_flat_rate, :sr_vocab, :sr_minutes_remaining)
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'sr_rate' => $model->getSrRate(),
                'sr_flat_rate' => $model->getSrFlatRate(),
                'sr_vocab' => $model->getSrVocab(),
                'sr_minutes_remaining' => $model->getSrMinutesRemaining(),
                'account_id' => $model->getAccountId()
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
    public function update($model): int
    {
        $statement = "
            UPDATE speech_recognition
            SET
                sr_rate = :sr_rate,
                sr_flat_rate = :sr_flat_rate,
                sr_vocab = :sr_vocab,
                sr_minutes_remaining = :sr_minutes_remaining
            WHERE
                account_id = :account_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'sr_rate' => $model->getSrRate(),
                'sr_flat_rate' => $model->getSrFlatRate(),
                'sr_vocab' => $model->getSrVocab(),
                'sr_minutes_remaining' => $model->getSrMinutesRemaining(),

                'account_id' => $model->getAccountId()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function delete(int $id):int
    {
        $statement = "
            DELETE FROM speech_recognition
            WHERE sr_id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
//            exit($e->getMessage());
        }
    }

    /**
     * get speech_recognition record by account_id
     * @param $id int account_id
     * @return array|null
     */
    public function findAlt($id): array|null
    {
        $statement = "
            SELECT 
                *            
            FROM
                speech_recognition
            WHERE account_id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException) {
            return null;
        }
    }
}