<?php

namespace Src\TableGateways;

use Src\Enums\ROLES;
use Src\Helpers\common;
use Src\Models\BaseModel;
use Src\Models\User;
use Src\Models\ZohoBill;
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

    public function findPrimaryZohoUserWithAccID($accId): array|null
    {

        $statement = "
            SELECT 
                *
            FROM
                zoho_users
            WHERE acc_id = ? and primary_contact = 1;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($accId));
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

    public function findTypistVendorZohoUserWithUserID($uid): array|null
    {

        $statement = "
            SELECT 
                *
            FROM
                zoho_users
            WHERE uid = ? and type = ".ROLES::TYPIST." and primary_contact = 1;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($uid));
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

    public function findAllZohoUsers($accId): array|null
    {

        $statement = "
            SELECT 
                *
            FROM
                zoho_users
            WHERE acc_id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($accId));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if(!$result)
            {
                return array();
            }return $result;
        } catch (\PDOException $e) {
            return null;
//            exit($e->getMessage());
        }
    }

    public function findMainClientAdminOfOrg($orgID): User|null
    {

        $statement = "
            SELECT 
                *
            FROM
                users
            WHERE account = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($orgID));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            if(!$result)
            {
                return null;
            }
            return User::withRow($result);
        } catch (\PDOException $e) {
            return null;
//            exit($e->getMessage());
        }
    }


    public function findAllSubClientAdminsOfOrg($orgID): array|null
    {

        $statement = "
            select 
                u.id,
                u.first_name,
                u.last_name,
                u.email
            from access
            left join users u on access.uid = u.id
            
            where acc_role = 2 and acc_id = ? and u.id not in (select id from users where account = acc_id);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($orgID));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if(!$result)
            {
                return null;
            }
            return $result;
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function findSystemAdmins(): array|null
    {

        $statement = "select 
       
            u.id, 
            u.first_name,
            u.last_name,
            u.email

            from access
            left join users u on access.uid = u.id
            
            where acc_role = ".ROLES::SYSTEM_ADMINISTRATOR."
            
            group by uid
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

            if(!$result)
            {
                return null;
            }
            return $result;
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
                    zoho_contact_id,
                    uid,
                    acc_id,
                    type,
                    primary_contact,
                    user_data
                )
            VALUES
                (
                    :zoho_id,
                    :zoho_contact_id,
                    :uid,
                    :acc_id,
                    :type,
                    :primary_contact,
                    :user_data
                )
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
//                'id' => $model->getId(),
                'zoho_id' => $model->getZohoId(),
                'zoho_contact_id' => $model->getZohoContactId(),
                'uid' => $model->getUid(),
                'acc_id' => $model->getAccId(),
                'type' => $model->getType(),
                'primary_contact' => $model->getPrimaryContact(),
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
                zoho_id = :zoho_id,
                zoho_contact_id = :zoho_contact_id,
                uid = :uid,
                acc_id = :acc_id,
                type = :type,
                primary_contact = :primary_contact,
                user_data = :user_data
            WHERE
                id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'zoho_contact_id' => $model->getZohoContactId(),
                'uid' => $model->getUid(),
                'acc_id' => $model->getAccId(),
                'type' => $model->getType(),
                'primary_contact' => $model->getPrimaryContact(),
                'user_data' => $model->getUserData(),
                'zoho_id' => $model->getZohoId(),

                'id' => $model->getId()

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
                    invoice_number,
                    zoho_contact_id,       
                    zoho_invoice_id,
                    local_invoice_data,
                    zoho_invoice_data
                )
            VALUES
                (
                 :invoice_number,
                 :zoho_contact_id,
                 :zoho_invoice_id,
                 :local_invoice_data,
                 :zoho_invoice_data
                )
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'invoice_number' => $model->getInvoiceNumber(),
                'zoho_contact_id' => $model->getZohoContactId(),
                'zoho_invoice_id' => $model->getZohoInvoiceId(),
                'local_invoice_data' => $model->getLocalInvoiceData(),
                'zoho_invoice_data' => $model->getZohoInvoiceData()
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
                zoho_contact_id = :zoho_contact_id,
                local_invoice_data = :local_invoice_data,
                zoho_invoice_data = :zoho_invoice_data
            WHERE
                zoho_invoice_id = :zoho_invoice_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'invoice_number' => $model->getInvoiceNumber(),
                'zoho_contact_id' => $model->getZohoContactId(),
                'local_invoice_data' => $model->getLocalInvoiceData(),
                'zoho_invoice_data' => $model->getZohoInvoiceData(),
                'zoho_invoice_id' => $model->getZohoInvoiceId()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }
    public function deleteZohoInvoice(int $zoho_invoice_id): int
    {
        $statement = "
            DELETE FROM zoho_invoices
            WHERE zoho_invoice_id = :zoho_invoice_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('zoho_invoice_id' => $zoho_invoice_id));
            return $statement->rowCount();
        } catch (\PDOException) {
            return 0;
        }
    }


    public function findZohoBill($bill_number): array|null
    {

        $statement = "
            SELECT 
                *            
            FROM
                zoho_bills
            WHERE bill_number = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($bill_number));
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

    public function insertZohoBill(BaseModel|ZohoBill $model): int
    {

        $statement = "
                    INSERT INTO zoho_bills
                    (
                    bill_number,
                    zoho_contact_id,
                    zoho_bill_id,
                    local_bill_data,
                    zoho_bill_data
                    )
                    VALUES
                    (
                    :bill_number,
                    :zoho_contact_id,
                    :zoho_bill_id,
                    :local_bill_data,
                    :zoho_bill_data
                    )
                ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'bill_number' => $model->getBillNumber(),
                'zoho_contact_id' => $model->getZohoContactId(),
                'zoho_bill_id' => $model->getZohoBillId(),
                'local_bill_data' => $model->getLocalBillData(),
                'zoho_bill_data' => $model->getZohoBillData()
            ));
            if ($statement->rowCount()) {
                return $this->db->lastInsertId();
            } else {
                return 0;
            }
        } catch (\PDOException) {
            return 0;
        }
    }

    public function updateZohoBill(BaseModel|ZohoBill $model): int
    {
        $statement = "
            UPDATE zoho_bills
            SET
            bill_number = :bill_number,
            zoho_contact_id = :zoho_contact_id,
            local_bill_data = :local_bill_data,
            zoho_bill_data = :zoho_bill_data
            WHERE
            zoho_bill_id = :zoho_bill_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'bill_number' => $model->getBillNumber(),
                'zoho_contact_id' => $model->getZohoContactId(),
                'local_bill_data' => $model->getLocalBillData(),
                'zoho_bill_data' => $model->getZohoBillData(),
                'zoho_bill_id' => $model->getZohoBillId()
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function deleteZohoBill(int $zoho_bill_id): int
    {
        $statement = "
                    DELETE FROM zoho_bills
                    WHERE zoho_bill_id = :zoho_bill_id;
";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('zoho_bill_id' => $zoho_bill_id));
            return $statement->rowCount();
        } catch (\PDOException) {
            return 0;
        }
    }

    /**
     * @param string $fileIds
     * Model example: file_id_1, file_id_2, ...
     * @param int $billed tiny int (0: not billed, 1: billed)
     * @return bool success | failed
     */
    public function markAsBilled(string $fileIds, int $billed): bool
    {
        $statement = "
            UPDATE files
            SET               
                billed = :billed,
                billed_date = :billed_date
            WHERE 
                  file_id in ($fileIds);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'billed' => $billed,
                'billed_date' => $billed?date("Y-m-d H:i:s"):null
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    /**
     * @param string $fileIds
     * Model example: file_id_1, file_id_2, ...
     * @param int $billed tiny int (0: not billed, 1: billed)
     * @return bool success | failed
     */
    public function markAsTypistBilled(string $fileIds, int $billed): bool
    {
        $statement = "
            UPDATE files
            SET               
                typ_billed = :billed
            WHERE 
                  file_id in ($fileIds);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'billed' => $billed
//                'billed_date' => $billed?date("Y-m-d H:i:s"):null
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function getNextBillNumber():string
    {
        $statement = "show table status where name = 'zoho_bills';";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            if(!$result)
            {
                return '0';
            }return $result['Auto_increment'];
        } catch (\PDOException $e) {
            return '0';
//            exit($e->getMessage());
        }
    }

}