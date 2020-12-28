<?php


namespace Src\Models;
use Src\TableGateways\UserGateway;
use Src\Traits\modelToString;

/**
 * Class User
 * model for users table
 * @package Src\Models
 */
class User extends BaseModel implements BaseModelInterface
{

    use modelToString;
    private UserGateway $UserGateway;
    public int $id;

    public function __construct(
                                public string $first_name,
                                public string $last_name,
                                public string $email,
                                public string $password,
                                public ?string $city = null,
                                public ?string $country = null,
                                public ?string $zipcode = null,

                                public ?string $state = null,
                                public string $address = '',

                                private $db = null
    )
    {
        if($db != null)
        {
            $this->UserGateway = new UserGateway($db);
            parent::__construct($this->UserGateway);
        }
    }


    // Custom Constructors //

    public static function withID($id, $db) {
        $instance = new self("","", "","", db: $db);
        $row = $instance->getRecord($id);
        $instance->fill( $row );
        return $instance;
    }

    public static function withRow( ?array $row, $db = null ) {
        if($row)
        {
            $instance = new self("","", "","", db: $db);
            $instance->fill( $row );
            return $instance;
        }else{
            return null;
        }
    }


/*

    public static function withAccID($account_id, $db) {
        $instance = new self(db: $db);
        $instance->setAccountId($account_id);
        $instance->loadFromDBwithAccID($account_id);
        return $instance;
    }*/


    // Getters and Setters //

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     */
    public function setFirstName(string $first_name): void
    {
        $this->first_name = $first_name;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name
     */
    public function setLastName(string $last_name): void
    {
        $this->last_name = $last_name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string|null $country
     */
    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return string|null
     */
    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    /**
     * @param string|null $zipcode
     */
    public function setZipcode(?string $zipcode): void
    {
        $this->zipcode = $zipcode;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string|null $state
     */
    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
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



    // Interface Functions ---------------------

    public function save():int{

        if($this->getId() != 0)
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
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->email = $row['email'];
            $this->password = $row['password'];
//            $this->country_id = $row['country_id'];
            $this->city = $row['city'];
            $this->country = $row['country'];
            $this->zipcode = $row['zipcode'];
//            $this->state_id = $row['state_id'];
            $this->state = $row['state'];
            $this->address = $row['address'];
//            $this->registeration_date = $row['registeration_date'];
//            $this->last_ip_address = $row['last_ip_address'];
//            $this->typist = $row['typist'];
//            $this->account_status = $row['account_status'];
//            $this->last_login = $row['last_login'];
//            $this->trials = $row['trials'];
//            $this->unlock_time = $row['unlock_time'];
//            $this->newsletter = $row['newsletter'];
//            $this->def_access_id = $row['def_access_id'];
//            $this->shortcuts = $row['shortcuts'];
//            $this->dictionary = $row['dictionary'];
//            $this->email_notification = $row['email_notification'];
//            $this->enabled = $row['enabled'];
//            $this->account = $row['account'];
//            $this->tutorials = $row['tutorials'];
        }
    }



    // Custom DB queries -------------------
/*
    protected function loadFromDBwithAccID($account_id) {
        $row = $this->UserGateway->findByAccID($account_id);
        $this->fill( $row );
    }*/

}