<?php


namespace Src\Models;
use Src\TableGateways\AccountGateway;
use Src\Traits\modelToString;

/**
 * Class Package
 * model for sr_packages table
 * @package Src\Models
 */
class Account extends BaseModel implements BaseModelInterface
{

    use modelToString;
    private AccountGateway $accountGateway;
    private int $acc_id = 0;
    private string $acc_creation_date;


    public function __construct(
        private string $acc_name = '', // NOT NULL
        private int $enabled = 1,
        private int $billable = 1,
        private int $acc_retention_time = 14,
        private int $subscription_type = 0,
        private float $bill_rate1 = 0,
        private int $bill_rate1_type = 0,
        private int $bill_rate1_tat = 0,
        private string $bill_rate1_desc = "",
        private float $bill_rate1_min_pay = 0,
        private ?float $bill_rate2 = 0,
        private ?int $bill_rate2_type = 0,
        private ?int $bill_rate2_tat = 0,
        private ?string $bill_rate2_desc = '',
        private ?float $bill_rate2_min_pay = 0,
        private ?float $bill_rate3 = 0,
        private ?int $bill_rate3_type = 0,
        private ?int $bill_rate3_tat = 0,
        private ?string $bill_rate3_desc = '',
        private ?float $bill_rate3_min_pay = 0,
        private ?float $bill_rate4 = 0,
        private ?int $bill_rate4_type = 0,
        private ?int $bill_rate4_tat = 0,
        private ?string $bill_rate4_desc = '',
        private ?float $bill_rate4_min_pay = 0,
        private ?float $bill_rate5 = 0,
        private ?int $bill_rate5_type = 0,
        private ?int $bill_rate5_tat = 0,
        private ?string $bill_rate5_desc = '',
        private ?float $bill_rate5_min_pay = 0,
        private ?int $lifetime_minutes = 0,
        private ?string $work_types = '',
        private int $next_job_tally = 0,
        private int $act_log_retention_time = 180,
        private string $job_prefix = '',
        private int $sr_enabled = 0,
        private int $auto_list_refresh_interval = 0,
        private string $transcribe_remarks = '',

        private $db = null
    )
    {
        if($db != null)
        {
            $this->accountGateway = new AccountGateway($db);
            parent::__construct($this->accountGateway);
        }
    }

    /**
     * @return int
     */
    public function getSubscriptionType(): int
    {
        return $this->subscription_type;
    }

    /**
     * @param int $subscription_type
     */
    public function setSubscriptionType(int $subscription_type): void
    {
        $this->subscription_type = $subscription_type;
    }

    /**
     * @return int
     */
    public function getAutoListRefreshInterval(): int
    {
        return $this->auto_list_refresh_interval;
    }

    /**
     * @param int $auto_list_refresh_interval
     */
    public function setAutoListRefreshInterval(int $auto_list_refresh_interval): void
    {
        $this->auto_list_refresh_interval = $auto_list_refresh_interval;
    }

    /**
     * @return string
     */
    public function getTranscribeRemarks(): string
    {
        return $this->transcribe_remarks;
    }

    /**
     * @param string $transcribe_remarks
     */
    public function setTranscribeRemarks(string $transcribe_remarks): void
    {
        $this->transcribe_remarks = $transcribe_remarks;
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

        if($this->acc_id != 0)
        {
            // update
            return $this->updateRecord();

        }else{
            // insert
            // set creation date to NOW
            $this->acc_creation_date = date("Y-m-d H:i:s");
            return $this->insertRecord();
        }
    }

    public function delete():int
    {
        return $this->deleteRecord($this->acc_id);
    }

    public function fill(bool|array $row) {
        // fill all properties from array
        if($row)
        {
            $this->acc_id = $row['acc_id'];
            $this->enabled = $row['enabled'];
            $this->billable = $row['billable'];
            $this->acc_name = $row['acc_name'];
            $this->acc_retention_time = $row['acc_retention_time'];
            $this->subscription_type = $row['subscription_type'];
            $this->acc_creation_date = $row['acc_creation_date'];
            $this->bill_rate1 = $row['bill_rate1'];
            $this->bill_rate1_type = $row['bill_rate1_type'];
            $this->bill_rate1_tat = $row['bill_rate1_tat'];
            $this->bill_rate1_desc = $row['bill_rate1_desc'];
            $this->bill_rate1_min_pay = $row['bill_rate1_min_pay'];
            $this->bill_rate2 = $row['bill_rate2'];
            $this->bill_rate2_type = $row['bill_rate2_type'];
            $this->bill_rate2_tat = $row['bill_rate2_tat'];
            $this->bill_rate2_desc = $row['bill_rate2_desc'];
            $this->bill_rate2_min_pay = $row['bill_rate2_min_pay'];
            $this->bill_rate3 = $row['bill_rate3'];
            $this->bill_rate3_type = $row['bill_rate3_type'];
            $this->bill_rate3_tat = $row['bill_rate3_tat'];
            $this->bill_rate3_desc = $row['bill_rate3_desc'];
            $this->bill_rate3_min_pay = $row['bill_rate3_min_pay'];
            $this->bill_rate4 = $row['bill_rate4'];
            $this->bill_rate4_type = $row['bill_rate4_type'];
            $this->bill_rate4_tat = $row['bill_rate4_tat'];
            $this->bill_rate4_desc = $row['bill_rate4_desc'];
            $this->bill_rate4_min_pay = $row['bill_rate4_min_pay'];
            $this->bill_rate5 = $row['bill_rate5'];
            $this->bill_rate5_type = $row['bill_rate5_type'];
            $this->bill_rate5_tat = $row['bill_rate5_tat'];
            $this->bill_rate5_desc = $row['bill_rate5_desc'];
            $this->bill_rate5_min_pay = $row['bill_rate5_min_pay'];
            $this->lifetime_minutes = $row['lifetime_minutes'];
            $this->work_types = $row['work_types'];
            $this->next_job_tally = $row['next_job_tally'];
            $this->act_log_retention_time = $row['act_log_retention_time'];
            $this->job_prefix = $row['job_prefix'];
            $this->sr_enabled = $row['sr_enabled'];
            $this->auto_list_refresh_interval = $row['auto_list_refresh_interval'];
            $this->transcribe_remarks = $row['transcribe_remarks'];
        }
    }

    // Getter and setters

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
     * @return string
     */
    public function getAccCreationDate(): string
    {
        return $this->acc_creation_date;
    }

    /**
     * @param string $acc_creation_date
     */
    public function setAccCreationDate(string $acc_creation_date): void
    {
        $this->acc_creation_date = $acc_creation_date;
    }

    /**
     * @return string
     */
    public function getAccName(): string
    {
        return $this->acc_name;
    }

    /**
     * @param string $acc_name
     */
    public function setAccName(string $acc_name): void
    {
        $this->acc_name = $acc_name;
    }

    /**
     * @return int
     */
    public function getEnabled(): int
    {
        return $this->enabled;
    }

    /**
     * @param int $enabled
     */
    public function setEnabled(int $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return int
     */
    public function getBillable(): int
    {
        return $this->billable;
    }

    /**
     * @param int $billable
     */
    public function setBillable(int $billable): void
    {
        $this->billable = $billable;
    }

    /**
     * @return int
     */
    public function getAccRetentionTime(): int
    {
        return $this->acc_retention_time;
    }

    /**
     * @param int $acc_retention_time
     */
    public function setAccRetentionTime(int $acc_retention_time): void
    {
        $this->acc_retention_time = $acc_retention_time;
    }

    /**
     * @return float|int
     */
    public function getBillRate1(): float|int
    {
        return $this->bill_rate1;
    }

    /**
     * @param float|int $bill_rate1
     */
    public function setBillRate1(float|int $bill_rate1): void
    {
        $this->bill_rate1 = $bill_rate1;
    }

    /**
     * @return int
     */
    public function getBillRate1Type(): int
    {
        return $this->bill_rate1_type;
    }

    /**
     * @param int $bill_rate1_type
     */
    public function setBillRate1Type(int $bill_rate1_type): void
    {
        $this->bill_rate1_type = $bill_rate1_type;
    }

    /**
     * @return int
     */
    public function getBillRate1tat(): int
    {
        return $this->bill_rate1_tat;
    }

    /**
     * @param int $bill_rate1_tat
     */
    public function setBillRate1tat(int $bill_rate1_tat): void
    {
        $this->bill_rate1_tat = $bill_rate1_tat;
    }

    /**
     * @return string
     */
    public function getBillRate1Desc(): string
    {
        return $this->bill_rate1_desc;
    }

    /**
     * @param string $bill_rate1_desc
     */
    public function setBillRate1Desc(string $bill_rate1_desc): void
    {
        $this->bill_rate1_desc = $bill_rate1_desc;
    }

    /**
     * @return float|int
     */
    public function getBillRate1MinPay(): float|int
    {
        return $this->bill_rate1_min_pay;
    }

    /**
     * @param float|int $bill_rate1_min_pay
     */
    public function setBillRate1MinPay(float|int $bill_rate1_min_pay): void
    {
        $this->bill_rate1_min_pay = $bill_rate1_min_pay;
    }

    /**
     * @return float|int
     */
    public function getBillRate2(): float|int
    {
        return $this->bill_rate2;
    }

    /**
     * @return int|null
     */
    public function getBillRate2Type(): ?int
    {
        return $this->bill_rate2_type;
    }

    /**
     * @param int|null $bill_rate2_type
     */
    public function setBillRate2Type(?int $bill_rate2_type): void
    {
        $this->bill_rate2_type = $bill_rate2_type;
    }

    /**
     * @return int|null
     */
    public function getBillRate2Tat(): ?int
    {
        return $this->bill_rate2_tat;
    }

    /**
     * @param int|null $bill_rate2_tat
     */
    public function setBillRate2Tat(?int $bill_rate2_tat): void
    {
        $this->bill_rate2_tat = $bill_rate2_tat;
    }

    /**
     * @return string|null
     */
    public function getBillRate2Desc(): ?string
    {
        return $this->bill_rate2_desc;
    }

    /**
     * @param string|null $bill_rate2_desc
     */
    public function setBillRate2Desc(?string $bill_rate2_desc): void
    {
        $this->bill_rate2_desc = $bill_rate2_desc;
    }

    /**
     * @return float|int|null
     */
    public function getBillRate2MinPay(): float|int|null
    {
        return $this->bill_rate2_min_pay;
    }

    /**
     * @param float|int|null $bill_rate2_min_pay
     */
    public function setBillRate2MinPay(float|int|null $bill_rate2_min_pay): void
    {
        $this->bill_rate2_min_pay = $bill_rate2_min_pay;
    }

    /**
     * @return float|int|null
     */
    public function getBillRate3(): float|int|null
    {
        return $this->bill_rate3;
    }

    /**
     * @param float|int|null $bill_rate3
     */
    public function setBillRate3(float|int|null $bill_rate3): void
    {
        $this->bill_rate3 = $bill_rate3;
    }

    /**
     * @return int|null
     */
    public function getBillRate3Type(): ?int
    {
        return $this->bill_rate3_type;
    }

    /**
     * @param int|null $bill_rate3_type
     */
    public function setBillRate3Type(?int $bill_rate3_type): void
    {
        $this->bill_rate3_type = $bill_rate3_type;
    }

    /**
     * @return int|null
     */
    public function getBillRate3Tat(): ?int
    {
        return $this->bill_rate3_tat;
    }

    /**
     * @param int|null $bill_rate3_tat
     */
    public function setBillRate3Tat(?int $bill_rate3_tat): void
    {
        $this->bill_rate3_tat = $bill_rate3_tat;
    }

    /**
     * @return string|null
     */
    public function getBillRate3Desc(): ?string
    {
        return $this->bill_rate3_desc;
    }

    /**
     * @param string|null $bill_rate3_desc
     */
    public function setBillRate3Desc(?string $bill_rate3_desc): void
    {
        $this->bill_rate3_desc = $bill_rate3_desc;
    }

    /**
     * @return float|int|null
     */
    public function getBillRate3MinPay(): float|int|null
    {
        return $this->bill_rate3_min_pay;
    }

    /**
     * @param float|int|null $bill_rate3_min_pay
     */
    public function setBillRate3MinPay(float|int|null $bill_rate3_min_pay): void
    {
        $this->bill_rate3_min_pay = $bill_rate3_min_pay;
    }

    /**
     * @return float|int|null
     */
    public function getBillRate4(): float|int|null
    {
        return $this->bill_rate4;
    }

    /**
     * @param float|int|null $bill_rate4
     */
    public function setBillRate4(float|int|null $bill_rate4): void
    {
        $this->bill_rate4 = $bill_rate4;
    }

    /**
     * @return int|null
     */
    public function getBillRate4Type(): ?int
    {
        return $this->bill_rate4_type;
    }

    /**
     * @param int|null $bill_rate4_type
     */
    public function setBillRate4Type(?int $bill_rate4_type): void
    {
        $this->bill_rate4_type = $bill_rate4_type;
    }

    /**
     * @return int|null
     */
    public function getBillRate4Tat(): ?int
    {
        return $this->bill_rate4_tat;
    }

    /**
     * @param int|null $bill_rate4_tat
     */
    public function setBillRate4Tat(?int $bill_rate4_tat): void
    {
        $this->bill_rate4_tat = $bill_rate4_tat;
    }

    /**
     * @return string|null
     */
    public function getBillRate4Desc(): ?string
    {
        return $this->bill_rate4_desc;
    }

    /**
     * @param string|null $bill_rate4_desc
     */
    public function setBillRate4Desc(?string $bill_rate4_desc): void
    {
        $this->bill_rate4_desc = $bill_rate4_desc;
    }

    /**
     * @return float|int|null
     */
    public function getBillRate4MinPay(): float|int|null
    {
        return $this->bill_rate4_min_pay;
    }

    /**
     * @param float|int|null $bill_rate4_min_pay
     */
    public function setBillRate4MinPay(float|int|null $bill_rate4_min_pay): void
    {
        $this->bill_rate4_min_pay = $bill_rate4_min_pay;
    }

    /**
     * @return float|int|null
     */
    public function getBillRate5(): float|int|null
    {
        return $this->bill_rate5;
    }

    /**
     * @param float|int|null $bill_rate5
     */
    public function setBillRate5(float|int|null $bill_rate5): void
    {
        $this->bill_rate5 = $bill_rate5;
    }

    /**
     * @return int|null
     */
    public function getBillRate5Type(): ?int
    {
        return $this->bill_rate5_type;
    }

    /**
     * @param int|null $bill_rate5_type
     */
    public function setBillRate5Type(?int $bill_rate5_type): void
    {
        $this->bill_rate5_type = $bill_rate5_type;
    }

    /**
     * @return int|null
     */
    public function getBillRate5Tat(): ?int
    {
        return $this->bill_rate5_tat;
    }

    /**
     * @param int|null $bill_rate5_tat
     */
    public function setBillRate5Tat(?int $bill_rate5_tat): void
    {
        $this->bill_rate5_tat = $bill_rate5_tat;
    }

    /**
     * @return string|null
     */
    public function getBillRate5Desc(): ?string
    {
        return $this->bill_rate5_desc;
    }

    /**
     * @param string|null $bill_rate5_desc
     */
    public function setBillRate5Desc(?string $bill_rate5_desc): void
    {
        $this->bill_rate5_desc = $bill_rate5_desc;
    }

    /**
     * @return float|int|null
     */
    public function getBillRate5MinPay(): float|int|null
    {
        return $this->bill_rate5_min_pay;
    }

    /**
     * @param float|int|null $bill_rate5_min_pay
     */
    public function setBillRate5MinPay(float|int|null $bill_rate5_min_pay): void
    {
        $this->bill_rate5_min_pay = $bill_rate5_min_pay;
    }

    /**
     * @return int|null
     */
    public function getLifetimeMinutes(): ?int
    {
        return $this->lifetime_minutes;
    }

    /**
     * @param int|null $lifetime_minutes
     */
    public function setLifetimeMinutes(?int $lifetime_minutes): void
    {
        $this->lifetime_minutes = $lifetime_minutes;
    }

        /**
     * @return int|null
     */
    public function getAccJobRefreshInterval(): ?int
    {
        return $this->auto_list_refresh_interval;
    }

    /**
     * @param int|null $auto_list_refresh_interval
     */
    public function setAccJobRefreshInterval(?int $auto_list_refresh_interval): void
    {
        $this->auto_list_refresh_interval = $auto_list_refresh_interval;
    }

    /**
     * @return string|null
     */
    public function getWorkTypes(): ?string
    {
        return $this->work_types;
    }

    /**
     * @param string|null $work_types
     */
    public function setWorkTypes(?string $work_types): void
    {
        $this->work_types = $work_types;
    }



    /**
     * @return int
     */
    public function getNextJobTally(): int
    {
        return $this->next_job_tally;
    }

    /**
     * @param int $next_job_tally
     */
    public function setNextJobTally(int $next_job_tally): void
    {
        $this->next_job_tally = $next_job_tally;
    }

    /**
     * @return int
     */
    public function getActLogRetentionTime(): int
    {
        return $this->act_log_retention_time;
    }

    /**
     * @param int $act_log_retention_time
     */
    public function setActLogRetentionTime(int $act_log_retention_time): void
    {
        $this->act_log_retention_time = $act_log_retention_time;
    }

    /**
     * @return string
     */
    public function getJobPrefix(): string
    {
        return $this->job_prefix;
    }

    /**
     * @param string $job_prefix
     */
    public function setJobPrefix(string $job_prefix): void
    {
        $this->job_prefix = $job_prefix;
    }

    /**
     * @return int
     */
    public function getSrEnabled(): int
    {
        return $this->sr_enabled;
    }

    /**
     * @param int $sr_enabled
     */
    public function setSrEnabled(int $sr_enabled): void
    {
        $this->sr_enabled = $sr_enabled;
    }

}