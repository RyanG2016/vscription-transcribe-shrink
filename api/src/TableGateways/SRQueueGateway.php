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

    public function insertModel(BaseModel|SRQueue $model): int
    {

        $statement = "
            INSERT INTO sr_queue 
                (file_id, srq_status, srq_revai_id, srq_revai_minutes, notes, srq_tmp_filename, srq_internal_id, refunded)
            VALUES
                (:file_id, :srq_status, :srq_revai_id, :srq_revai_minutes, :notes, :srq_tmp_filename, :srq_internal_id, :refunded)
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'file_id' => $model->getFileId()  ,
                'srq_status' => $model->getSrqStatus()  ,
                'srq_revai_id' => $model->getSrqRevaiId()  ,
                'srq_revai_minutes' => $model->getSrqRevaiMinutes()  ,
                'srq_tmp_filename' => $model->getSrqTmpFilename()  ,
                'srq_internal_id' => $model->getSrqInternalId()  ,
                'refunded' => $model->getRefunded()  ,
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

    public function updateModel(BaseModel|SRQueue $model): int
    {
        $statement = "
            UPDATE sr_queue
            SET
                file_id = :file_id,
                srq_status = :srq_status,
                srq_revai_id = :srq_revai_id,
                srq_revai_minutes = :srq_revai_minutes,
                refunded = :refunded,
                srq_tmp_filename = :srq_tmp_filename,
                srq_internal_id = :srq_internal_id,
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
                'srq_internal_id' => $model->getSrqInternalId()  ,
                'notes' => $model->getNotes(),
                'refunded' => $model->getRefunded(),
                'srq_tmp_filename' => $model->getSrqTmpFilename(),
                'srq_id' => $model->getSrqId()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function deleteModel(int $id): int
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

    public function findModel($id): array|null
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
            $result = null;
            if($statement->rowCount())
            {
                $result = $statement->fetch(\PDO::FETCH_ASSOC);
            }

            return $result;
        } catch (\PDOException) {
            return null;
        }
    }

    /**
     * retrieves next internalID to set
     * @return int nextInternalID or 0 if failed
     */
    public function getNextInternalID(): int
    {
        $nextInternalID = 1;

        $maxStatement = "
            select MAX(srq_internal_id) as max from sr_queue;
        ";


        try {
            $maxStatement = $this->db->prepare($maxStatement);
            $maxStatement->execute();
            $max = $maxStatement->fetch(\PDO::FETCH_ASSOC)["max"];
            if($max != null)
            {
                $nextInternalID = $max+1;
            }

            return $nextInternalID;
        } catch (\PDOException) {
            return 0;
        }
    }
/*   public function getNextInternalID($srq_id): int
    {
        $nextInternalID = 1;

        $maxStatement = "
            select MAX(srq_internal_id) as max from sr_queue;
        ";
        $maxStatement = $this->db->prepare($maxStatement);
        $maxStatement->execute();
        $max = $maxStatement->fetch(\PDO::FETCH_ASSOC)["max"];

        if($max != null)
        {
            $nextInternalID = $max+1;
        }

        $statement = "
            UPDATE sr_queue
            SET
                srq_internal_id = :srq_internal_id
            WHERE
                srq_id = :srq_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('srq_internal_id' => $nextInternalID,
                'srq_id' => $srq_id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            if ($statement->rowCount()) {
                return $nextInternalID;
            }
            else{
                return 0;
            }
        } catch (\PDOException) {
            return 0;
        }
    }*/

    public function findAltModel($id): array|null
    {
        $statement = "
            SELECT 
                *            
            FROM
                sr_queue
            WHERE file_id = ?;
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

    public function findByRevAiID(string $id): array|null
    {
        $statement = "
            SELECT 
                *            
            FROM
                sr_queue
            WHERE srq_revai_id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));

            $result = null;
            if($statement->rowCount())
            {
                $result = $statement->fetch(\PDO::FETCH_ASSOC);
            }
            return $result;
        } catch (\PDOException) {
            return null;
        }
    }

    public function findAllModel($page = 1): array|null
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
                $statement->closeCursor();
                unset($statement);
                return null;
            }

        } catch (\PDOException) {
            return null;
        }
    }

    public function getChartData()
    {

        $statement = "SELECT srq_status_ref.srq_status as 'status' , srq_status_ref.srq_status_desc as 'label', COUNT(sr_queue.srq_status) AS 'count'
                    FROM sr_queue
                             RIGHT JOIN srq_status_ref ON srq_status_ref.srq_status = sr_queue.srq_status
                    GROUP BY srq_status_ref.srq_status;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

//            return $result;
            return array(
                "labels" => array_column($result, 'label'),
                "data" => array_column($result, 'count')
            );

            /*if(isset($_GET["tr"]))
            {
                // set job start timer
                $_SESSION['timerStart'] = date("Y-m-d H:i:s");
                // load tmp file for transcribe.php
                return $this->loadTmpFile($result);
            }else{
                return $result;
            }*/

        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * get next file queued for internal processing
     * @return array|null
     */
    public function getNextQFIProcessing(): array|null
    {
        $statement = "
            SELECT 
                *
            FROM
                sr_queue
            WHERE 
                srq_status = :status
            ORDER BY srq_internal_id
            LIMIT 1
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(
                array(
                    'status' => SRQ_STATUS::INTERNAL_PROCESSING
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