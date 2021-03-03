<?php


namespace Src\Models;

use Src\TableGateways\FileGateway;
use Src\Traits\modelToString;

/**
 * Class SRQueue
 * model for sr_queue table
 * @package Src\Models
 */
class File extends BaseModel implements BaseModelInterface
{

    use modelToString;
    private FileGateway $fileGateway;

    public function __construct(
        private $db,
        private int $file_id,
        private string $job_id,
        private int $acc_id,
        private ?string $job_upload_date = null,
        private ?string $file_transcribed_date = null,
        private int $elapsed_time = 0,
        private ?string $typist_comments = null,
        private int $isBillable = 1,
        private int $billed = 0,
        private int $typ_billed = 0,
        private ?string $user_field_1 = null,
        private ?string $user_field_2 = null,
        private ?string $user_field_3 = null,
        private int $deleted = 0,
        private ?string $job_transcribed_by = null,
        private int $times_text_downloaded_date = 0,
        private ?string $text_downloaded_date = null,
        private ?string $file_date_dict = null,
        private ?float $audio_length = null,
        private ?string $job_uploaded_by = null,
        private int $file_status = 0,
        private ?int $last_audio_position = 0,
        private int $file_speaker_type = 0,
        private ?int $file_type = null,
        private ?string $org_ext = null,
        private ?string $tmp_name = null,
        private ?string $orig_filename = null,
        private ?string $job_document_html = null,
        private int $has_caption = 0,
        private ?string $filename = null,
        private ?String $file_tag = null,
        private ?string $file_author = null,
        private ?string $file_work_type = null,
        private ?string $file_comment = null,
        private ?string $job_document_rtf = null,
        private ?string $captions = null
    )
    {
        if(is_null($this->job_upload_date))
        {
            $this->job_upload_date = date("Y-m-d H:i:s");
        }

        $this->fileGateway = new FileGateway($db);
        parent::__construct($this->fileGateway);

    }

    /**
     * @return string|null
     */
    public function getCaptions(): ?string
    {
        return $this->captions;
    }

    /**
     * @param string|null $captions
     */
    public function setCaptions(?string $captions): void
    {
        $this->captions = $captions;
    }


    // Custom Constructors //

    public static function withID($id, $db) {
        $instance = new self(db: $db, file_id: $id, job_id: 0, acc_id: 0);
        $row = $instance->getRecordAlt($id);
        $instance->fill( $row );
        return $instance;
    }

    public static function withRow(?array $row, $db = null ) {
        if($row)
        {
            $instance = new self(db: $db, file_id: $row["file_id"], job_id: $row["job_id"], acc_id: $row["acc_id"]);
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
            if(isset($row['file_id'])) $this->file_id = $row['file_id'];
            if(isset($row['job_id'])) $this->job_id = $row['job_id'];
            if(isset($row['acc_id'])) $this->acc_id = $row['acc_id'];
            if(isset($row['file_type'])) $this->file_type = $row['file_type'];
            if(isset($row['org_ext'])) $this->org_ext = $row['org_ext'];
            if(isset($row['filename'])) $this->filename = $row['filename'];
            if(isset($row['tmp_name'])) $this->tmp_name = $row['tmp_name'];
            if(isset($row['orig_filename'])) $this->orig_filename = $row['orig_filename'];
            if(isset($row['job_document_html'])) $this->job_document_html = $row['job_document_html'];
            if(isset($row['job_document_rtf'])) $this->job_document_rtf = $row['job_document_rtf'];
            if(isset($row['has_caption'])) $this->has_caption = $row['has_caption'];
            if(isset($row['captions'])) $this->captions = $row['captions'];
            if(isset($row['file_tag'])) $this->file_tag = $row['file_tag'];
            if(isset($row['file_author'])) $this->file_author = $row['file_author'];
            if(isset($row['file_work_type'])) $this->file_work_type = $row['file_work_type'];
            if(isset($row['file_comment'])) $this->file_comment = $row['file_comment'];
            if(isset($row['file_speaker_type'])) $this->file_speaker_type = $row['file_speaker_type'];
            if(isset($row['file_date_dict'])) $this->file_date_dict = $row['file_date_dict'];
            if(isset($row['file_status'])) $this->file_status = $row['file_status'];
            if(isset($row['audio_length'])) $this->audio_length = $row['audio_length'];
            if(isset($row['last_audio_position'])) $this->last_audio_position = $row['last_audio_position'];
            if(isset($row['job_upload_date'])) $this->job_upload_date = $row['job_upload_date'];
            if(isset($row['job_uploaded_by'])) $this->job_uploaded_by = $row['job_uploaded_by'];
            if(isset($row['text_downloaded_date'])) $this->text_downloaded_date = $row['text_downloaded_date'];
            if(isset($row['times_text_downloaded_date'])) $this->times_text_downloaded_date = $row['times_text_downloaded_date'];
            if(isset($row['job_transcribed_by'])) $this->job_transcribed_by = $row['job_transcribed_by'];
            if(isset($row['file_transcribed_date'])) $this->file_transcribed_date = $row['file_transcribed_date'];
            if(isset($row['elapsed_time'])) $this->elapsed_time = $row['elapsed_time'];
            if(isset($row['typist_comments'])) $this->typist_comments = $row['typist_comments'];
            if(isset($row['isBillable'])) $this->isBillable = $row['isBillable'];
            if(isset($row['billed'])) $this->billed = $row['billed'];
            if(isset($row['typ_billed'])) $this->typ_billed = $row['typ_billed'];
            if(isset($row['user_field_1'])) $this->user_field_1 = $row['user_field_1'];
            if(isset($row['user_field_2'])) $this->user_field_2 = $row['user_field_2'];
            if(isset($row['user_field_3'])) $this->user_field_3 = $row['user_field_3'];
            if(isset($row['deleted'])) $this->deleted = $row['deleted'];
        }
    }

    public function saveNewStatus():bool
    {
        return $this->fileGateway->directUpdateFileStatus($this->file_id, $this->file_status, $this->filename);
    }

    public function saveHTML($optional_has_caption = null, $captions = null):int
    {
        return $this->fileGateway->updateFileHTML($this, $optional_has_caption, $captions);
    }

    public function save(): int
    {
        if($this->file_id != 0)
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
        return $this->deleteRecord($this->file_id);
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
     * @return string
     */
    public function getJobId(): string
    {
        return $this->job_id;
    }

    /**
     * @param string $job_id
     */
    public function setJobId(string $job_id): void
    {
        $this->job_id = $job_id;
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
     * @return string|null
     */
    public function getJobUploadDate(): ?string
    {
        return $this->job_upload_date;
    }

    /**
     * @param string|null $job_upload_date
     */
    public function setJobUploadDate(?string $job_upload_date): void
    {
        $this->job_upload_date = $job_upload_date;
    }

    /**
     * @return string|null
     */
    public function getFileTranscribedDate(): ?string
    {
        return $this->file_transcribed_date;
    }

    /**
     * @param string|null $file_transcribed_date
     */
    public function setFileTranscribedDate(?string $file_transcribed_date): void
    {
        $this->file_transcribed_date = $file_transcribed_date;
    }

    /**
     * @return int
     */
    public function getElapsedTime(): int
    {
        return $this->elapsed_time;
    }

    /**
     * @param int $elapsed_time
     */
    public function setElapsedTime(int $elapsed_time): void
    {
        $this->elapsed_time = $elapsed_time;
    }

    /**
     * @return string|null
     */
    public function getTypistComments(): ?string
    {
        return $this->typist_comments;
    }

    /**
     * @param string|null $typist_comments
     */
    public function setTypistComments(?string $typist_comments): void
    {
        $this->typist_comments = $typist_comments;
    }

    /**
     * @return int
     */
    public function getIsBillable(): int
    {
        return $this->isBillable;
    }

    /**
     * @param int $isBillable
     */
    public function setIsBillable(int $isBillable): void
    {
        $this->isBillable = $isBillable;
    }

    /**
     * @return int
     */
    public function getBilled(): int
    {
        return $this->billed;
    }

    /**
     * @param int $billed
     */
    public function setBilled(int $billed): void
    {
        $this->billed = $billed;
    }

    /**
     * @return int
     */
    public function getTypBilled(): int
    {
        return $this->typ_billed;
    }

    /**
     * @param int $typ_billed
     */
    public function setTypBilled(int $typ_billed): void
    {
        $this->typ_billed = $typ_billed;
    }

    /**
     * @return string|null
     */
    public function getUserField1(): ?string
    {
        return $this->user_field_1;
    }

    /**
     * @param string|null $user_field_1
     */
    public function setUserField1(?string $user_field_1): void
    {
        $this->user_field_1 = $user_field_1;
    }

    /**
     * @return string|null
     */
    public function getUserField2(): ?string
    {
        return $this->user_field_2;
    }

    /**
     * @param string|null $user_field_2
     */
    public function setUserField2(?string $user_field_2): void
    {
        $this->user_field_2 = $user_field_2;
    }

    /**
     * @return string|null
     */
    public function getUserField3(): ?string
    {
        return $this->user_field_3;
    }

    /**
     * @param string|null $user_field_3
     */
    public function setUserField3(?string $user_field_3): void
    {
        $this->user_field_3 = $user_field_3;
    }

    /**
     * @return int
     */
    public function getDeleted(): int
    {
        return $this->deleted;
    }

    /**
     * @param int $deleted
     */
    public function setDeleted(int $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return string|null
     */
    public function getJobTranscribedBy(): ?string
    {
        return $this->job_transcribed_by;
    }

    /**
     * @param string|null $job_transcribed_by
     */
    public function setJobTranscribedBy(?string $job_transcribed_by): void
    {
        $this->job_transcribed_by = $job_transcribed_by;
    }

    /**
     * @return int
     */
    public function getTimesTextDownloadedDate(): int
    {
        return $this->times_text_downloaded_date;
    }

    /**
     * @param int $times_text_downloaded_date
     */
    public function setTimesTextDownloadedDate(int $times_text_downloaded_date): void
    {
        $this->times_text_downloaded_date = $times_text_downloaded_date;
    }

    /**
     * @return string|null
     */
    public function getTextDownloadedDate(): ?string
    {
        return $this->text_downloaded_date;
    }

    /**
     * @param string|null $text_downloaded_date
     */
    public function setTextDownloadedDate(?string $text_downloaded_date): void
    {
        $this->text_downloaded_date = $text_downloaded_date;
    }

    /**
     * @return string|null
     */
    public function getFileDateDict(): ?string
    {
        return $this->file_date_dict;
    }

    /**
     * @param string|null $file_date_dict
     */
    public function setFileDateDict(?string $file_date_dict): void
    {
        $this->file_date_dict = $file_date_dict;
    }

    /**
     * @return float|null
     */
    public function getAudioLength(): ?int
    {
        return $this->audio_length;
    }

    /**
     * @param float|null $audio_length
     */
    public function setAudioLength(?float $audio_length): void
    {
        $this->audio_length = $audio_length;
    }

    /**
     * @return string|null
     */
    public function getJobUploadedBy(): ?string
    {
        return $this->job_uploaded_by;
    }

    /**
     * @param string|null $job_uploaded_by
     */
    public function setJobUploadedBy(?string $job_uploaded_by): void
    {
        $this->job_uploaded_by = $job_uploaded_by;
    }

    /**
     * @return int
     */
    public function getFileStatus(): int
    {
        return $this->file_status;
    }

    /**
     * @param int $file_status
     */
    public function setFileStatus(int $file_status): void
    {
        $this->file_status = $file_status;
    }

    /**
     * @return int|null
     */
    public function getLastAudioPosition(): ?int
    {
        return $this->last_audio_position;
    }

    /**
     * @param int|null $last_audio_position
     */
    public function setLastAudioPosition(?int $last_audio_position): void
    {
        $this->last_audio_position = $last_audio_position;
    }

    /**
     * @return int
     */
    public function getFileSpeakerType(): int
    {
        return $this->file_speaker_type;
    }

    /**
     * @param int $file_speaker_type
     */
    public function setFileSpeakerType(int $file_speaker_type): void
    {
        $this->file_speaker_type = $file_speaker_type;
    }

    /**
     * @return int|null
     */
    public function getFileType(): ?int
    {
        return $this->file_type;
    }

    /**
     * @param int|null $file_type
     */
    public function setFileType(?int $file_type): void
    {
        $this->file_type = $file_type;
    }

    /**
     * @return string|null
     */
    public function getOrgExt(): ?string
    {
        return $this->org_ext;
    }

    /**
     * @param string|null $org_ext
     */
    public function setOrgExt(?string $org_ext): void
    {
        $this->org_ext = $org_ext;
    }

    /**
     * @return string|null
     */
    public function getTmpName(): ?string
    {
        return $this->tmp_name;
    }

    /**
     * @param string|null $tmp_name
     */
    public function setTmpName(?string $tmp_name): void
    {
        $this->tmp_name = $tmp_name;
    }

    /**
     * @return string|null
     */
    public function getOrigFilename(): ?string
    {
        return $this->orig_filename;
    }

    /**
     * @param string|null $orig_filename
     */
    public function setOrigFilename(?string $orig_filename): void
    {
        $this->orig_filename = $orig_filename;
    }

    /**
     * @return string|null
     */
    public function getJobDocumentHtml(): ?string
    {
        return $this->job_document_html;
    }

    /**
     * @param string|null $job_document_html
     */
    public function setJobDocumentHtml(?string $job_document_html): void
    {
        $this->job_document_html = $job_document_html;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     */
    public function setFilename(?string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return String|null
     */
    public function getFileTag(): ?string
    {
        return $this->file_tag;
    }

    /**
     * @param String|null $file_tag
     */
    public function setFileTag(?string $file_tag): void
    {
        $this->file_tag = $file_tag;
    }

    /**
     * @return string|null
     */
    public function getFileAuthor(): ?string
    {
        return $this->file_author;
    }

    /**
     * @param string|null $file_author
     */
    public function setFileAuthor(?string $file_author): void
    {
        $this->file_author = $file_author;
    }

    /**
     * @return string|null
     */
    public function getFileWorkType(): ?string
    {
        return $this->file_work_type;
    }

    /**
     * @param string|null $file_work_type
     */
    public function setFileWorkType(?string $file_work_type): void
    {
        $this->file_work_type = $file_work_type;
    }

    /**
     * @return string|null
     */
    public function getFileComment(): ?string
    {
        return $this->file_comment;
    }

    /**
     * @param string|null $file_comment
     */
    public function setFileComment(?string $file_comment): void
    {
        $this->file_comment = $file_comment;
    }

    /**
     * @return string|null
     */
    public function getJobDocumentRtf(): ?string
    {
        return $this->job_document_rtf;
    }

    /**
     * @param string|null $job_document_rtf
     */
    public function setJobDocumentRtf(?string $job_document_rtf): void
    {
        $this->job_document_rtf = $job_document_rtf;
    }

    /**
     * @return int
     */
    public function getHasCaption(): int
    {
        return $this->has_caption;
    }

    /**
     * @param int $has_caption
     */
    public function setHasCaption(int $has_caption): void
    {
        $this->has_caption = $has_caption;
    }


}