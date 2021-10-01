<?php


namespace Src\Models;
use Src\TableGateways\zohoGateway;
use Src\Traits\modelToString;

/**
 * Class Package
 * model for sr_packages table
 * @package Src\Models
 */
class ZohoBill implements BaseModelInterface
{

    use modelToString;
    private zohoGateway $zohoGateway;


    public function __construct(
                                private int $id = 0,
//                                private int $zoho_id = 0,
                                private string $bill_number = '',
                                private int $zoho_vendor_id = 0,
                                private string $zoho_bill_id = '',
                                private ?string $local_bill_data = null,
                                private ?string $zoho_bill_data = null,
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
     * @return string
     */
    public function getZohoBillId(): string
    {
        return $this->zoho_bill_id;
    }

    /**
     * @param string $zoho_bill_id
     */
    public function setZohoBillId(string $zoho_bill_id): void
    {
        $this->zoho_bill_id = $zoho_bill_id;
    }

    // Custom Constructors //

    public function withID($bill_number, $db) {
        $instance = new self(db: $db);
        $row = $instance->zohoGateway->findZohoBill($bill_number);
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
            return $this->zohoGateway->updateZohoBill($this);

        }else{
            // insert
            $this->id = $this->zohoGateway->insertZohoBill($this);
            return $this->id;
        }
    }

    public function delete():int
    {
        return $this->zohoGateway->deleteZohoBill($this->zoho_bill_id);
    }

    public function fill(bool|array $row) {
        // fill all properties from array
        if($row)
        {
            $this->id = $row['id'];
//            $this->zoho_id = $row['zoho_id'];
            $this->bill_number = $row['bill_number'];
            $this->zoho_vendor_id = $row['zoho_vendor_id'];
            $this->zoho_bill_id = $row['zoho_bill_id'];
            $this->local_bill_data = $row['local_bill_data'];
            $this->zoho_bill_data = $row['zoho_bill_data'];
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
     * @return string
     */
    public function getBillNumber(): string
    {
        return $this->bill_number;
    }

    /**
     * @param string $bill_number
     */
    public function setBillNumber(string $bill_number): void
    {
        $this->bill_number = $bill_number;
    }

    /**
     * @return string|null
     */
    public function getLocalBillData(): ?string
    {
        return $this->local_bill_data;
    }

    /**
     * @param string|null $local_bill_data
     */
    public function setLocalBillData(?string $local_bill_data): void
    {
        $this->local_bill_data = $local_bill_data;
    }

    /**
     * @return int
     */
    public function getZohoVendorId(): int
    {
        return $this->zoho_vendor_id;
    }

    /**
     * @param int $zoho_vendor_id
     */
    public function setZohoVendorId(int $zoho_vendor_id): void
    {
        $this->zoho_vendor_id = $zoho_vendor_id;
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

    /**
     * @return string|null
     */
    public function getZohoBillData(): ?string
    {
        return $this->zoho_bill_data;
    }

    /**
     * @param string|null $zoho_bill_data
     */
    public function setZohoBillData(?string $zoho_bill_data): void
    {
        $this->zoho_bill_data = $zoho_bill_data;
    }



}