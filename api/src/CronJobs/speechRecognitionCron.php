<?php

namespace Src\CronJobs;

use DateTime;
use Src\Enums\FILE_STATUS;
use Src\Models\File;
use Src\TableGateways\SRQueueGateway;
use Src\TableGateways\FileGateway;
use Src\Helpers\common;
use Src\Helpers\SRLogger;
use Src\Models\SRQueue;
use Src\Models\SR;
use Src\Enums\SRLOG_ACTIVITY;
use Src\Enums\SRQ_STATUS;
use getID3;
//$rootDir = "C:\\xampp\htdocs\\vscription";
//require "$rootDir\api\bootstrap.php";
//$rootDir = __DIR__;
require __DIR__ . "../../bootstrap.php";


class speechRecognitionCron{

    private $db;
    private SRQueueGateway $srqGateway;
    private FileGateway $fileGateway;
    private SRLogger $srlogger;
    private common $common;
    private getID3 $id3;

    private string $uploadDir;
    private string $revAItmpDir;

    //Vars//
    private int $startTimeStamp;
    private int $requestCount;
    private int $retries;
    const MAX_RETRIES = 3;
    const REVAI_API_REQUEST_LIMIT = 10000;
    const REVAI_API_TIME_LIMIT = 600; // 10 minutes
    const REVAI_POST_URL = "https://api.rev.ai/speechtotext/v1/jobs";
    private string $REVAI_CALLBACK_URL;


    // Runtime vars for each file //
    private string $ddlLink;
    private string $tmpFileName;
    private ?File $fileE;
    private ?SR $srE;
    private ?SRQueue $srqE;

    public function __construct($db)
    {
        // call main process
        $this->db = $db;
        $this->srqGateway = new SRQueueGateway($db);
        $this->fileGateway = new FileGateway($db);
        $this->srlogger = new SRLogger($db);
        $this->common = new common();
        $this->id3 = new getID3();

        $this->uploadDir = __DIR__ . "../../../uploads/";
        $this->revAItmpDir = __DIR__ . "../../../transcribe/sr/".getenv("REVAI_TMP_DIR_NAME")."/";
        $this->REVAI_CALLBACK_URL = getenv("BASE_LINK") . "/api/webhooks/" . getenv('REVAI_CALLBACK_URL_DIR_NAME');
        $this->startTimeStamp = time();
        $this->requestCount = 0;
        $this->retries = 0;

        $this->prepareNextFile();
    }

    function resetRevAiVars()
    {
        $this->startTimeStamp = time();
        $this->requestCount = 0;
    }

    function prepareNextFile()
    {
        $srqRow = $this->srqGateway->getFirst();

        if($srqRow)
        {
            $this->srqE = SRQueue::withRow($srqRow, $this->db);
            $this->fileE = File::withRow($this->fileGateway->findAlt($this->srqE->getFileId()), $this->db);
            $this->srE = SR::withAccID($this->fileE->getAccId(), $this->db);

            $this->tmpFileName = $this->common->generateRandomFileName("REVAI_", $this->common->getFileExtension($this->fileE->getFilename()));;

            if(
                copy(
                    $this->uploadDir . $this->fileE->getFilename(),
                    $this->revAItmpDir . $this->tmpFileName
                    )
            ){
                $this->log($this->srqE->getSrqId(),$this->fileE->getFileId(),SRLOG_ACTIVITY::COPIED_TO_TEMP);

                $this->ddlLink = $this->getDDL($this->tmpFileName);
                $this->srqE->setSrqTmpFilename($this->tmpFileName);
                $this->srqE->save();
                $this->log($this->srqE->getSrqId(),$this->fileE->getFileId(),SRLOG_ACTIVITY::DDL_GENERATED, $this->ddlLink);


                // Duration Workaround

                $realDurationInMins = $this->getFileBillableDuration($this->revAItmpDir . $this->tmpFileName,
                                                                                    $this->fileE->getFilename());
                $diff = abs($this->srqE->getSrqRevaiMinutes() - $realDurationInMins);

                // Green Case
                if($realDurationInMins < $this->srqE->getSrqRevaiMinutes())
                {
                    // refund difference to user account
                    $this->srE->setSrMinutesRemaining($this->srE->getSrMinutesRemaining() + $diff);
                    $this->srE->save();

                    // set correct minutes to srq_tbl
                    $this->srqE->setSrqRevaiMinutes($realDurationInMins);
                    $this->srqE->save();

                    $this->log($this->srqE->getSrqId(),$this->fileE->getFileId(),SRLOG_ACTIVITY::REFUND_DIFF_MINUTES, "refunded: " . $diff);
                    // proceed
                }

                // Red Case
                else if($realDurationInMins > $this->srqE->getSrqRevaiMinutes())
                {
                    // 2 cases

                    // case purple
                    if( ($this->srE->getSrMinutesRemaining() - $diff) >= 0)
                    {
                        $this->log($this->srqE->getSrqId(),$this->fileE->getFileId(),SRLOG_ACTIVITY::DEDUCT_DIFF_MINUTES, "deducted: " . $diff);

                        $this->srE->setSrMinutesRemaining($this->srE->getSrMinutesRemaining() - $diff);
                        $this->srE->save();

                        $this->srqE->setSrqRevaiMinutes($realDurationInMins);
                        $this->srqE->save();

                        // proceed
                    }

                    // case red
                    else{
                        // refund user with old miscalculated minutes
                        $old_duration = $this->srqE->getSrqRevaiMinutes();
                        $this->srE->setSrMinutesRemaining(
                            $this->srE->getSrMinutesRemaining() + $old_duration
                        );
                        $this->srE->save();


                        // set insuff balance status & update with real duration
                        $this->srqE->setSrqRevaiMinutes($realDurationInMins);
                        $this->srqE->setSrqStatus(SRQ_STATUS::INSUFFICIENT_BALANCE);
                        $this->srqE->save();

                        // set file status to awaiting transcription
                        $this->fileE->setFileStatus(FILE_STATUS::AWAITING_TRANSCRIPTION);
                        $this->fileE->saveNewStatus();

                        $this->log($this->srqE->getSrqId(),$this->fileE->getFileId(),SRLOG_ACTIVITY::INSUFFICIENT_BALANCE, "Skipping file");
                        $this->log($this->srqE->getSrqId(),$this->fileE->getFileId(),SRLOG_ACTIVITY::REFUND_DIFF_MINUTES, "refunded: " . $old_duration);
                        // SKIP
                        $this->prepareNextFile();
                        return; // prevent further execution for revai request

                    }
                }

               // to rev ai
                $this->revAi();


            }else{
                $this->log($this->srqE->getSrqId(),
                $this->fileE->getFileId(),
                SRLOG_ACTIVITY::COULD_COPY_TO_TEMP);

                $this->srqE->setSrqStatus(SRQ_STATUS::MANUAL_REVISION_REQ);
                $this->srqE->setNotes("Couldnt copy file to tmp directory");
                $this->srqE->save();

                $this->prepareNextFile();
            }

        }else{
            // No Files in Queue
            sleep(10);
        }
    }

    function thresholdWait()
    {
        if ((time() - $this->startTimeStamp) < self::REVAI_API_TIME_LIMIT)
        {
            sleep((self::REVAI_API_TIME_LIMIT - (time() - $this->startTimeStamp)) + 10);
        }

        $this->resetRevAiVars();
        $this->revAi();
    }

    function revAi()
    {
        // send to rev.ai

        // Curl request to rev.ai
        $this->requestCount++;

        if($this->requestCount > self::REVAI_API_REQUEST_LIMIT)
        {
            $this->thresholdWait();
            return;
        }

        $response = $this->curlPostRevAi($this->ddlLink);

        if($response["status"] == "in_progress")
        {
            // success
            $this->retries = 0;

            $this->log($this->srqE->getSrqId(),$this->fileE->getFileId(),
                SRLOG_ACTIVITY::PROCESSING, "rev.ai ID: " . $response["id"]);

            $this->srqE->setSrqRevaiId($response["id"]);
            $this->srqE->setSrqStatus(SRQ_STATUS::PROCESSING);
            $this->srqE->save();

            $this->fileE->setFileStatus(FILE_STATUS::RECOGNITION_IN_PROGRESS);
            $this->fileE->saveNewStatus();

        }
        else{
            // curl post failed
            $this->log($this->srqE->getSrqId(),$this->fileE->getFileId(),
                SRLOG_ACTIVITY::REVAI_SEND_ERROR, $response['title']);

            if($this->retries < self::MAX_RETRIES)
            {
                // retry
                $this->retries++;
                $this->revAi();
                return;
            }else{
                // set as failed
                $this->srqE->setSrqStatus(SRQ_STATUS::REVAI_FAILED_TO_RESPOND_WITH_SUCCESS);
                $this->srqE->setNotes($response['title']);
                $this->srqE->save();

                $this->fileE->setFileStatus(FILE_STATUS::AWAITING_TRANSCRIPTION);
                $this->fileE->saveNewStatus();

                // add minutes back to user account
                $this->log($this->srqE->getSrqId(),$this->fileE->getFileId(),
                    SRLOG_ACTIVITY::ADDED_MINUTES_TO_ACC, $this->srqE->getSrqRevaiMinutes());
                $this->srE->setSrMinutesRemaining($this->srE->getSrMinutesRemaining() + $this->srqE->getSrqRevaiMinutes());
                $this->srE->save();

            }

        }

        // Next file
        $this->prepareNextFile();
    }

    function curlPostRevAi($ddlLink): array|null
    {

        $data = array("media_url" => $ddlLink,
//            "metadata" => "SAMPLE",
            "callback_url" => $this->REVAI_CALLBACK_URL,
            "skip_diarization"=> true,
            "skip_punctuation" => false,
            "language" => "en"
            );

        $curl = curl_init();

        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => self::REVAI_POST_URL,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_CAINFO => "serverpem.pem",
            // getenv("BASE_LINK").'/api/v1/conversions'
            // CURLOPT_URL => 'https://pro.vtex/api/v1/conversions',
            // CURLOPT_USERAGENT => 'Files API',
            // CURLOPT_COOKIESESSION => false
            // CURLOPT_COOKIE => $strCookie,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . getenv('REVAI_ACCESS_TOKEN')
            )
        ]);

//        // todo disable for production the 2 lines below
//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        return json_decode($resp, true);
    }

    /**
     * calculates file duration in minutes rounded to nearest 15 seconds
     * @param $tmpFilePath
     * @param $originalName
     * @return float rounded file duration (min)
     */
    function getFileBillableDuration($tmpFilePath, $originalName): float
    {
        $fileInfo = $this->id3->analyze($tmpFilePath, filesize($tmpFilePath), $originalName);
        $file_duration = (int)ceil(@$fileInfo['playtime_seconds']);
        return $this->roundUpToAnyIncludeCurrent($file_duration);
    }

    function roundUpToAny($n,$x=15) {
        return round(($n+$x/2)/$x)*$x;
    }

    /**
     * @param $n float file duration in seconds
     * @param int $x round to nearest $x seconds
     * @return float rounded duration in minutes
     */
    function roundUpToAnyIncludeCurrent($n, $x=15): float
    {
        $seconds = (round($n)%$x === 0) ? round($n) : round(($n+$x/2)/$x)*$x;
        return $seconds/60;
    }

    // Helping Functions //

    /**
     * Generates DDL for rev.ai files
     * @param $filename
     * @return String
     */
    function getDDL($filename) : String
    {
        return getenv("BASE_LINK") . "/sr/" . getenv('REVAI_TMP_DIR_NAME') . "/" . $filename;
    }

    function log($srq_id, $file_id, $activity, $opt_msg = null)
    {
        $this->srlogger->log($srq_id,$file_id,$activity, $opt_msg);
        $this->output($srq_id . " | " . $file_id. " | " .$activity. " | " . $opt_msg);
    }

    function output($msg)
    {
        $mainLog = __DIR__ . "../../../revai.log";
        $oldLog = __DIR__ . "../../../revai.old.log";

        $date = new DateTime();
        $date = $date->format("y:m:d h:i:s");
        $str = "[$date]: $msg";
        echo $str;

        if(file_exists($mainLog))
        {
            if(filesize($mainLog) > 5000000) // 5MB
            {
                if(file_exists($oldLog))
                {
                    unlink($oldLog);
                }
                rename($mainLog, $oldLog);
            }
        }

        file_put_contents($mainLog, $str.PHP_EOL , FILE_APPEND | LOCK_EX);
    }

}

new speechRecognitionCron($dbConnection);

