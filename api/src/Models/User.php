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
<<<<<<< HEAD
                                public ?string $card_number = null,
                                public ?string $expiration_date =null,
                                public ?string $security_code = null,
=======
                                public string $card_number = '',
                                public string $expiration_date ='',
                                public string $security_code = '',
>>>>>>> 2f8cc4abb12dfe0b4721f8935ee2c7704207c5b5

                                public int $email_notification = 1,
                                public int $newsletter = 0,
                                public int $account_status = 5,
                                public int $account = 0,
                                public int $typist = 0,
                                private $db = null
    )
    {
        if($db != null)
        {
            $this->UserGateway = new UserGateway($db);
            parent::__construct($this->UserGateway);
        }
    }

    /**
     * @return int
     */
    public function getEmailNotification(): int
    {
        return $this->email_notification;
    }

    /**
     * @param int $email_notification
     */
    public function setEmailNotification(int $email_notification): void
    {
        $this->email_notification = $email_notification;
    }

    /**
     * @return int
     */
    public function getNewsletter(): int
    {
        return $this->newsletter;
    }

    /**
     * @param int $newsletter
     */
    public function setNewsletter(int $newsletter): void
    {
        $this->newsletter = $newsletter;
    }

    /**
     * @return int
     */
    public function getAccountStatus(): int
    {
        return $this->account_status;
    }

    /**
     * @param int $account_status
     */
    public function setAccountStatus(int $account_status): void
    {
        $this->account_status = $account_status;
    }

    /**
     * @return int
     */
    public function getTypist(): int
    {
        return $this->typist;
    }

    /**
     * @param int $typist
     */
    public function setTypist(int $typist): void
    {
        $this->typist = $typist;
    }


    // Custom Constructors //

    public static function withID($id, $db) {
        $instance = new self("","", "","", db: $db);
        $row = $instance->getRecord($id);
        $instance->fill( $row );
        return $instance;
    }

    public static function withEmail($email, $db) {
        $instance = new self("","", "","", db: $db);
        $row = $instance->getRecordAlt($email);
        if($row)
        {
            $instance->fill( $row );
        }else{
            return false;
        }
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
    public function getFullName(): string
    {
        return $this->first_name . " " . $this->last_name;
    }

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
    public function setCardNumber(string $card_number): void
    {
        $this->card_number = $card_number;
    }
<<<<<<< HEAD
    public function getCardNumber(): ?string
=======
    public function getCardNumber(): string
>>>>>>> 2f8cc4abb12dfe0b4721f8935ee2c7704207c5b5
    {
        return $this->card_number;
    }

    public function setExpirationDate(string $expiration_date): void
    {
        $this->expiration_date = $expiration_date;
    }
<<<<<<< HEAD
    public function getExpirationDate(): ?string
=======
    public function getExpirationDate(): string
>>>>>>> 2f8cc4abb12dfe0b4721f8935ee2c7704207c5b5
    {
        return $this->expiration_date;
    }

    public function setSecurityCode(string $security_code): void
    {
        $this->security_code = $security_code;
    }
<<<<<<< HEAD
     public function getSecurityCode(): ?string
=======
    public function getSecurityCode(): string
>>>>>>> 2f8cc4abb12dfe0b4721f8935ee2c7704207c5b5
    {
        return $this->security_code;
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

            $this->city = $row['city'];
            $this->country = $row['country'];
            $this->zipcode = $row['zipcode'];

            $this->state = $row['state'];
            $this->address = $row['address'];
//            $this->registeration_date = $row['registeration_date'];
//            $this->last_ip_address = $row['last_ip_address'];
            $this->typist = $row['typist'];
            $this->account_status = $row['account_status'];
//            $this->last_login = $row['last_login'];
//            $this->trials = $row['trials'];
//            $this->unlock_time = $row['unlock_time'];
            $this->newsletter = $row['newsletter'];
//            $this->def_access_id = $row['def_access_id'];
//            $this->shortcuts = $row['shortcuts'];
//            $this->dictionary = $row['dictionary'];
            $this->email_notification = $row['email_notification'];
//            $this->enabled = $row['enabled'];
            $this->account = $row['account'];
            $this->card_number = $row['card_number'];
            $this->expiration_date = $row['expiration_date'];
            $this->security_code = $row['security_code'];
//            $this->tutorials = $row['tutorials'];
        }
    }

    /**
     * @return int
     */
    public function getAccount(): int
    {
        return $this->account;
    }

    /**
     * @param int $account
     */
    public function setAccount(int $account): void
    {
        $this->account = $account;
    }



    // Custom DB queries -------------------
/*
    protected function loadFromDBwithAccID($account_id) {
        $row = $this->UserGateway->findByAccID($account_id);
        $this->fill( $row );
    }*/

}