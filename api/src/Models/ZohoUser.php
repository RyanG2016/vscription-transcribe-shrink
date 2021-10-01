<?php


namespace Src\Models;
use Src\TableGateways\zohoGateway;
use Src\Traits\modelToString;

/**
 * Class Package
 * model for sr_packages table
 * @package Src\Models
 */
class ZohoUser implements BaseModelInterface
{

    use modelToString;
    private zohoGateway $zohoGateway;


    /**
     * @param int $id
     * @param string $zoho_id zoho contact-person id
     * @param string $zoho_contact_id zoho contact id of org not contact-person id
     * @param int $uid
     * @param int|null $acc_id
     * @param int $type systemAdmin(1), clientAdmin(2), typist(3)
     * @param int $primary_contact
     * @param string|null $user_data
     * @param string $created_at
     * @param null $db
     */
    public function __construct(
                                private int $id = 0,
                                private string $zoho_id = '',
                                private string $zoho_contact_id = '',
                                private int $uid = 0,
                                private ?int $acc_id = null,
                                private int $type = 0,
                                private int $primary_contact = 0,
                                private ?string $user_data = null,
                                private string $created_at = '',

                                private $db = null
    )
    {
//        if($this->created_at == "" || $this->created_at == null) $this->created_at = date("Y-m-d H:i:s");

        if($db != null)
        {
            $this->zohoGateway = new zohoGateway($db);
        }
    }

    /**
     * @return int
     */
    public function getPrimaryContact(): int
    {
        return $this->primary_contact;
    }

    /**
     * @param int $primary_contact
     */
    public function setPrimaryContact(int $primary_contact): void
    {
        $this->primary_contact = $primary_contact;
    }

    /**
     * @return int|string
     */
    public function getZohoContactId(): int|string
    {
        return $this->zoho_contact_id;
    }

    /**
     * @param int|string $zoho_contact_id
     */
    public function setZohoContactId(int|string $zoho_contact_id): void
    {
        $this->zoho_contact_id = $zoho_contact_id;
    }




    // Custom Constructors //

    public static function withID($id, $db) {
        $instance = new self(db: $db);
        $row = $instance->zohoGateway->findZohoUser($id);
        if(!$row)
        {
            return null;
        }
        $instance->fill( $row );
        return $instance;
    }

    public static function primaryWithAccID($acc_id, $db) {
        $instance = new self(db: $db);
        $row = $instance->zohoGateway->findPrimaryZohoUserWithAccID($acc_id);
        if(!$row)
        {
            return null;
        }
        $instance->fill( $row );
        return $instance;
    }

    public static function withRow( ?array $row, $db = null ) {
        if($row)
        {
            $instance = new self(db: $db);
            $instance->fill( $row );
            return $instance;
        }else{
            return null;
        }
    }


    // Interface Functions ---------------------

    public function save():int{

        if($this->id != 0)
        {
            // update
            return $this->zohoGateway->updateZohoUser($this);

        }else{
            // insert
            $this->id = $this->zohoGateway->insertZohoUser($this);
            return $this->id;
        }
    }

    public function delete():int
    {
        return $this->zohoGateway->deleteZohoUser($this->zoho_id);
    }

    public function fill(bool|array $row) {
        // fill all properties from array
        if($row)
        {
            $this->id = $row['id'];
            $this->zoho_id = $row['zoho_id'];
            $this->zoho_contact_id = $row['zoho_contact_id'];
            $this->uid = $row['uid'];
            $this->acc_id = $row['acc_id'];
            $this->type = $row['type'];
            $this->primary_contact = $row['primary_contact'];
            $this->user_data = $row['user_data'];
            $this->created_at = $row['created_at'];
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getZohoId(): int
    {
        return $this->zoho_id;
    }

    /**
     * @param int $zoho_id
     */
    public function setZohoId(int $zoho_id): void
    {
        $this->zoho_id = $zoho_id;
    }

    /**
     * @return int
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    /**
     * @param int $uid
     */
    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @return int|null
     */
    public function getAccId(): ?int
    {
        return $this->acc_id;
    }

    /**
     * @param int|null $acc_id
     */
    public function setAccId(?int $acc_id): void
    {
        $this->acc_id = $acc_id;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getUserData(): ?string
    {
        return $this->user_data;
    }

    /**
     * @param string|null $user_data
     */
    public function setUserData(?string $user_data): void
    {
        $this->user_data = $user_data;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    /**
     * @param string $created_at
     */
    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }




}