<?php


namespace Src\Models;
use Src\TableGateways\PHPServicesGateway;
use Src\Traits\modelToString;

/**
 * Class PHPService
 * model for sr_packages table
 * @PHPService Src\Models
 */
class PHPService extends BaseModel implements BaseModelInterface
{

    use modelToString;
    private PHPServicesGateway $phpServiceGateway;


    public function __construct(public int $service_id = 0,
                                public string $service_name = '',
                                public ?string $last_start_time = null,
                                public ?string $last_stop_time = null,
                                public ?string $revai_start_window = null,
                                public int $requests_made = 0,
                                public int $current_status = 0,
                                private $db = null
    )
    {
        if($db != null)
        {
            $this->phpServiceGateway = new PHPServicesGateway($db);
            parent::__construct($this->phpServiceGateway);
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

        if($this->service_id != 0)
        {
            // update
            return $this->updateRecord();

        }else{
            // insert
            return $this->insertRecord();
        }
    }

    public function updateRequests(int $requests):int{
        $this->requests_made = $requests;
        return $this->phpServiceGateway->updateRequests($this);
    }

    public function updateStartTime():int{
        $this->last_start_time = date("Y-m-d H:i:s");
        $this->current_status = 1;
        return $this->phpServiceGateway->updateStartTime($this);
    }

    public function updateStopTime():int{
        $this->last_stop_time = date("Y-m-d H:i:s");
        $this->current_status = 0;
        return $this->phpServiceGateway->updateStopTime($this);
    }

    public function delete():int
    {
        return $this->deleteRecord($this->service_id);
    }

    public function fill(bool|array $row) {
        // fill all properties from array
        if($row)
        {
            $this->service_id = $row['service_id'];
            $this->service_name = $row['service_name'];
            $this->last_start_time = $row['last_start_time'];
            $this->last_stop_time = $row['last_stop_time'];
            $this->revai_start_window = $row['revai_start_window'];
            $this->requests_made = $row['requests_made'];
            $this->current_status = $row['current_status'];

        }
    }

    // getters and setters //////

    /**
     * @return int
     */
    public function getServiceId(): int
    {
        return $this->service_id;
    }

    /**
     * @param int $service_id
     */
    public function setServiceId(int $service_id): void
    {
        $this->service_id = $service_id;
    }

    /**
     * @return string
     */
    public function getServiceName(): string
    {
        return $this->service_name;
    }

    /**
     * @param string $service_name
     */
    public function setServiceName(string $service_name): void
    {
        $this->service_name = $service_name;
    }

    /**
     * @return string|null
     */
    public function getLastStartTime(): ?string
    {
        return $this->last_start_time;
    }

    /**
     * @param string|null $last_start_time
     */
    public function setLastStartTime(?string $last_start_time): void
    {
        $this->last_start_time = $last_start_time;
    }

    /**
     * @return string|null
     */
    public function getLastStopTime(): ?string
    {
        return $this->last_stop_time;
    }

    /**
     * @param string|null $last_stop_time
     */
    public function setLastStopTime(?string $last_stop_time): void
    {
        $this->last_stop_time = $last_stop_time;
    }

    /**
     * @return int
     */
    public function getRequestsMade(): int
    {
        return $this->requests_made;
    }

    /**
     * @param int $requests_made
     */
    public function setRequestsMade(int $requests_made): void
    {
        $this->requests_made = $requests_made;
    }

    /**
     * @return int
     */
    public function getCurrentStatus(): int
    {
        return $this->current_status;
    }

    /**
     * @param int $current_status
     */
    public function setCurrentStatus(int $current_status): void
    {
        $this->current_status = $current_status;
    }

    /**
     * @return string|null
     */
    public function getRevaiStartWindow(): ?string
    {
        return $this->revai_start_window;
    }

    /**
     * @param string|null $revai_start_window
     */
    public function setRevaiStartWindow(?string $revai_start_window): void
    {
        $this->revai_start_window = $revai_start_window;
    }

}