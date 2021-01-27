<?php


namespace Src\Models;
use Src\TableGateways\accessGateway;
use Src\Traits\modelToString;

/**
 * Class Package
 * model for sr_packages table
 * @package Src\Models
 */
class Access extends BaseModel implements BaseModelInterface
{

    use modelToString;
    private accessGateway $accessGateway;


    public function __construct(
                                private int $access_id = 0,
                                private int $acc_id = 0,
                                private int $uid = 0,
                                private ?string $username = "",
                                private int $acc_role = 0,

                                private $db = null
    )
    {
        if($db != null)
        {
            $this->accessGateway = new accessGateway($db);
            parent::__construct($this->accessGateway);
        }
    }


    // Custom Constructors //

    public static function withID($id, $db) {
        $instance = new self(db: $db);
        $row = $instance->getRecord($id);
        if(!$row)
        {
            return null;
        }
        $instance->fill( $row );
        return $instance;
    }

    public static function getHighestAccessWithID($id, $db) {
        $instance = new self(db: $db);
        $row = $instance->accessGateway->findHighestAccessModel($id);
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

        if($this->access_id != 0)
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
        return $this->deleteRecord($this->access_id);
    }

    public function fill(bool|array $row) {
        // fill all properties from array
        if($row)
        {
            $this->access_id = $row['access_id'];
            $this->acc_id = $row['acc_id'];
            $this->uid = $row['uid'];
            $this->username = $row['username'];
            $this->acc_role = $row['acc_role'];
        }
    }

    /**
     * @return int
     */
    public function getAccessId(): int
    {
        return $this->access_id;
    }

    /**
     * @param int $access_id
     */
    public function setAccessId(int $access_id): void
    {
        $this->access_id = $access_id;
    }

    /**
     * @return int
     */
    public function getAccId(): int
    {
        return $this->acc_id;
    }

    /**
     * @param int $acc_id
     */
    public function setAccId(int $acc_id): void
    {
        $this->acc_id = $acc_id;
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
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return int
     */
    public function getAccRole(): int
    {
        return $this->acc_role;
    }

    /**
     * @param int $acc_role
     */
    public function setAccRole(int $acc_role): void
    {
        $this->acc_role = $acc_role;
    }


}