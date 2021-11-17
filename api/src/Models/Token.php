<?php


namespace Src\Models;
use Src\TableGateways\tokenGateway;
use Src\Traits\modelToString;

/**
 * Class Package
 * model for sr_packages table
 * @package Src\Models
 */
class Token extends BaseModel implements BaseModelInterface
{

    use modelToString;
    private tokenGateway $tokenGateway;


    public function __construct(
                                private int $id = 0,
                                private ?string $email = '',
                                private string $identifier = '',
                                private string $time = '',
                                private int $used = 0,
                                private int $token_type = 4,
                                private int $extra1 = 0,
                                private int $extra2 = 0,

                                private $db = null
    )
    {
        if($db != null)
        {
            $this->tokenGateway = new tokenGateway($db);
            parent::__construct($this->tokenGateway);
        }
    }


    // Custom Constructors //

    public static function withID($id, $db) {
        $instance = new self(db: $db);
        $row = $instance->getRecord($id);
        $instance->fill( $row );
        return $instance;
    }

    public static function withRef($ref, $db) {
        $instance = new self(db: $db);
        $row = $instance->getRecordAlt($ref);
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
            return $this->updateRecord();

        }else{
            // insert
            return $this->insertRecord();
        }
    }

    public function delete():int
    {
        return $this->deleteRecord($this->id);
    }

    public function fill(bool|array $row) {
        // fill all properties from array
        if($row)
        {
            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->identifier = $row['identifier'];
            $this->time = $row['time'];
            $this->used = $row['used'];
            $this->token_type = $row['token_type'];
            $this->extra1 = $row['extra1'];
            $this->extra2 = $row['extra2'];
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
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getTime(): string
    {
        return $this->time;
    }

    /**
     * @param string $time
     */
    public function setTime(string $time): void
    {
        $this->time = $time;
    }

    /**
     * @return int
     */
    public function getUsed(): int
    {
        return $this->used;
    }

    /**
     * @param int $used
     */
    public function setUsed(int $used): void
    {
        $this->used = $used;
    }

    /**
     * @return int
     */
    public function getTokenType(): int
    {
        return $this->token_type;
    }

    /**
     * @param int $token_type
     */
    public function setTokenType(int $token_type): void
    {
        $this->token_type = $token_type;
    }

    /**
     * @return int
     */
    public function getExtra1(): int
    {
        return $this->extra1;
    }

    /**
     * @param int $extra1
     */
    public function setExtra1(int $extra1): void
    {
        $this->extra1 = $extra1;
    }

    /**
     * @return int
     */
    public function getExtra2(): int
    {
        return $this->extra2;
    }

    /**
     * @param int $extra2
     */
    public function setExtra2(int $extra2): void
    {
        $this->extra2 = $extra2;
    }

}