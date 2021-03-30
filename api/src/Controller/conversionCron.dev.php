<?php
$rootDir = "C:\\xampp\htdocs\\vscription";
$switchPath = "C:\Program Files (x86)\NCH Software\Switch\switch.exe";

require "$rootDir\api\bootstrap.php";
require "$rootDir\api\src\TableGateways\common.php";
//require '../../bootstrap.php';
use Src\Enums\FILE_STATUS;
use Src\Enums\SRQ_STATUS;
use Src\TableGateways\conversionGateway;
use Src\TableGateways\FileGateway;
use Src\Models\SRQueue;

global $conversionsGateway;
global $fileGateway;
$conversionsGateway = new conversionGateway($dbConnection);
$fileGateway = new FileGateway($dbConnection);

global $uploadsDir;
global $switchPath;
global $conv_ext;
global $org_ext;
global $plainName;
global $orgFile;
global $orgFileSize;
global $retries;
global $file_id;
global $file_status;
global $acc_id;
global $dbConnection;

// switch process status vars //
global $firstCheckPass;
global $kill_pattern;
$kill_pattern = '~(switch)\.exe~i';
$firstCheckPass = false;

$uploadsDir = "$rootDir\uploads";

//$word = new com("IDPMControl.Initialize") or die("Unable to instantiate Word");
//echo "Loaded Word, version {$word->Version}\n";

$conv_ext = ".mp3";

$start = true;


while ($start) {
    global $file_status;
    global $acc_id;
    $fileEntry = $conversionsGateway->findAll(true);
    if(sizeof($fileEntry) > 0)
    {
        $retries = 0;
        $fileEntry = $fileEntry[0];
        $acc_id = $fileEntry["acc_id"];
//        print_r($fileEntry);
        $fileName = $fileEntry["filename"];
        $org_ext = $fileEntry["org_ext"];
        $orgFile = $uploadsDir . "\\". $fileName;
        $orgFileSize = filesize($orgFile);
        $plainName = pathinfo($fileName, PATHINFO_FILENAME);
        $file_id = $fileEntry["file_id"];
        $fileStatus = $fileEntry["file_status"];

        if($fileStatus != FILE_STATUS::QUEUED_FOR_CONVERSION && $fileStatus != FILE_STATUS::QUEUED_FOR_SR_CONVERSION)
        {
            $conversionsGateway->updateConversionStatusFromParam($file_id ,2); // need review
        }

        convertDssToMp3($fileName);
    }else{
//        vtexEcho("nothing to convert.. waiting\n");
        echo "[" . (new DateTime())->format("y:m:d h:i:s")."] " . "Nothing to convert.. waiting\n";
    }

//    exit();
    sleep(5); // check intervals of 5 seconds
}


convertDssToMp3($fileName);

vtexEcho("\n\n");

function convertDssToMp3($fileName)
{
//    global $org_ext;
//    global $retries;
    global $uploadsDir;
    global $switchPath;
    global $conv_ext;
    global $plainName;
    global $orgFile;
    global $orgFileSize;
    global $file_id;
    global $conversionsGateway;
    global $fileGateway;
    global $file_status;
    global $acc_id;
    global $dbConnection;

//    $command = "'$switchPath' -convert '$uploadsDir\out\\$fileName' -settempfolder '$uploadsDir\out\\tmp\' -format .mp3 -overwrite ALWAYS -hide -exit";
    //    $command = "'$switchPath' -convert '$orgFile' -outfolder '$uploadsDir' -format $conv_ext -overwrite ALWAYS -hide -exit";
    $command = "'$switchPath' -convert '$orgFile' -outfolder '$uploadsDir' -format $conv_ext -overwrite ALWAYS -exit";

    $taskName = "_" . $fileName . "_" . time();
    escapeshellarg($command);
    vtexEcho(shell_exec('SCHTASKS /F /Create /TN '.$taskName.' /TR "'.$command.'" /SC MONTHLY /RU INTERACTIVE'));
    addSpace();
    vtexEcho(shell_exec('SCHTASKS /RUN /TN "'.$taskName.'"'));
    addSpace();

    // lets start checking for status for this job
    vtexEcho(" -- Monitoring convert status -- \n");

//    $statusCommand = "schtasks.exe /query  /tn \"$taskName\"";
    $checking = true;
    $convertStartTime = time();
    while ($checking) {

        echo "\n";
        sleep(5); // check convert progress intervals of 5 seconds
        $switchDone = checkSwitchDone();

        if($switchDone) {

            // conversion done or failed
            // check for converted file existence and logical size

            $convertedFile = $uploadsDir . "\\" . $plainName . $conv_ext;
            $convertedFileExists = file_exists($convertedFile);
            vtexEcho("File Exists = " . $convertedFileExists . "\n");
            if ($convertedFileExists) {
                // check logical size
                if (filesize($convertedFile) > $orgFileSize) {
                    // conversion OK

                    vtexEcho("File converted successfully to " . $conv_ext . "\n");

                    $diff = time() - $convertStartTime;
                    vtexEcho("Finish time: " . $diff . " secs\n" );
                    vtexEcho("Finish time: ".formatSecs($diff)." \n" );

                    $conversionsGateway->updateConversionStatusFromParam($file_id, 1);

                    // send files with prev status of 9 to speech recognition queue
                    if($file_status == FILE_STATUS::QUEUED_FOR_SR_CONVERSION)
                    {
                        $fileGateway->directUpdateFileStatus($file_id, FILE_STATUS::QUEUED_FOR_RECOGNITION, $plainName . $conv_ext);

                        // move to queue SRQueue entry for this file
                        $srq = SRQueue::withFileID($file_id, $dbConnection);
                        $srq->setSrqStatus(SRQ_STATUS::QUEUED);
                        $srq->save();

                    }else{
                        $fileGateway->directUpdateFileStatus($file_id, FILE_STATUS::AWAITING_TRANSCRIPTION, $plainName . $conv_ext);
                    }


                } else {
                    // partial conversion - fail
                    // delete it and set as failed
                    unlink($convertedFile);
                    retryConvert();
                }
            } else {
                // conversion failed
                retryConvert();
            }

            // Delete the scheduled task for this file
            vtexEcho("Deleting Job Queue Entry \n");
            vtexEcho( shell_exec('SCHTASKS /DELETE /TN "' . $taskName . '" /F') );
            $checking = false;

            addSpace();
        }else{
            // still converting
//            vtexEcho("==> X Switch is still running..\n");
            $diff = time() - $convertStartTime;
            vtexEcho("File: ($file_id) - " . $fileName . "\n");
            vtexEcho("Elapsed time: " . $diff . " secs\n" );
            vtexEcho("Elapsed time: ".formatSecs($diff)." \n" );
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

/** This function checks if switch.exe is still running or not
 * @return true if switch has closed == completed.
 * @return false if switch is still running == converting.
 */
function checkSwitchDone()
{
//    global  $task_list;
    global $firstCheckPass;
    global $kill_pattern;

    $task_list = array();
    exec("tasklist 2>NUL", $task_list);

    $switchFound = false;
    foreach ($task_list AS $task_line)
    {
        if (preg_match($kill_pattern, $task_line, $out))
        {
            vtexEcho("=> Detected: ".$out[1]." - file convert in progress..\n");
//            $switchFound = true;
            return false;
//        exec("taskkill /F /IM ".$out[1].".exe 2>NUL");
        }
    }
    if(!$switchFound)
    {
        if(!$firstCheckPass){
            vtexEcho("=> Conversion may be completed.\n");
            vtexEcho("Checking again in (5)\n");
            sleep(1);
            vtexEcho("Checking again in (4)\n");
            sleep(1);
            vtexEcho("Checking again in (3)\n");
            sleep(1);
            vtexEcho("Checking again in (2)\n");
            sleep(1);
            vtexEcho("Checking again in (1)\n");
            sleep(1);
            $firstCheckPass = true;
            return checkSwitchDone();
        }else{
            vtexEcho("=> NCH convert completed.\n");
            $firstCheckPass = false;
            return true;
        }
    }
}

function vtexEcho($msg){
    global $rootDir;
    $mainLog = "$rootDir\\convert.log";
    $oldLog = "$rootDir\\convert.old.log";

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
    global $conversionsGateway;
    global $retries;
    global $fileName;
    global $file_id;

    if($retries >= 2){
        vtexEcho("Failed to convert file\n");
        $conversionsGateway->updateConversionStatusFromParam($file_id ,3);
    }
    else{
        vtexEcho("Conversion failed - retrying ($retries)");
        addSpace();
        $retries++;
        convertDssToMp3($fileName);
    }
}

function formatSecs($seconds) {
    $t = round($seconds);
    return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
}

function addSpace(){
    echo "\n\n";
    vtexEcho("--------------------------------");
    echo "\n\n";
}