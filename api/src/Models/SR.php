<?php


namespace Src\Models;
use Src\TableGateways\SRGateway;
use Src\Traits\modelToString;

/**
 * Class SR
 * model for speech_recognition table
 * @package Src\Models
 */
class SR extends BaseModel implements BaseModelInterface
{

    use modelToString;
    private SRGateway $srGateway;


    public function __construct(public int $sr_id = 0,
                                public ?int $account_id = null,
                                public ?float $sr_rate = 0.00,
                                public ?float $sr_flat_rate = 0.00,
                                public ?string $sr_vocab = "",
                                public ?float $sr_minutes_remaining = 0.00,
                                private $db = null
    )
    {
        if($db != null)
        {
            $this->srGateway = new SRGateway($db);
            parent::__construct($this->srGateway);
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


    public static function withAccID($account_id, $db) {
        $instance = new self(db: $db);
        $instance->setAccountId($account_id);
        $instance->loadFromDBwithAccID($account_id);
        return $instance;
    }


    // Getters and Setters //

    /**
     * @return int
     */
    public function getSrId(): int
    {
        return $this->sr_id;
    }

    /**
     * @param int $sr_id
     */
    public function setSrId(int $sr_id): void
    {
        $this->sr_id = $sr_id;
    }

    /**
     * @return int|null
     */
    public function getAccountId(): ?int
    {
        return $this->account_id;
    }

    /**
     * @param int|null $account_id
     */
    public function setAccountId(?int $account_id): void
    {
        $this->account_id = $account_id;
    }

    /**
     * @return float|null
     */
    public function getSrRate(): ?float
    {
        return $this->sr_rate;
    }

    /**
     * @param float|null $sr_rate
     */
    public function setSrRate(?float $sr_rate): void
    {
        $this->sr_rate = $sr_rate;
    }

    /**
     * @return float|null
     */
    public function getSrFlatRate(): ?float
    {
        return $this->sr_flat_rate;
    }

    /**
     * @param float|null $sr_flat_rate
     */
    public function setSrFlatRate(?float $sr_flat_rate): void
    {
        $this->sr_flat_rate = $sr_flat_rate;
    }

    /**
     * @return string|null
     */
    public function getSrVocab(): ?string
    {
        return $this->sr_vocab;
    }

    /**
     * @param string|null $sr_vocab
     */
    public function setSrVocab(?string $sr_vocab): void
    {
        $this->sr_vocab = $sr_vocab;
    }

    /**
     * @return float|null
     */
    public function getSrMinutesRemaining(): ?float
    {
        return $this->sr_minutes_remaining;
    }

    /**
     * @param float|null $sr_minutes_remaining
     */
    public function setSrMinutesRemaining(?float $sr_minutes_remaining): void
    {
        $this->sr_minutes_remaining = $sr_minutes_remaining;
    }

    // Interface Functions ---------------------

    public function save():int{

        if($this->getSrId() != 0)
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
        return $this->deleteRecord($this->sr_id);
    }

    public function fill(bool|array $row) {
        // fill all properties from array
        if($row)
        {
            $this->sr_id = $row['sr_id'];
            $this->account_id = $row['account_id'];
            $this->sr_rate = $row['sr_rate'];
            $this->sr_flat_rate = $row['sr_flat_rate'];
            $this->sr_vocab = $row['sr_vocab'];
            $this->sr_minutes_remaining = $row['sr_minutes_remaining'];
        }
    }


    // Custom DB queries -------------------

    protected function loadFromDBwithAccID($account_id) {
        $row = $this->srGateway->findByAccID($account_id);
        $this->fill( $row );
    }

}