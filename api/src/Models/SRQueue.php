<?php


namespace Src\Models;
use Src\Enums\SRQ_STATUS;
use Src\TableGateways\SRQueueGateway;
use Src\Traits\modelToString;

/**
 * Class SRQueue
 * model for sr_queue table
 * @package Src\Models
 */
class SRQueue extends BaseModel implements BaseModelInterface
{
    use modelToString;

    private SRQueueGateway $srqGateway;

    public function __construct(
        private int $file_id,
        private int $srq_status = SRQ_STATUS::QUEUED,
        private ?string $srq_revai_id = null,
        private float $srq_revai_minutes = 0.00,
        private ?string $notes = null,
        private int $srq_id = 0,
        private ?string $srq_tmp_filename = null,
        private $db = null
    )
    {
        if($db != null)
        {
            $this->srqGateway = new SRQueueGateway($db);
            parent::__construct($this->srqGateway);
        }
    }

    // Custom Constructors //

    public static function withID($id, $db) {
        $instance = new self(file_id: $id, db: $db);
        $row = $instance->getRecord($id);
        $instance->fill( $row );
        return $instance;
    }

    public static function withRow(?array $row, $db = null )
    {
        if($row)
        {
            $instance = new self(file_id: $row["file_id"], db: $db);
            $instance->fill( $row );
            return $instance;
        }else{
            return null;
        }
    }

    // Implemented functions

    public function fill(bool|array $row)
    {
        if($row)
        {
            $this->notes = $row['notes'];
            $this->file_id = $row['file_id'];
            $this->srq_revai_minutes = $row['srq_revai_minutes'];
            $this->srq_revai_id = $row['srq_revai_id'];
            $this->srq_status = $row['srq_status'];
            $this->srq_id = $row['srq_id'];
            $this->srq_tmp_filename = $row['srq_tmp_filename'];
        }
    }

    public function save(): int
    {
        if($this->srq_id != 0)
        {
            // update
            return $this->updateRecord();

        }else{
            // insert
            return $this->insertRecord();
        }
    }


    public function delete(): int
    {
        return $this->deleteRecord($this->srq_id);
    }


    // Getters and Setters //

    /**
     * @return int
     */
    public function getFileId(): int
    {
        return $this->file_id;
    }

    /**
     * @param int $file_id
     */
    public function setFileId(int $file_id): void
    {
        $this->file_id = $file_id;
    }

    /**
     * @return int
     */
    public function getSrqStatus(): int
    {
        return $this->srq_status;
    }

    /**
     * @param int $srq_status
     */
    public function setSrqStatus(int $srq_status): void
    {
        $this->srq_status = $srq_status;
    }

    /**
     * @return string|null
     */
    public function getSrqRevaiId(): ?string
    {
        return $this->srq_revai_id;
    }

    /**
     * @param string|null $srq_revai_id
     */
    public function setSrqRevaiId(?string $srq_revai_id): void
    {
        $this->srq_revai_id = $srq_revai_id;
    }

    /**
     * @return float
     */
    public function getSrqRevaiMinutes(): float
    {
        return $this->srq_revai_minutes;
    }

    /**
     * @param float $srq_revai_minutes
     */
    public function setSrqRevaiMinutes(float $srq_revai_minutes): void
    {
        $this->srq_revai_minutes = $srq_revai_minutes;
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     */
    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    /**
     * @return int
     */
    public function getSrqId(): int
    {
        return $this->srq_id;
    }

    /**
     * @param int $srq_id
     */
    public function setSrqId(int $srq_id): void
    {
        $this->srq_id = $srq_id;
    }

    /**
     * @return string|null
     */
    public function getSrqTmpFilename(): ?string
    {
        return $this->srq_tmp_filename;
    }

    /**
     * @param string|null $srq_tmp_filename
     */
    public function setSrqTmpFilename(?string $srq_tmp_filename): void
    {
        $this->srq_tmp_filename = $srq_tmp_filename;
    }


}