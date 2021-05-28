<?php
//$rootDir = "C:\\xampp\htdocs\\vscription";

//require "$rootDir\api\bootstrap.php";
//require "$rootDir\api\src\TableGateways\common.php";
//require '../../bootstrap.php';

namespace Src\CronJobs;

use Src\CronJobs\CronConfiguration;
use Src\Enums\FILE_STATUS;
use Src\Enums\SRQ_STATUS;
use Src\Enums\PHP_SERVICES_IDS;
use Src\TableGateways\conversionGateway;
use Src\TableGateways\FileGateway;
use Src\Models\SRQueue;
use Src\Helpers\common;
use Src\Models\PHPService;
use DateTime;

require __DIR__ . "/../../bootstrap.php";
require_once( __DIR__ . '/../../../audioParser/getid3/getid3.php');


class conversionService
{
    private $dir = __DIR__;
    private $rootDir;
    private $uploadsDir;
    private $shellDir;
    private $batchFile;
    private $switchPath;
    private $plainName;
    private $orgFile;
    private $orgFileSize;
    private $org_ext;

    private $retries;

    private $file_id;
    private $fileName;
    private $file_status;
    private $acc_id;

    // switch process status vars //
    private $firstCheckPass;
    private $kill_pattern;
    private $start;
    private $currentIterations;

    // gatways
    private $conversionsGateway;
    private $fileGateway;

    public function __construct(
        private $db,
        private $conv_ext = ".mp3"
    )
    {
        $this->rootDir = realpath($this->dir . "/../../../");
        $this->uploadsDir = "$this->rootDir\uploads";
        $this->shellDir = "$this->rootDir\convert_shell";
        $this->switchPath = "C:\Program Files (x86)\NCH Software\Switch\switch.exe";

        $this->conversionsGateway = new conversionGateway($this->db);
        $this->fileGateway = new FileGateway($this->db);

        $this->firstCheckPass = false;
        $this->kill_pattern = '~(switch)\.exe~i';
        $this->start = true;
        $this->currentIterations = 0;

        $this->workflow();
    }

    function workflow(){
        $service = PHPService::withID(PHP_SERVICES_IDS::CONVERSION_SERVICE, $this->db);

        $service->updateStartTime();

        // loop

        while ($this->start) {
//            global $file_status;
//            global $acc_id;

            $this->currentIterations += 1;

            if($this->currentIterations >= CronConfiguration::MAX_ITERATIONS)
            {
                $start = false;
                $service->updateStopTime();
                die("Shutting Down");
            }

            $fileEntry = $this->conversionsGateway->findAll(true);
            if(sizeof($fileEntry) > 0)
            {
                $this->retries = 0;
                $fileEntry = $fileEntry[0];
                $this->acc_id = $fileEntry["acc_id"];
                // print_r($fileEntry);
                $fileName = $fileEntry["filename"];
                $this->fileName = $fileName;
                $this->org_ext = $fileEntry["org_ext"];
                $this->orgFile = $this->uploadsDir . "\\". $fileName;
                $this->orgFileSize = filesize($this->orgFile);
                $this->plainName = pathinfo($fileName, PATHINFO_FILENAME);
                $this->file_id = $fileEntry["file_id"];
                $this->file_status = $fileEntry["file_status"];

                if($this->file_status != FILE_STATUS::QUEUED_FOR_CONVERSION && $this->file_status != FILE_STATUS::QUEUED_FOR_SR_CONVERSION)
                {
//                    $this->conversionsGateway->updateConversionStatusFromParam($this->file_id ,2); // need review // todo uncomment
                }

                $this->convertDssToMp3($fileName);
            }else{
//        vtexEcho("nothing to convert.. waiting\n");
        echo "[" . (new DateTime())->format("y:m:d h:i:s")."] " . "Nothing to convert.. waiting - " . $this->currentIterations . "\n" ;

            }

//    exit();
            sleep(CronConfiguration::CONVERSION_SLEEP_TIME); // check intervals of 5 seconds
        }


//        $this->convertDssToMp3($fileName);
//        $this->vtexEcho("\n\n");

    }


    // functions
    function convertDssToMp3($fileName)
    {
//    global $org_ext;
//    global $retries;
//        global $uploadsDir;
//        global $switchPath;
//        global $conv_ext;
//        global $plainName;
//        global $orgFile;
//        global $orgFileSize;
//        global $file_id;
//        global $conversionsGateway;
//        global $fileGateway;
//        global $file_status;
//        global $acc_id;
//        global $dbConnection;


        $command = "'$this->switchPath' -convert ".escapeshellarg($this->orgFile)." -outfolder ".escapeshellarg($this->uploadsDir)." -format $this->conv_ext -overwrite ALWAYS -exit";
        // $command = "'$switchPath' -convert '$orgFile' -outfolder '$uploadsDir' -format $conv_ext -overwrite ALWAYS -exit";
        // $command = '"C:\Program Files (x86)\NCH Software\Switch\switch.exe" -convert "D:\tmp\test\dss\d.DS2" -outfolder "D:\tmp\test\dss" -settempfolder "D:\tmp\test\dss\tmp" -format .mp3 -overwrite ALWAYS -hide -exit';
        // $command = '"C:\Program Files (x86)\NCH Software\Switch\switch.exe" -convert "D:\tmp\test\dss\d.DS2" -outfolder "D:\tmp\test\dss" -format .mp3 -overwrite ALWAYS -hide -exit';
        
        $taskName = "_F" . $this->file_id . "_" . time();
        // escapeshellarg($command);
        // todo command to shell file
        // generate batch command file
        $this->batchFile = $this->shellDir . "\\" . $taskName . ".bat";
        file_put_contents($this->batchFile, $command.PHP_EOL , LOCK_EX);

        $this->vtexEcho(shell_exec('SCHTASKS /F /Create /TN '.$taskName.' /TR "'.$this->batchFile.'" /SC MONTHLY /RU INTERACTIVE'));
        $this->addSpace();
        $this->vtexEcho(shell_exec('SCHTASKS /RUN /TN "'.$taskName.'"'));
        $this->addSpace();

        // lets start checking for status for this job
        $this->vtexEcho(" -- Monitoring convert status -- \n");

        // $statusCommand = "schtasks.exe /query  /tn \"$taskName\"";
        $checking = true;
        $convertStartTime = time();
        while ($checking) {

            echo "\n";
            sleep(5); // check convert progress intervals of 5 seconds
            $switchDone = $this->checkSwitchDone();

            if($switchDone) {

                // conversion done or failed
                // check for converted file existence and logical size

                $convertedFile = $this->uploadsDir . "\\" . $this->plainName . $this->conv_ext;
                $convertedFileExists = file_exists($convertedFile);
                $this->vtexEcho("File Exists = " . $convertedFileExists . "\n");
                if ($convertedFileExists) {
                    // check logical size
                    if (filesize($convertedFile) > $this->orgFileSize) {
                        // conversion OK

                        $this->vtexEcho("File converted successfully to " . $this->conv_ext . "\n");

                        $diff = time() - $convertStartTime;
                        $this->vtexEcho("Finish time: " . $diff . " secs\n" );
                        $this->vtexEcho("Finish time: ".$this->formatSecs($diff)." \n" );

                        $this->conversionsGateway->updateConversionStatusFromParam($this->file_id, 1);

                        // send files with prev status of 9 to speech recognition queue
                        if($this->file_status == FILE_STATUS::QUEUED_FOR_SR_CONVERSION)
                        {
                            $this->fileGateway->directUpdateFileStatus($this->file_id, FILE_STATUS::QUEUED_FOR_RECOGNITION, $this->plainName . $this->conv_ext);

                            // move to queue SRQueue entry for this file
                            $srq = SRQueue::withFileID($this->file_id, $this->db);
                            $srq->setSrqStatus(SRQ_STATUS::QUEUED);
                            $srq->save();

                        }else{
                            $this->fileGateway->directUpdateFileStatus($this->file_id, FILE_STATUS::AWAITING_TRANSCRIPTION, $this->plainName . $this->conv_ext);
                        }


                    } else {
                        // partial conversion - fail
                        // delete it and set as failed
                        unlink($convertedFile);

                        // clear prev task/shell entries
                        $this->vtexEcho( shell_exec('SCHTASKS /DELETE /TN "' . $taskName . '" /F') );
                        unlink($this->batchFile); // delete task batch file
                        $this->retryConvert();
                        return;
                    }
                } else {
                    // conversion failed
                    // clear prev task/shell entries
                    $this->vtexEcho( shell_exec('SCHTASKS /DELETE /TN "' . $taskName . '" /F') );
                    unlink($this->batchFile); // delete task batch file
                    $this->retryConvert();
                    return;
                }

                // Delete the scheduled task for this file
                $this->vtexEcho("Deleting Job Queue Entry \n");
                $this->vtexEcho( shell_exec('SCHTASKS /DELETE /TN "' . $taskName . '" /F') );
                unlink($this->batchFile); // delete task batch file
                $checking = false;

                $this->addSpace();
            }else{
                // still converting
//            vtexEcho("==> X Switch is still running..\n");
                $diff = time() - $convertStartTime;
                $this->vtexEcho("File: ($this->file_id) - " . $fileName . "\n");
                $this->vtexEcho("Elapsed time: " . $diff . " secs\n" );
                $this->vtexEcho("Elapsed time: ".$this->formatSecs($diff)." \n" );
            }

            /*0  State = 'Unknown'
            1  State = 'Disabled'
            2  State = 'Queued'
            3  State = 'Ready'
            4  State = 'Running'*/

        }


//    echo shell_exec('SCHTASKS /DELETE /TN "'.$taskName.'" /F');
//    addSpace();
    }

    /** Checks if switch.exe is still running or not
     * @return bool true if switch has closed == completed.<br>
     *              false if switch is still running == converting.
     */
    function checkSwitchDone()
    {
//    global  $task_list;

        $task_list = array();
        exec("tasklist 2>NUL", $task_list);

        $switchFound = false;
        foreach ($task_list AS $task_line)
        {
            if (preg_match($this->kill_pattern, $task_line, $out))
            {
                $this->vtexEcho("=> Detected: ".$out[1]." - file convert in progress..\n");
//            $switchFound = true;
                return false;
//        exec("taskkill /F /IM ".$out[1].".exe 2>NUL");
            }
        }
        if(!$switchFound)
        {
            if(!$this->firstCheckPass){
                $this->vtexEcho("=> Conversion may be completed.\n");
                $this->vtexEcho("Checking again in (5)\n");
                sleep(1);
                $this->vtexEcho("Checking again in (4)\n");
                sleep(1);
                $this->vtexEcho("Checking again in (3)\n");
                sleep(1);
                $this->vtexEcho("Checking again in (2)\n");
                sleep(1);
                $this->vtexEcho("Checking again in (1)\n");
                sleep(1);
                $this->firstCheckPass = true;
                return $this->checkSwitchDone();
            }else{
                $this->vtexEcho("=> NCH convert completed.\n");
                $this->firstCheckPass = false;
                return true;
            }
        }
    }

    function vtexEcho($msg){
//        global $rootDir;
        $mainLog = "$this->rootDir\\convert.log";
        $oldLog = "$this->rootDir\\convert.old.log";

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

    function retryConvert(){
//        global $conversionsGateway;
//        global $retries;
//        global $fileName;
//        global $file_id;

        if($this->retries >= 2){
            $this->vtexEcho("Failed to convert file\n");
            $this->conversionsGateway->updateConversionStatusFromParam($this->file_id ,3);
        }
        else{
            $this->vtexEcho("Conversion failed - retrying ($this->retries)");
            $this->addSpace();
            $this->retries++;
            $this->convertDssToMp3($this->fileName);
        }
    }

    function formatSecs($seconds) {
        $t = round($seconds);
        return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
    }

    function addSpace(){
        echo "\n\n";
        $this->vtexEcho("--------------------------------");
        echo "\n\n";
    }
}

new conversionService($dbConnection);