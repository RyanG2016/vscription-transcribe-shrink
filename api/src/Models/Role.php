<?php


namespace Src\Models;
use Src\TableGateways\roleGateway;
use Src\Traits\modelToString;

/**
 * Class Package
 * model for sr_packages table
 * @package Src\Models
 */
class Role extends BaseModel implements BaseModelInterface
{

    use modelToString;
    private roleGateway $roleGateway;


    public function __construct(
                                private int $role_id = 0,
                                private string $role_name = "", //  23 max
                                private string $role_desc = "", // 255 max

                                private $db = null
    )
    {
        if($db != null)
        {
            $this->roleGateway = new roleGateway($db);
            parent::__construct($this->roleGateway);
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

        if($this->role_id != 0)
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
        return $this->deleteRecord($this->role_id);
    }

    public function fill(bool|array $row) {
        // fill all properties from array
        if($row)
        {
            $this->role_id = $row['role_id'];
            $this->role_name = $row['role_name'];
            $this->role_desc = $row['role_desc'];
        }
    }

    /**
     * @return int
     */
    public function getRoleId(): int
    {
        return $this->role_id;
    }

    /**
     * @param int $role_id
     */
    public function setRoleId(int $role_id): void
    {
        $this->role_id = $role_id;
    }

    /**
     * @return string
     */
    public function getRoleName(): string
    {
        return $this->role_name;
    }

    /**
     * @param string $role_name
     */
    public function setRoleName(string $role_name): void
    {
        $this->role_name = $role_name;
    }

    /**
     * @return string
     */
    public function getRoleDesc(): string
    {
        return $this->role_desc;
    }

    /**
     * @param string $role_desc
     */
    public function setRoleDesc(string $role_desc): void
    {
        $this->role_desc = $role_desc;
    }

}