<?php


namespace Src\Models;
use Src\Enums\SESSION_SRC;
use Src\TableGateways\sessionGateway;
use Src\Traits\modelToString;

/**
 * Class Package
 * model for sr_packages table
 * @package Src\Models
 */
class Session implements BaseModelInterface
{

    use modelToString;
    private sessionGateway $sessionGateway;


    /**
     * @param int $id
     * @param int $uid
     * @param string $php_sess_id
     * @param int $src
     * @param int $revoked
     * @param string|null $revoke_date
     * @param string $login_time
     * @param string $expire_time
     * @param null $db
     */
    public function __construct(
                                private int $id = 0,
                                private int $uid = 0,
                                private string $php_sess_id = '',
                                private int $src = 0,
                                private int $revoked = 0,
                                private ?string $revoke_date = null,
                                private string $login_time = '',
                                private string $expire_time = '',
                                private ?string $ip_address = null,

                                private $db = null
    )
    {
        if($this->login_time == '') {
            $this->login_time = date("Y-m-d H:i:s");
        }
        if($this->expire_time == '') {
            $this->expire_time = date("Y-m-d H:i:s", strtotime(SESSION_SRC::DEF_EXPIRE_TIME));
        }
        if($db != null)
        {
            $this->sessionGateway = new sessionGateway($db);
        }
    }

    /**
     * @return string|null
     */
    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    /**
     * @param string|null $ip_address
     */
    public function setIpAddress(?string $ip_address): void
    {
        $this->ip_address = $ip_address;
    }

    /**
     * @return string
     */
    public function getPhpSessId(): string
    {
        return $this->php_sess_id;
    }

    /**
     * @param string $php_sess_id
     */
    public function setPhpSessId(string $php_sess_id): void
    {
        $this->php_sess_id = $php_sess_id;
    }

    // Custom Constructors //

    public static function withID($id, $db) {
        $instance = new self(db: $db);
        $row = $instance->sessionGateway->findSession($id);
        if(!$row)
        {
            return null;
        }
        $instance->fill( $row );
        return $instance;
    }

    public static function withPhpSessID($php_session_id, $db) {
        $instance = new self(db: $db);
        $row = $instance->sessionGateway->findSessionWithPHPSessID($php_session_id);
        if(!$row) return false;
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
            return $this->sessionGateway->updateSession($this);

        }else{
            // insert
            $this->id = $this->sessionGateway->insertSession($this);
            return $this->id;
        }
    }

    public function delete():int
    {
        return $this->sessionGateway->deleteSession($this->id);
    }

    public function fill(bool|array $row) {
        // fill all properties from array
        if($row)
        {
            $this->id = $row['id'];
            $this->uid = $row['uid'];
            $this->php_sess_id = $row['php_sess_id'];
            $this->src = $row['src'];
            $this->revoked = $row['revoked'];
            $this->revoke_date = $row['revoke_date'];
            $this->ip_address = $row['ip_address'];
            $this->login_time = $row['login_time'];
            $this->expire_time = $row['expire_time'];
        }
    }

    // Getter/Setters Functions ---------------------

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
     * @return int
     */
    public function getSrc(): int
    {
        return $this->src;
    }

    /**
     * @param int $src
     */
    public function setSrc(int $src): void
    {
        $this->src = $src;
    }

    /**
     * @return int
     */
    public function getRevoked(): int
    {
        return $this->revoked;
    }

    /**
     * @param int $revoked
     */
    public function setRevoked(int $revoked): void
    {
        $this->revoked = $revoked;
    }

    /**
     * @return string|null
     */
    public function getRevokeDate(): ?string
    {
        return $this->revoke_date;
    }

    /**
     * @param string|null $revoke_date
     */
    public function setRevokeDate(?string $revoke_date): void
    {
        $this->revoke_date = $revoke_date;
    }

    /**
     * @return string
     */
    public function getLoginTime(): string
    {
        return $this->login_time;
    }

    /**
     * @param string $login_time
     */
    public function setLoginTime(string $login_time): void
    {
        $this->login_time = $login_time;
    }

    /**
     * @return string
     */
    public function getExpireTime(): string
    {
        return $this->expire_time;
    }

    /**
     * @param string $expire_time
     */
    public function setExpireTime(string $expire_time): void
    {
        $this->expire_time = $expire_time;
    }


}