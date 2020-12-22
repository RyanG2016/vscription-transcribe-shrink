<?php


namespace Src\TableGateways;


use Src\Enums\SRQ_STATUS;
use Src\Helpers\common;
use Src\Models\BaseModel;
use Src\Models\SRQueue;

class SRQueueGateway implements GatewayInterface
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

    public function insert(BaseModel|SRQueue $model): int
    {

        $statement = "
            INSERT INTO sr_queue 
                (file_id, srq_status, srq_revai_id, srq_revai_minutes, notes)
            VALUES
                (:file_id, :srq_status, :srq_revai_id, :srq_revai_minutes, :notes)
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'file_id' => $model->getFileId()  ,
                'srq_status' => $model->getSrqStatus()  ,
                'srq_revai_id' => $model->getSrqRevaiId()  ,
                'srq_revai_minutes' => $model->getSrqRevaiMinutes()  ,
                'notes' => $model->getNotes()
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

    public function update(BaseModel|SRQueue $model): int
    {
        $statement = "
            UPDATE sr_queue
            SET
                file_id = :file_id,
                srq_status = :srq_status,
                srq_revai_id = :srq_revai_id,
                srq_revai_minutes = :srq_revai_minutes,
                notes = :notes     
            WHERE
                srq_id = :srq_id
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'file_id' => $model->getFileId()  ,
                'srq_status' => $model->getSrqStatus()  ,
                'srq_revai_id' => $model->getSrqRevaiId()  ,
                'srq_revai_minutes' => $model->getSrqRevaiMinutes()  ,
                'notes' => $model->getNotes(),
                'srq_id' => $model->getSrqId()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function delete(int $id): int
    {
        $statement = "
            DELETE FROM sr_queue
            WHERE srq_id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException) {
            return 0;
        }
    }

    public function find($id): array|null
    {
        $statement = "
            SELECT 
                *            
            FROM
                sr_queue
            WHERE srq_id = ?;
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

    public function findAll($page = 1): array|null
    {
        $offset = $this->common->getOffsetByPageNumber($page, $this->limit);

        $statement = "
            SELECT 
                *
            FROM
                sr_queue
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
        }
    }


    public function getFirst(): array|null
    {
        $statement = "
            SELECT 
                *
            FROM
                sr_queue
            WHERE 
                srq_status = :status
            ORDER BY srq_id
            LIMIT 1
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(
                array(
                    'status' => SRQ_STATUS::QUEUED
                )
            );
            if ($statement->rowCount()) {
                return $statement->fetch(\PDO::FETCH_ASSOC);
            } else {
                return null;
            }

        } catch (\PDOException) {
            return null;
        }
    }
}