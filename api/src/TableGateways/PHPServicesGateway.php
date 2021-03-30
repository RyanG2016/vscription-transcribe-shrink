<?php

namespace Src\TableGateways;

use Src\Helpers\common;
use Src\Models\BaseModel;
use Src\Models\PHPService;

class PHPServicesGateway implements GatewayInterface
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
                php_services
            LIMIT :limit
            OFFSET :offset
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->bindParam(":limit",$this->limit, \PDO::PARAM_INT);
            $statement->bindParam(":offset",$offset, \PDO::PARAM_INT);
            $statement->execute();
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
                php_services
            WHERE service_id = ?;
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

    public function insertModel(BaseModel|PHPService $model):int
    {

        $statement = "
            INSERT INTO php_services 
                (service_name, last_start_time, last_stop_time, requests_made, current_status)
            VALUES
                (:service_name, :last_start_time, :last_stop_time, :requests_made, :current_status)
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'service_name' => $model->getServiceName(),
                'last_start_time' => $model->getLastStartTime(),
                'last_stop_time' => $model->getLastStopTime(),
                'revai_start_window' => $model->getRevaiStartWindow(),
                'requests_made' => $model->getRequestsMade(),
                'current_status' => $model->getCurrentStatus()

            ));
            if($statement->rowCount())
            {
                return $this->db->lastInsertId();
            }else{
                return 0;
            }
        } catch (\PDOException) {
            return 0;
        }
    }

// sr_rate = COALESCE(:sr_rate, @sr_rate),
// sr_flat_rate  = COALESCE(:sr_flat_rate, @sr_flat_rate),
// sr_vocab = COALESCE(:sr_vocab, @sr_vocab),
// sr_minutes_remaining = COALESCE(:sr_minutes_remaining, @sr_minutes_remaining)
    public function updateModel(BaseModel|PHPService $model): int
    {
        $statement = "
            UPDATE php_services
            SET
                service_name = :service_name,
                last_start_time = :last_start_time,
                last_stop_time = :last_stop_time,
                revai_start_window = :revai_start_window,
                requests_made = :requests_made,
                current_status = :current_status
            WHERE
                service_id = :service_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'service_id' => $model->getServiceId(),
                'service_name' => $model->getServiceName(),
                'last_start_time' => $model->getLastStartTime(),
                'last_stop_time' => $model->getLastStopTime(),
                'revai_start_window' => $model->getRevaiStartWindow(),
                'requests_made' => $model->getRequestsMade(),
                'current_status' => $model->getCurrentStatus()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function updateStartTime(BaseModel|PHPService $model): int
    {
        $statement = "
            UPDATE php_services
            SET
                last_start_time = :last_start_time,
                current_status = :current_status
            WHERE
                service_id = :service_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'service_id' => $model->getServiceId(),
                'last_start_time' => $model->getLastStartTime(),
                'current_status' => $model->getCurrentStatus()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function setRevAiStart(BaseModel|PHPService $model): int
    {
        $statement = "
            UPDATE php_services
            SET
                revai_start_window = :revai_start_window
            WHERE
                service_id = :service_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'service_id' => $model->getServiceId(),
                'revai_start_window' => $model->getRevaiStartWindow()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function updateStopTime(BaseModel|PHPService $model): int
    {
        $statement = "
            UPDATE php_services
            SET
                last_stop_time = :last_stop_time,
                current_status = :current_status
            WHERE
                service_id = :service_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'service_id' => $model->getServiceId(),
                'last_stop_time' => $model->getLastStopTime(),
                'current_status' => $model->getCurrentStatus()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function updateRequests(BaseModel|PHPService $model): int
    {
        $statement = "
            UPDATE php_services
            SET
                requests_made = :requests_made
            WHERE
                service_id = :service_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'service_id' => $model->getServiceId(),
                'requests_made' => $model->getRequestsMade()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function deleteModel(int $id):int
    {
        $statement = "
            DELETE FROM php_services
            WHERE service_id = :id;
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
     * get php_services record by service_id
     * @param $id int service_id
     * @return array|null
     */
    public function findAltModel($id): array|null
    {
        $statement = "
            SELECT 
                *            
            FROM
                php_services
            WHERE service_id = ?;
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