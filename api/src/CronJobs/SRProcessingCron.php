<?php

namespace Src\CronJobs;

use DateTime;
use Done\Subtitles\Subtitles;
use Src\Enums\FILE_STATUS;
use Src\Enums\HTTP_CONTENT_TYPE;
use Src\Models\File;
use Src\TableGateways\SRQueueGateway;
use Src\TableGateways\FileGateway;
use Src\Helpers\common;
use Src\Helpers\SRLogger;
use Src\Models\SRQueue;
use Src\Models\SR;
use Src\Enums\SRLOG_ACTIVITY;
use Src\Enums\SRQ_STATUS;

require __DIR__ . "/../../bootstrap.php";

/**
 * Class SRProcessingCron <br>
 * Processes responses returned from Rev.ai
 * @package Src\CronJobs
 */
class SRProcessingCron{

    private $db;
    private SRQueueGateway $srqGateway;
    private FileGateway $fileGateway;
    private SRLogger $srlogger;
    private common $common;


    private string $uploadDir;
    private string $revAItmpDir;

    // const //
    const WAIT_THEN_CURL_CAPTIONS = 1;
    const WAIT_THEN_CURL_TRANSCRIPT = 2;

    //Vars//
    private int $startTimeStamp;
    private int $requestCount;
    private int $retries;
    const REVAI_API_REQUEST_LIMIT = 10000;
    const REVAI_API_TIME_LIMIT = 600; // 10 minutes
    const REVAI_TRANSCRIPT_URL = "https://api.rev.ai/speechtotext/v1/jobs/{id}/transcript";
    const REVAI_CAPTIONS_URL = "https://api.rev.ai/speechtotext/v1/jobs/{id}/captions";


    // Runtime vars for each file //

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

        $this->uploadDir = __DIR__ . "/../../../uploads/";
        $this->revAItmpDir = __DIR__ . "/../../../transcribe/sr/".getenv("REVAI_TMP_DIR_NAME")."/";
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
        $srqRow = $this->srqGateway->getNextQFIProcessing();

        if($srqRow)
        {
            $this->srqE = SRQueue::withRow($srqRow, $this->db);
            $this->fileE = File::withRow($this->fileGateway->findAltModel($this->srqE->getFileId()), $this->db);
            $this->srE = SR::withAccID($this->fileE->getAccId(), $this->db);

            $this->requestCount++;

            if($this->requestCount > self::REVAI_API_REQUEST_LIMIT)
            {
                $this->thresholdWait(self::WAIT_THEN_CURL_CAPTIONS);
                return;
            }

            $this->curlGetCaptions();

        }else{
            // No Files in Queue
            echo "[" . (new DateTime())->format("y:m:d h:i:s")."] " . "Nothing to receive from rev.ai.. waiting\n";
            sleep(10);
            $this->prepareNextFile();
        }
    }


    function getCaptionsUrl($job_id): string
    {
        return str_replace("{id}", $job_id, self::REVAI_CAPTIONS_URL);
    }
    function getTranscriptUrl($job_id): string
    {
        return str_replace("{id}", $job_id, self::REVAI_TRANSCRIPT_URL);
    }

    function curlGetCaptions(): void
    {

        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->getCaptionsUrl($this->srqE->getSrqRevaiId()),
            CURLOPT_HTTPGET => true, // GET request
            CURLOPT_HTTPHEADER => array(
                'Accept: '. HTTP_CONTENT_TYPE::TEXT_VTT,
                'Authorization: Bearer ' . getenv('REVAI_ACCESS_TOKEN')
            )
        ]);


        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        if($resp)
        {

            // convert to vtt file
            $subtitles = Subtitles::load($resp, 'vtt');

            // save the vtt file
            if(file_put_contents($this->uploadDir. pathinfo($this->fileE->getFilename(), PATHINFO_FILENAME) . ".vtt",
                $resp))
            {
                $this->log($this->srqE->getSrqId(),$this->fileE->getFileId(), SRLOG_ACTIVITY::VTT_FILE_SAVED_IN_UP_DIR,
                    "filename: " . pathinfo($this->fileE->getFilename(), PATHINFO_FILENAME)
                    . " | srq_id: " . $this->srqE->getSrqId());
            }


            // save html data
            $html = $this->processVttToHtml($subtitles->getInternalFormat()); // process to html with timestamps
            $this->fileE->setJobDocumentHtml($html);
            $this->fileE->saveHTML(1, json_encode($subtitles->getInternalFormat()));
            $this->log($this->srqE->getSrqId(),$this->fileE->getFileId(), SRLOG_ACTIVITY::VTT_PROCESSED_TO_HTML,
                "file_id: " . $this->fileE->getFileId()
                . " | srq_id: " . $this->srqE->getSrqId());

            $this->complete();

        }else{
            $this->log($this->srqE->getSrqId(),$this->fileE->getFileId(), SRLOG_ACTIVITY::COULDNT_FETCH_CAPTIONS,
                "revai_id: " . $this->srqE->getSrqRevaiId(). " | srq_id: " . $this->srqE->getSrqId());


            $this->requestCount++;
            if($this->requestCount > self::REVAI_API_REQUEST_LIMIT)
            {
                $this->thresholdWait(self::WAIT_THEN_CURL_TRANSCRIPT);
                return;
            }
            $this->curlGetTrans();

        }
    }

    function complete()
    {
        $this->srqE->setSrqStatus(SRQ_STATUS::COMPLETE);
        $this->srqE->save();

        $this->fileE->setFileStatus(FILE_STATUS::AWAITING_CORRECTION);
        $this->fileE->saveNewStatus();

        $this->log($this->srqE->getSrqId(),$this->fileE->getFileId(), SRLOG_ACTIVITY::COMPLETE,
            "file_id: " . $this->fileE->getFileId());

        $this->prepareNextFile();
    }

    function curlGetTrans(): void
    {

        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->getTranscriptUrl($this->srqE->getSrqRevaiId()),
            CURLOPT_HTTPGET => true, // GET request

            CURLOPT_HTTPHEADER => array(
                'Accept: '. HTTP_CONTENT_TYPE::TEXT_PLAIN,
                'Authorization: Bearer ' . getenv('REVAI_ACCESS_TOKEN')
            )
        ]);


        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);


        if ($resp) {
            $html = $this->processTxtToHTML($resp); // process to html with timestamps

            $this->fileE->setJobDocumentHtml($html);
            $this->fileE->saveHTML(1);
            $this->log($this->srqE->getSrqId(), $this->fileE->getFileId(), SRLOG_ACTIVITY::TEXT_PROCESSED_TO_HTML,
                "file_id: " . $this->fileE->getFileId()
                . " | srq_id: " . $this->srqE->getSrqId());

            $this->complete();
        }
        else {
            $this->log($this->srqE->getSrqId(), $this->fileE->getFileId(), SRLOG_ACTIVITY::COULDNT_FETCH_TRANSCRIPT_NOR_CAPTIONS,
                "revai_id: " . $this->srqE->getSrqRevaiId() . " | srq_id: " . $this->srqE->getSrqId());

            // todo IMP!
            $this->srqE->setSrqStatus(SRQ_STATUS::MANUAL_REVISION_REQ);
            $this->srqE->save();

            $this->prepareNextFile();
        }

    }

    function processTxtToHTML($plainTxt): string
    {
        $result = "";
        $lastSpeaker = "";

        $lines = explode("\n", $plainTxt);
        foreach ($lines as $line)
        {
            if(!empty($line))
            {
                // process line into 3 chunks > $transcript
                // [0] -> Speaker
                // [1] -> Start Time
                // [2] -> Text paragraph
                $transcript = explode("    ", $line);

                // echo speaker if speaker is changed
                if($lastSpeaker != $transcript[0])
                {
                    $result .= ">> " . $transcript[0]. ": ";
                    $lastSpeaker = $transcript[0];
                }

                // text

                $result .= wordwrap($transcript[2],70,"<br>") . "<br><br>";
            }
        }

        return $result;
    }

    function processVttToHtml(array $subtitleInternalArray):string
    {
        //to select a certail div
        //    tinymce.activeEditor.selection.select(tinymce.activeEditor.dom.select('strong')[0]);
        //    tinymce.activeEditor.selection.select(tinymce.activeEditor.dom.select('#findme')[0])

        $html = "";

        foreach ($subtitleInternalArray as $block) {
//        echo $block['start'];
//        echo $block['end'];

            $currentLine = "";
            $id = $this->generateDivIDFromVTTStart($block['start']);
            $stSpan =  "<div id='$id'>";

            foreach ($block['lines'] as $line) {
                $currentLine .= $line . " ";
            }
            $html .= $stSpan . wordwrap($currentLine, 70, "<br>") . "</div>";

        }

//    echo $html;
        return $html;
    }
/*
    function processVttToHtmlAble(array $subtitleInternalArray):string
    {
        //to select a certain div in tinymce
        //    tinymce.activeEditor.selection.select(tinymce.activeEditor.dom.select('strong')[0]);
        //    tinymce.activeEditor.selection.select(tinymce.activeEditor.dom.select('#findme')[0])
        //editor.selection.getNode().scrollIntoView(false)
        //
        //tinymce.activeEditor.selection.getNode().scrollIntoView(true)

        $html = "";
//        $html .= '<div id="transcript" class="able-transcript-area">';
//        $html .= '<div class="able-window-toolbar"></div>';
//        $html .= '<div class="able-transcript">';

        foreach ($subtitleInternalArray as $block) {
//        echo $block['start'];
//        echo $block['end'];
            $currentLine = "";
            $id = $this->generateDivIDFromVTTStart($block['start']);
            $stSpan =  "<span id='$id' class='able-transcript-seekpoint able-transcript-caption' data-start='".$block['start']."' data-end='".$block['end']."' >";

            foreach ($block['lines'] as $line) {
                $currentLine .= $line . " ";
            }
            $html .= $stSpan . wordwrap($currentLine, 70, "<br>") . "<br></span>";

        }

        $html .= "</div>";
        $html .= "</div>";
        return $html;
    }*/

    function generateDivIDFromVTTStart(float $start): string
    {
        //    $str = str_replace(".","T",$str);

        return "ST" . number_format($start,2,'T','');
    }

    function thresholdWait($reason_code)
    {
        if ((time() - $this->startTimeStamp) < self::REVAI_API_TIME_LIMIT)
        {
            sleep((self::REVAI_API_TIME_LIMIT - (time() - $this->startTimeStamp)) + 10);
        }

        $this->resetRevAiVars();

        switch ($reason_code)
        {
            case self::WAIT_THEN_CURL_CAPTIONS:
                $this->curlGetCaptions();
                break;

            case self::WAIT_THEN_CURL_TRANSCRIPT:
                $this->curlGetTrans();
                break;
        }
    }



    // Helping Functions //


    function log($srq_id, $file_id, $activity, $opt_msg = null)
    {
        $this->srlogger->log($srq_id,$file_id,$activity, $opt_msg);
        $this->output($srq_id . " | " . $file_id. " | " .$activity. " | " . $opt_msg);
    }

    function output($msg)
    {
        $mainLog = __DIR__ . "/../../../revai-receive.log";
        $oldLog = __DIR__ . "/../../../revai-receive.old.log";

        $date = new DateTime();
        $date = $date->format("y:m:d h:i:s");
        $str = "[$date]: $msg";
        echo $str . "\n";

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

new SRProcessingCron($dbConnection);

