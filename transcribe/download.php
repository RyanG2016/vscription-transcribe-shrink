<?php

//This file is very sensitive to any echos as they get output to the rtf and prevents them from opening
//Make sure there are no echos in this file. (Aside from login echo)
require '../api/bootstrap.php';

use Mnvx\Lowrapper\Converter;
use Mnvx\Lowrapper\LowrapperParameters;
use Mnvx\Lowrapper\Format;

include('data/parts/head_without_zoho.php');
include('data/parts/constants.php');
include('data/parts/config.php');
include('data/parts/common_functions.php');
require_once(__DIR__ . '/rtf3/src/HtmlToRtf.php');

if (!isset($_GET['down'])) {
    header("Location: index.php");
    exit();
}

$hash = $_GET['down'];


//* get download file_id and acc_id of that file related to the hash parameter from get *//
$sql3 = "SELECT * FROM downloads WHERE hash = ? and expired = 0";
if ($stmt3 = mysqli_prepare($con, $sql3)) {
    mysqli_stmt_bind_param($stmt3, "s", $hash);
    if (mysqli_stmt_execute($stmt3)) {
        $result = mysqli_stmt_get_result($stmt3);
        // Check number of rows in the result set
        if (mysqli_num_rows($result) == 1) {
            /** PERMISSION OK HASH OK - NOT EXPIRED */

            // Fetch result rows as an associative array
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $file_id = $row['file_id'];
            $acc_id = $row['acc_id'];

            /** check if the current user acc_id match */
            if ($acc_id == $_SESSION['accID']) {
                /** Update download statistics */
                incrementDownloadCounter($con, $file_id, $acc_id);

                /** set download link as expired */
                expireDownloadLink($con, $file_id, $acc_id);

                /** get file data to prepare for download */
                downloadFile($con, $file_id, $acc_id);

            } else {
                /** PERMISSION DENIED ACCOUNT ID DOESN'T MATCH */
                header("Location: accessdenied.php");
            }


        } else {
            /** PERMISSION DENIED ACCOUNT ID DOESN'T MATCH */
            header("Location: accessdenied.php");
            return false;

        }
    } else {
        //echo "Error executing " .$sql3;
    }
} else {
    //echo "ERROR: Could not prepare to execute $sql1. " . mysqli_error($con);
    //die( "Error in excute: (" .$con->errno . ") " . $con->error);
}

function downloadFile($con, $fileID, $accID)
{

    /*------Generate File download ------*/
    $sql = "
    SELECT 
       job_id, 
       file_author,
       file_transcribed_date,
       file_work_type,
       audio_length,
       file_date_dict,
       file_transcribed_date,
       typist_comments,
       file_comment,
       job_document_html
            FROM files WHERE file_id=? AND acc_id = ?";

    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $fileID, $accID);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            // Check number of rows in the result set
            if (mysqli_num_rows($result) > 0) {
                //echo "We found at least one row for job " . $job_id;
                // Fetch result rows as an associative array
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                    $filename = $row['job_id'];
                    $rtf = generateHTMLReport($row["job_document_html"], $row);

                    header('Content-Disposition: attachment; filename="' . $filename . '.rtf"');
                    header('Content-Type: text/plain'); # Don't use application/force-download - it's not a real MIME type, and the Content-Disposition header is sufficient
                    header('Content-Length: ' . strlen($rtf));
                    header('Connection: close');
                    echo $rtf;
                }
            }
        } else {
            // error connecting to DB
        }
    } else {
        //echo "<p>No matches found for job " .$job_id . "</p>";
    }
}

/**
 * Converts HTML to RTF
 * @param $report string html report text
 * @return string RTF data
 */
function convertHTMLToRTF($report)
{

//    $report = generateHTMLReport($html, $row);
//    file_put_contents($tmpHtmlFile , html_entity_decode($html, ENT_QUOTES));
//    $tmpHtmlFile = "test/" . $row["job_id"] . ".html";
    $outputDir = realpath(__DIR__."/convertTmp/");
    // Convert HTML to RTF
    $converter = new Converter("soffice", $outputDir);
    // $converter->setLogger(new Logger());
    // Describe parameters for converter

    $parameters = (new LowrapperParameters())
        ->setInputData(html_entity_decode($report, ENT_QUOTES))
//        ->setOutputFile(realpath(__DIR__."/test/".'outputResultMan.docx'))
//        ->setOutputFile( $outputDir."\\output.docx" )
        ->setOutputFormat(Format::TEXT_RTF);


    // Run converter
    try {
        $result = $converter->convert($parameters);
    } catch (\Mnvx\Lowrapper\LowrapperException $e) {
//        echo $e;
        $result = "Error occurred while generating file, please try again or contact system admin.";
    }

//    $htmlToRtfConverter = new HtmlToRtf\HtmlToRtf($report);
//    return trim($htmlToRtfConverter->getRTF());
    return $result;
}

/**
 * Generates RTF text from HTML
 * @param $html string html text
 * @param $row array file entry from db
 * @return string RTF
 */

function generateHTMLReport($html, $row)
{
    $initials = strtolower(substr($_SESSION['fname'], 0, 1)) . strtolower(substr($_SESSION['lname'], 0, 1));

    $report = '<body style="font-family: Arial, Helvetica, sans-serif">';
    $report .= '<b>' . 'Job Number: ' . '</b>' . $row['job_id'] . '<br/>';
    $report .= '<b>' . 'Author Name: ' . '</b>' . $row['file_author'] . '<br/>';
    $report .= '<b>' . 'Typist Name: ' . '</b>' . $initials . '<br/>';
    $report .= '<b>' . 'Job Type: ' . '</b>' . ucfirst($row['file_work_type']) . '<br/>';
    $report .= '<b>' . 'Job Length: ' . '</b>';

    $report .= gmdate("H:i:s", $row['audio_length']);
    $report .= "<br/>";
    $report .= '<b>' . 'Date Dictated: ' . '</b>' . $row['file_date_dict'] . '<br/>';
    $report .= '<b>' . 'Date Transcribed: ' . '</b>' . $row['file_transcribed_date'] . '<br/>';
    $report .= '<b>' . 'File Comments: ' . '</b>' . $row['file_comment'] . '<br/>';
    $report .= '<b>' . 'Typist Comments: ' . '</b>' . $row['typist_comments'] . '<br/>';

    $report .= '<br/>';
    $report .= '<br/>';
    $decodedHTML = html_entity_decode($html, ENT_QUOTES);
    $decodedHTML = str_replace("&lt;INAUDIBLE&gt;", "-INAUDIBLE-", $decodedHTML);
    $report .= $decodedHTML;
    $report .= "</body>";

    return convertHTMLToRTF($report);
}

function incrementDownloadCounter($con, $fileID, $accID)
{
    /*------Update download statistics ------*/

    $text_downloaded_date = date("Y-m-d H:i:s");

    $sql1 = "UPDATE files SET times_text_downloaded_date=times_text_downloaded_date+1, text_downloaded_date=COALESCE(text_downloaded_date, ?)
       WHERE file_id = ? AND acc_id = ?";

    if ($stmt1 = mysqli_prepare($con, $sql1)) {
        if (!$stmt1->bind_param("sii", $text_downloaded_date, $fileID, $accID)) {
            // die( "Error in bind_param: (" .$con->errno . ") " . $con->error);
        }
        $B = mysqli_stmt_execute($stmt1);
        if ($B) { // file download incremented
            $ip = getIP2();
            /** logging download statistics */

            $a = array(
                'email' => $_SESSION['uEmail'],
                'activity' => 'Downloading file ' . $fileID,
                'actPage' => 'download.php',
                //'actPage' => header('Location: '.$_SERVER['REQUEST_URI']),   //This isn't working. For now am going to hardcode the page into the function call
                'actIP' => $ip,
                'acc_id' => $_SESSION['accID']
            );
            $b = json_encode($a);
            insertAuditLogEntry($con, $b);
        } else {
            //echo "ERROR: Could not able to execute $sql1. " . mysqli_error($con);
            //die( "Error in excute: (" .$con->errno . ") " . $con->error);
        }
    } else {
        //echo "ERROR: Could not able to execute $sql1. " . mysqli_error($con);
    }
}

function expireDownloadLink($con, $fileID, $accID)
{
    //* get download file_id and acc_id of that file related to the hash parameter from get *//
    $sql3 = "UPDATE downloads set expired = 1 where file_id = ? and acc_id = ?";
    if ($stmt3 = mysqli_prepare($con, $sql3)) {
        mysqli_stmt_bind_param($stmt3, "ii", $fileID, $accID);
        if (mysqli_stmt_execute($stmt3)) {
            $result = mysqli_stmt_get_result($stmt3);
            // Check number of rows in the result set
            if ($result) {
                // UPDATE OK
                return true;
            } else {
                // FAILED TO EXPIRE DOWNLOAD LINK
                return false;

            }
        } else {
            //echo "Error executing " .$sql3;
        }
    } else {
        //echo "ERROR: Could not prepare to execute $sql1. " . mysqli_error($con);
        //die( "Error in excute: (" .$con->errno . ") " . $con->error);
    }
    return true;
}