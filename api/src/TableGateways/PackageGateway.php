<?php

namespace Src\TableGateways;

use Src\Helpers\common;
use Src\Models\BaseModel;
use Src\Models\Package;

class PackageGateway implements GatewayInterface
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
                sr_packages
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
                sr_packages
            WHERE srp_id = ?;
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

    public function insertModel(BaseModel|Package $model):int
    {

        $statement = "
            INSERT INTO sr_packages 
                (srp_name, srp_minutes, srp_price)
            VALUES
                (:srp_name, :srp_minutes, :srp_price)
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'srp_name' => $model->getSrpName(),
                'srp_minutes' => $model->getSrpMins(),
                'srp_price' => $model->getSrpPrice()

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
    public function updateModel(BaseModel|Package $model): int
    {
        $statement = "
            UPDATE sr_packages
            SET
                srp_minutes = :srp_minutes,
                srp_name = :srp_name,
                srp_price = :srp_price
            WHERE
                srp_id = :srp_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'srp_name' => $model->getSrpName(),
                'srp_minutes' => $model->getSrpMins(),
                'srp_price' => $model->getSrpPrice(),
                'srp_id' => $model->getSrpId()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function deleteModel(int $id):int
    {
        $statement = "
            DELETE FROM sr_packages
            WHERE srp_id = :id;
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
     * get sr_packages record by srp_id
     * @param $id int srp_id
     * @return array|null
     */
    public function findAltModel($id): array|null
    {
        $statement = "
            SELECT 
                *            
            FROM
                sr_packages
            WHERE srp_id = ?;
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