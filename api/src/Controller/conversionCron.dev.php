<?php
$rootDir = "C:\\xampp\htdocs\\vscription";
$switchPath = "C:\Program Files (x86)\NCH Software\Switch\switch.exe";

require "$rootDir\api\bootstrap.php";
require "$rootDir\api\src\TableGateways\common.php";
//require '../../bootstrap.php';
use Src\TableGateways\conversionGateway;
use Src\TableGateways\FileGateway;

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
$uploadsDir = "$rootDir\uploads";

//$word = new com("IDPMControl.Initialize") or die("Unable to instantiate Word");
//echo "Loaded Word, version {$word->Version}\n";

$conv_ext = ".mp3";

$start = true;


while ($start) {
    $fileEntry = $conversionsGateway->findAll(true);
    if(sizeof($fileEntry) > 0)
    {
        $retries = 0;
        $fileEntry = $fileEntry[0];
        print_r($fileEntry);
        $fileName = $fileEntry["filename"];
        $org_ext = $fileEntry["org_ext"];
        $orgFile = $uploadsDir . "\\". $fileName;
        $orgFileSize = filesize($orgFile);
        $plainName = pathinfo($fileName, PATHINFO_FILENAME);
        $file_id = $fileEntry["file_id"];
        $fileStatus = $fileEntry["file_status"]; // todo check if 8 (Queued for conversion)

        if($fileStatus != 8)
        {
            $conversionsGateway->updateConversionStatusFromParam($file_id ,2); // need review
        }

        convertDssToMp3($fileName);
    }else{
        echo "nothing to convert.. waiting\n";
    }

//    exit();
    sleep(5); // check intervals of 5 seconds
}


convertDssToMp3($fileName);

echo "\n";
echo "\n";

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

//    $command = "'$switchPath' -convert '$uploadsDir\out\\$fileName' -settempfolder '$uploadsDir\out\\tmp\' -format .mp3 -overwrite ALWAYS -hide -exit";
    $command = "'$switchPath' -convert '$orgFile' -format $conv_ext -overwrite ALWAYS -hide -exit";

    $taskName = "_" . $fileName . "_" . time();
    escapeshellarg($command);
    echo shell_exec('SCHTASKS /F /Create /TN '.$taskName.' /TR "'.$command.'" /SC MONTHLY /RU INTERACTIVE');
    addSpace();
    echo shell_exec('SCHTASKS /RUN /TN "'.$taskName.'"');
    addSpace();

    // lets start checking for status for this job
    echo " -- Start checking job completion status -- ";

    $statusCommand = "schtasks.exe /query  /tn \"$taskName\"";
    $checking = true;
    while ($checking) {

        sleep(2); // check convert progress intervals of 2 seconds
        $result = exec($statusCommand, $output, $return_var);
//        echo $result;
        echo "\n";
        preg_match('/ (\w+$)/', $result, $output_array);
        $status = $output_array[1];
        echo "Status = " . $status; // Ready or Running - else should error out
        echo "\n";
        switch($status){
            case "Ready":
                // conversion done or failed
                // check for converted file existence and logical size

                $convertedFile = $uploadsDir . "\\" . $plainName.$conv_ext;
                $convertedFileExists = file_exists($convertedFile);
                echo "File Exists = " . $convertedFileExists . "\n";
                if($convertedFileExists)
                {
                    // check logical size
                    if(filesize($convertedFile) > $orgFileSize)
                    {
                        // conversion OK
                        echo "File converted successfully to " . $conv_ext . "\n";

                        $conversionsGateway->updateConversionStatusFromParam($file_id ,1);
                        $fileGateway->directUpdateFileStatus($file_id ,0, $plainName.$conv_ext);

                    }else{
                        // partial conversion - fail
                        // delete it and set as failed
                        unlink($convertedFile);
                        retryConvert();
                    }
                } else{
                    // conversion failed
                    retryConvert();
                }

                // Delete the scheduled task for this file
                echo "Deleting Job Queue Entry \n";
                echo shell_exec('SCHTASKS /DELETE /TN "'.$taskName.'" /F');
                $checking = false;
                break;

            case "Running":
                // still converting
                echo "Conversion in progress for file: " . $fileName;
                echo "\n";
                break;

            default:
                echo "Unexpected response recieved - conversion failed";
                $checking = false;
                break;
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

//_exec("youexe.exe");
function retryConvert(){
    global $conversionsGateway;
    global $retries;
    global $fileName;
    global $file_id;

    if($retries >= 2){
        echo "Failed to convert file\n";
        $conversionsGateway->updateConversionStatusFromParam($file_id ,3);
    }
    else{
        echo "Conversion failed - retrying ($retries)";
        addSpace();
        $retries++;
        convertDssToMp3($fileName);
    }
}
function addSpace(){
    echo "\n";
    echo "\n";
    echo "--------------------------------";
    echo "\n";
    echo "\n";
}