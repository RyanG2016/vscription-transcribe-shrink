<?php
namespace Src\Controller;

use Src\Enums\FILE_STATUS;
use Src\Enums\HTTP_CONTENT_TYPE;
use Src\Enums\HTTP_RESPONSE;
use Src\Enums\SRLOG_ACTIVITY;
use Src\Enums\SRQ_STATUS;
use Src\Helpers\common;
use Src\Models\SR;
use Src\Models\SRQueue;
use Src\TableGateways\SRGateway;
use Src\TableGateways\SRQueueGateway;
use Src\TableGateways\FileGateway;
use Src\Models\File;
use Src\Traits\httpResponse;
use Src\Helpers\SRLogger;

class SRQueueController {

    private $srQueueGateway;
    private $filesGateway;
    private $file;
    private $srLogger;
    private $common;
    use httpResponse;

    public function __construct(private $db, private $requestMethod, private $page = null)
    {
        $this->srQueueGateway = new SRQueueGateway($db);
        $this->filesGateway = new FileGateway($db);
        $this->srLogger = new SRLogger($db);
        $this->common = new common();
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
//            case 'GET':

//                break;
            case 'POST':
                if ($this->page == "incoming") {
                    $response = $this->processRevaiNotification();
                } else {
                    $response = $this->$this->respond(HTTP_RESPONSE::HTTP_NOT_FOUND);
                }
                break;
//            case 'PUT':
//                $response = $this->updateCitiesFromRequest($this->citiesId);
//                break;
//            case 'DELETE':
//                $response = $this->deleteCities($this->citiesId);
//                break;
            default:
                $response = $this->$this->respond(HTTP_RESPONSE::HTTP_NOT_FOUND);
                break;
        }
        header($response['status_code_header']);
        header("Content-type:". $response['content_type']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function processRevaiNotification()
    {
        $postBody = $this->readPost();
        $revaiResponse = $postBody["job"];

        if($revaiResponse)
        {
            $row = $this->srQueueGateway->findByRevAiID($revaiResponse["id"]);
            if($row)
            {
                $row = $this->srQueueGateway->findByRevAiID($revaiResponse["id"]);
                $srq = SRQueue::withRow($row, $this->db);

                // delete temp DDL file
                if($srq->getSrqTmpFilename())
                {
                    $tmpDDLFilePath = __DIR__ . "/../../../transcribe/sr/".getenv("REVAI_TMP_DIR_NAME")."/"
                        . $srq->getSrqTmpFilename();

                    if(file_exists($tmpDDLFilePath))
                    {
                        unlink($tmpDDLFilePath);
                        $this->srLogger->log($srq->getSrqId(),null, SRLOG_ACTIVITY::DELETE_TMP_DDL_FILE);
                    }

                }


                if($revaiResponse["status"] == "transcribed")
                {
                    $srq->setSrqStatus(SRQ_STATUS::INTERNAL_PROCESSING);
                    $srq->setSrqInternalId($this->srQueueGateway->getNextInternalID());
                    $srq->save();

                    $this->srLogger->log($srq->getSrqId(),null, SRLOG_ACTIVITY::INTERNAL_QUEUED,
                        "internal queue #: " . $srq->getSrqInternalId());

                        // rest is handled by internal queue process

                }
                else if($revaiResponse["status"] == "failed"){

                    $minutes = $srq->getSrqRevaiMinutes();

                    // get file
                    $file = File::withID($srq->getFileId(), $this->db);
                    $sr = SR::withAccID($file->getAccId(), $this->db);

                    // refund minutes back to user
                    if($srq->getRefunded() != 0)
                    {
                        $sr->addToMinutesRemaining($minutes);
                        $sr->save();
                    }

                    // set SRQ status to failed
                    $srq->setSrqStatus(SRQ_STATUS::FAILED);
                    $srq->setRefunded(1);
                    $srq->setNotes($revaiResponse["failure"] .": " . $revaiResponse["failure_detail"]);
                    $srq->save();

                    // set file_status to awaiting transcription
                    $file->setFileStatus(FILE_STATUS::AWAITING_TRANSCRIPTION);
                    $file->saveNewStatus();

                    // log failed
                    $this->srLogger->log($srq->getSrqId(),null, SRLOG_ACTIVITY::FAILED,
                        "rev.ai failed to process file minutes refunded to user | " .
                        $revaiResponse["failure"] .": " . $revaiResponse["failure_detail"]);

                }

            }else{
                // No entry found for ID
                $this->srLogger->log(null,null, SRLOG_ACTIVITY::REVAI_ID_NOT_FOUND,
                    $revaiResponse["id"]);
            }
        }
        else{
            $this->srLogger->log(null,null, SRLOG_ACTIVITY::UNKNOWN_WEBHOOK_BODY,
                "unknown request body received at rev.ai webhook | " .
                $postBody);
        }

        return $this->respond(HTTP_RESPONSE::HTTP_OK);
    }

    private function readPost():array
    {
        return json_decode(file_get_contents('php://input'), true);
    }


    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => true,
            'msg' => 'Invalid input'
        ]);
        return $response;
    }


    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
