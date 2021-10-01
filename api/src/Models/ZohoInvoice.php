<?php


namespace Src\Models;
use Src\TableGateways\zohoGateway;
use Src\Traits\modelToString;

/**
 * Class Package
 * model for sr_packages table
 * @package Src\Models
 */
class ZohoInvoice implements BaseModelInterface
{

    use modelToString;
    private zohoGateway $zohoGateway;


    public function __construct(
                                private int $id = 0,
//                                private int $zoho_id = 0,
                                private string $invoice_number = '',
                                private int $zoho_contact_id = 0,
                                private string $zoho_invoice_id = '',
                                private ?string $local_invoice_data = null,
                                private ?string $zoho_invoice_data = null,
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
    public function getZohoInvoiceId(): int
    {
        return $this->zoho_invoice_id;
    }

    /**
     * @param int $zoho_invoice_id
     */
    public function setZohoInvoiceId(int $zoho_invoice_id): void
    {
        $this->zoho_invoice_id = $zoho_invoice_id;
    }


    // Custom Constructors //

    public function withID($invoice_number, $db) {
        $instance = new self(db: $db);
        $row = $instance->zohoGateway->findZohoInvoice($invoice_number);
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
            return $this->zohoGateway->updateZohoInvoice($this);

        }else{
            // insert
            $this->id = $this->zohoGateway->insertZohoInvoice($this);
            return $this->id;
        }
    }

    public function delete():int
    {
        return $this->zohoGateway->deleteZohoInvoice($this->zoho_invoice_id);
    }

    public function fill(bool|array $row) {
        // fill all properties from array
        if($row)
        {
            $this->id = $row['id'];
//            $this->zoho_id = $row['zoho_id'];
            $this->invoice_number = $row['invoice_number'];
            $this->zoho_contact_id = $row['zoho_contact_id'];
            $this->zoho_invoice_id = $row['zoho_invoice_id'];
            $this->local_invoice_data = $row['local_invoice_data'];
            $this->zoho_invoice_data = $row['zoho_invoice_data'];
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
    public function getInvoiceNumber(): string
    {
        return $this->invoice_number;
    }

    /**
     * @param string $invoice_number
     */
    public function setInvoiceNumber(string $invoice_number): void
    {
        $this->invoice_number = $invoice_number;
    }

    /**
     * @return string|null
     */
    public function getLocalInvoiceData(): ?string
    {
        return $this->local_invoice_data;
    }

    /**
     * @param string|null $local_invoice_data
     */
    public function setLocalInvoiceData(?string $local_invoice_data): void
    {
        $this->local_invoice_data = $local_invoice_data;
    }

    /**
     * @return int
     */
    public function getZohoContactId(): int
    {
        return $this->zoho_contact_id;
    }

    /**
     * @param int $zoho_contact_id
     */
    public function setZohoContactId(int $zoho_contact_id): void
    {
        $this->zoho_contact_id = $zoho_contact_id;
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
    public function getZohoInvoiceData(): ?string
    {
        return $this->zoho_invoice_data;
    }

    /**
     * @param string|null $zoho_invoice_data
     */
    public function setZohoInvoiceData(?string $zoho_invoice_data): void
    {
        $this->zoho_invoice_data = $zoho_invoice_data;
    }



}