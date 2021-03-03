<?php


namespace Src\Models;
use Src\TableGateways\PackageGateway;
use Src\Traits\modelToString;

/**
 * Class Package
 * model for sr_packages table
 * @package Src\Models
 */
class Package extends BaseModel implements BaseModelInterface
{

    use modelToString;
    private PackageGateway $pkgGateway;


    public function __construct(public int $srp_id = 0,
                                public string $srp_name = '',
                                public float $srp_minutes = 0.00,
                                public ?float $srp_price = 0.00,
                                public string $srp_desc = '',
                                private $db = null
    )
    {
        if($db != null)
        {
            $this->pkgGateway = new PackageGateway($db);
            parent::__construct($this->pkgGateway);
        }
    }


    // Custom Constructors //

    public static function withID($id, $db) {
        $instance = new self(db: $db);
        $row = $instance->getRecord($id);
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

        if($this->srp_id != 0)
        {
            // update
            return $this->updateRecord();

        }else{
            // insert
            return $this->insertRecord();
        }
    }

    public function delete():int
    {
        return $this->deleteRecord($this->srp_id);
    }

    public function fill(bool|array $row) {
        // fill all properties from array
        if($row)
        {
            $this->srp_id = $row['srp_id'];
            $this->srp_name = $row['srp_name'];
            $this->srp_minutes = $row['srp_minutes'];
            $this->srp_price = $row['srp_price'];
            $this->srp_desc = $row['srp_desc'];

        }
    }

    /**
     * @return float
     */
    public function getSrpMinutes(): float
    {
        return $this->srp_minutes;
    }

    /**
     * @param float $srp_minutes
     */
    public function setSrpMinutes(float $srp_minutes): void
    {
        $this->srp_minutes = $srp_minutes;
    }

    /**
     * @return string
     */
    public function getSrpDesc(): string
    {
        return $this->srp_desc;
    }

    /**
     * @param string $srp_desc
     */
    public function setSrpDesc(string $srp_desc): void
    {
        $this->srp_desc = $srp_desc;
    }



    // getters and setters //////

    /**
     * @return int
     */
    public function getSrpId(): int
    {
        return $this->srp_id;
    }

    /**
     * @param int $srp_id
     */
    public function setSrpId(int $srp_id): void
    {
        $this->srp_id = $srp_id;
    }

    /**
     * @return string
     */
    public function getSrpName(): string
    {
        return $this->srp_name;
    }

    /**
     * @param string $srp_name
     */
    public function setSrpName(string $srp_name): void
    {
        $this->srp_name = $srp_name;
    }

    /**
     * @return float
     */
    public function getSrpMins(): float
    {
        return $this->srp_minutes;
    }

    /**
     * @param float $srp_mins
     */
    public function setSrpMins(float $srp_mins): void
    {
        $this->srp_minutes = $srp_mins;
    }

    /**
     * @return float|null
     */
    public function getSrpPrice(): ?float
    {
        return $this->srp_price;
    }

    /**
     * @param float|null $srp_price
     */
    public function setSrpPrice(?float $srp_price): void
    {
        $this->srp_price = $srp_price;
    }
}