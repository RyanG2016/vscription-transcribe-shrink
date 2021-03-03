<?php


namespace Src\Models;
use Src\Enums\PAYMENT_STATUS;
use Src\TableGateways\paymentGateway;
use Src\Traits\modelToString;


/**
 * Class Payment
 * model for payments table
 * @package Src\Models
 */
class Payment extends BaseModel implements BaseModelInterface
{

    use modelToString;
    private paymentGateway $paymentGateway;


    public function __construct(public ?int $payment_id = 0,
                                public int $user_id = 0,
                                public float $amount = 0.00,
                                public ?string $ref_id = null,
                                public ?string $trans_id = null,
                                public ?string $payment_json = null,
                                public ?int $pkg_id = null,
                                public int $status = PAYMENT_STATUS::RECORDED,
                                private $db = null
    )
    {
        if($db != null)
        {
            $this->paymentGateway = new paymentGateway($db);
            parent::__construct($this->paymentGateway);
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

    /**
     * @return int
     */
    public function getPaymentId(): int
    {
        return $this->payment_id;
    }

    /**
     * @param int $payment_id
     */
    public function setPaymentId(int $payment_id): void
    {
        $this->payment_id = $payment_id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string|null
     */
    public function getRefId(): ?string
    {
        return $this->ref_id;
    }

    /**
     * @return string|null
     */
    public function getTransId(): ?string
    {
        return $this->trans_id;
    }

    /**
     * @param string|null $trans_id
     */
    public function setTransId(?string $trans_id): void
    {
        $this->trans_id = $trans_id;
    }

    /**
     * @return string|null
     */
    public function getPaymentJson(): ?string
    {
        return $this->payment_json;
    }

    /**
     * @param string|null $payment_json
     */
    public function setPaymentJson(?string $payment_json): void
    {
        $this->payment_json = $payment_json;
    }

    /**
     * @param string|null $ref_id
     */
    public function setRefId(?string $ref_id): void
    {
        $this->ref_id = $ref_id;
    }

    /**
     * @return int|null
     */
    public function getPkgId(): ?int
    {
        return $this->pkg_id;
    }

    /**
     * @param int|null $pkg_id
     */
    public function setPkgId(?int $pkg_id): void
    {
        $this->pkg_id = $pkg_id;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }


    public static function withrefID($refID, $db) {
        $instance = new self(db: $db);
        $instance->setRefId($refID);
        $record = $instance->getRecordAlt($refID);
        if($record)
        {
            $instance->fill($record);
        }else{
            return null;
        }
        return $instance;
    }


    // Getters and Setters //


    // Interface Functions ---------------------

    public function save():int{

        if($this->payment_id != 0)
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
        return $this->deleteRecord($this->payment_id);
    }

    public function fill(bool|array $row) {
        // fill all properties from array
        if($row)
        {
            $this->payment_id = $row['payment_id'];
            $this->user_id = $row['user_id'];
            $this->amount = $row['amount'];
            $this->ref_id = $row['ref_id'];
            $this->status = $row['status'];
            $this->payment_json = $row['payment_json'];
            $this->trans_id = $row['trans_id'];
            $this->pkg_id = $row['pkg_id'];
        }
    }


    // Custom DB queries -------------------

}