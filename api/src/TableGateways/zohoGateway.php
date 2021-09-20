<?php

namespace Src\TableGateways;

use Src\Helpers\common;
use Src\Models\BaseModel;
use Src\Models\ZohoInvoice;
use Src\Models\ZohoUser;

class zohoGateway
{

    private $db;
    private $common;
    private $limit = 5;

    public function __construct($db)
    {
        $this->db = $db;
        $this->common = new common();
    }

    public function findAllUsers()
    {
        $offset = $this->common->getOffsetByPageNumber(isset($_GET['page'])?$_GET['page']:1, $this->limit);

        $statement = "
            SELECT 
                *
            FROM
                zoho_users
            LIMIT :limit
            OFFSET :offset";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'limit' => $this->limit,
                'offset' => $offset
            ));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if (isset($_GET['dt'])) {
                $json_data = array(
                    //            "draw"            => intval( $_REQUEST['draw'] ),
                    //            "recordsTotal"    => intval( 2 ),
                    //            "recordsFiltered" => intval( 1 ),
                    "count" => $statement->rowCount(),
                    "data" => $result
                );
                //        $response['body'] = json_encode($result);
                $result = $json_data;
            }
            return $result;
        } catch (\PDOException $e) {
            return array();
        }
    }
    public function findZohoUser($zoho_id): array|null
    {

        $statement = "
            SELECT 
                *
            FROM
                zoho_users
            WHERE zoho_id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($zoho_id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            if(!$result)
            {
                return array();
            }return $result;
        } catch (\PDOException $e) {
            return null;
//            exit($e->getMessage());
        }
    }
    public function insertZohoUser(BaseModel|ZohoUser $model): int
    {

        $statement = "
            INSERT into zoho_users
                (
                    zoho_id,
                    uid,
                    acc_id,
                    type,
                    user_data
                )
            VALUES
                (
                    :zoho_id,
                    :uid,
                    :acc_id,
                    :type,
                    :user_data
                )
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
//                'id' => $model->getId(),
                'zoho_id' => $model->getZohoId(),
                'uid' => $model->getUid(),
                'acc_id' => $model->getAccId(),
                'type' => $model->getType(),
                'user_data' => $model->getUserData()
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
    public function updateZohoUser(BaseModel|ZohoUser $model): int
    {
        $statement = "
            UPDATE zoho_users
            SET
                uid = :uid,
                acc_id = :acc_id,
                type = :type,
                user_data = :user_data
            WHERE
                zoho_id = :zoho_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'uid' => $model->getUid(),
                'acc_id' => $model->getAccId(),
                'type' => $model->getType(),
                'user_data' => $model->getUserData(),

                'zoho_id' => $model->getZohoId()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }
    public function deleteZohoUser(int $zoho_id): int
    {
        $statement = "
            DELETE FROM zoho_users
            WHERE zoho_id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $zoho_id));
            return $statement->rowCount();
        } catch (\PDOException) {
            return 0;
        }
    }


    public function findAllInvoices()
    {
        $offset = $this->common->getOffsetByPageNumber(isset($_GET['page'])?$_GET['page']:1, $this->limit);

        $statement = "
            SELECT 
                *
            FROM
                zoho_invoices
            LIMIT :limit
            OFFSET :offset";


        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'limit' => $this->limit,
                'offset' => $offset
            ));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if (isset($_GET['dt'])) {
                $json_data = array(
                    "count" => $statement->rowCount(),
                    //            "recordsTotal"    => intval( 2 ),
                    //            "recordsFiltered" => intval( 1 ),
                    "data" => $result
                );
                //        $response['body'] = json_encode($result);
                $result = $json_data;
            }
            return $result;
        } catch (\PDOException $e) {
            return array();
        }
    }
    public function findZohoInvoice($invoice_number): array|null
    {

        $statement = "
            SELECT 
                *            
            FROM
                zoho_invoices
            WHERE invoice_number = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($invoice_number));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            if(!$result)
            {
                return array();
            }return $result;
        } catch (\PDOException $e) {
            return null;
//            exit($e->getMessage());
        }
    }
    public function insertZohoInvoice(BaseModel|ZohoInvoice $model): int
    {

        $statement = "
            INSERT INTO zoho_invoices 
                (
                    zoho_id,
                    invoice_number,
                    invoice_data
                )
            VALUES
                (
                 :zoho_id,
                 :invoice_number,
                 :invoice_data
                )
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'zoho_id' => $model->getZohoId(),
                'invoice_number' => $model->getInvoiceNumber(),
                'invoice_data' => $model->getInvoiceData()
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
    public function updateZohoInvoice(BaseModel|ZohoInvoice $model): int
    {
        $statement = "
            UPDATE zoho_invoices
            SET
                invoice_number = :invoice_number,
                invoice_data = :invoice_data
            WHERE
                zoho_id = :zoho_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'zoho_id' => $model->getZohoId(),
                'invoice_number' => $model->getInvoiceNumber(),
                'invoice_data' => $model->getInvoiceData()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }
    public function deleteZohoInvoice(int $invoice_number): int
    {
        $statement = "
            DELETE FROM zoho_invoices
            WHERE invoice_number = :invoice_number;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('invoice_number' => $invoice_number));
            return $statement->rowCount();
        } catch (\PDOException) {
            return 0;
        }
    }



}