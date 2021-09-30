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
                                private int $zoho_id = 0,
                                private string $invoice_number = '',
                                private ?string $invoice_data = null,
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

        if($this->zoho_id != 0)
        {
            // update
            return $this->zohoGateway->updateZohoInvoice($this);

        }else{
            // insert
            $this->zoho_id = $this->zohoGateway->insertZohoInvoice($this);
            return $this->zoho_id;
        }
    }

    public function delete():int
    {
        return $this->zohoGateway->deleteZohoInvoice($this->zoho_id);
    }

    public function fill(bool|array $row) {
        // fill all properties from array
        if($row)
        {
            $this->id = $row['id'];
            $this->zoho_id = $row['zoho_id'];
            $this->invoice_number = $row['invoice_number'];
            $this->invoice_data = $row['invoice_data'];
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
    public function getInvoiceData(): ?string
    {
        return $this->invoice_data;
    }

    /**
     * @param string|null $invoice_data
     */
    public function setInvoiceData(?string $invoice_data): void
    {
        $this->invoice_data = $invoice_data;
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