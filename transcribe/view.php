<?php
require '../api/bootstrap.php';

//use Mnvx\Lowrapper\Converter;
//use Mnvx\Lowrapper\LowrapperParameters;
//use Mnvx\Lowrapper\Format;

include('data/parts/head.php');
include('data/parts/constants.php');
include('data/parts/config.php');
include('data/parts/common_functions.php');


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
//                /** Update download/view statistics */
                updateInitDownloadDate($con, $file_id, $acc_id);

                /** set download link as expired */
                expireDownloadLink($con, $file_id, $acc_id);

                /** get file data to prepare for download */
                //moved below

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

function updateInitDownloadDate($con, $fileID, $accID)
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
        if ($B) {
            /** logging download statistics */

            $a = array(
                'email' => $_SESSION['uEmail'],
                'activity' => 'File viewed: ' . $fileID,
                'actPage' => 'view.php',
                //'actPage' => header('Location: '.$_SERVER['REQUEST_URI']),   //This isn't working. For now am going to hardcode the page into the function call
                'actIP' => getIP2(),
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

?>

<html lang="en">

<head>
    <?php include_once("gaTrackingCode.php");?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <noscript>
        <meta http-equiv="refresh" content="0;url=noscript.php">
    </noscript>
    <meta name="viewport" content="width=device-width, initial-scale=1">


<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>-->

    <!--  MDC Components  -->
<!--    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">-->
<!--    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>-->
<!---->
<!--    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">-->
<!--    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>-->



<!--    <script src="data/scripts/main.min.js?v=3"></script>-->


    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>
    <!--    <link rel="stylesheet" href="data/css/vs-style.css">-->

    <title>vScription Job Viewer</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
<!--    <link rel="stylesheet" href="data/css/job_lister_form_5.css">-->

<!--    <link rel="stylesheet" href="data/main/jquery-ui.css">-->
    <script src="data/main/jquery.js"></script>
<!--    <script src="data/main/jquery-ui.js"></script>-->

    <!--	Scroll Bar Dependencies    -->

    <script src="data/scrollbar/jquery.nicescroll.js"></script>


    <!-- BOOTSTRAP -->
<!---->
<!--    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"-->
<!--            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"-->
<!--            crossorigin="anonymous"></script>-->
<!---->
<!--    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">-->
<!--    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>-->
<!---->
<!--    -->
    <link rel="stylesheet" href="data/css/view.css">
    <script type="text/javascript">
        $(".vtex-report-body").niceScroll({
            hwacceleration: true,
            smoothscroll: true,
            cursorcolor: "white",
            cursorborder: 0,
            scrollspeed: 10,
            mousescrollstep: 20,
            cursoropacitymax: 0.7
            //  cursorwidth: 16

        });
    </script>
</head>

<body>

<div class="vtex-card">


<?php
viewFile($con, $file_id, $acc_id);

function viewFile($con, $fileID, $accID)
{

    /*------Generate File  ------*/
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
//                    $filename = $row['job_id'];

                    echo generateHTMLReport($row["job_document_html"], $row);

//                    header('Content-Disposition: attachment; filename="' . $filename . '.rtf"');
//                    header('Content-Type: text/plain'); # Don't use application/force-download - it's not a real MIME type, and the Content-Disposition header is sufficient
//                    header('Content-Length: ' . strlen($rtf));
//                    header('Connection: close');
//                    echo $rtf;
                }
            }
        } else {
            // error connecting to DB
        }
    } else {
        //echo "<p>No matches found for job " .$job_id . "</p>";
    }
}

//
///**
// * Converts HTML to RTF
// * @param $report string html report text
// * @return string RTF data
// */
//function convertHTMLToRTF($report)
//{
//
////    $report = generateHTMLReport($html, $row);
////    file_put_contents($tmpHtmlFile , html_entity_decode($html, ENT_QUOTES));
////    $tmpHtmlFile = "test/" . $row["job_id"] . ".html";
//    $outputDir = realpath(__DIR__."/convertTmp/");
//    // Convert HTML to RTF
//    $converter = new Converter("soffice", $outputDir);
//    // $converter->setLogger(new Logger());
//    // Describe parameters for converter
//
//    $parameters = (new LowrapperParameters())
//        ->setInputData(html_entity_decode($report, ENT_QUOTES))
////        ->setOutputFile(realpath(__DIR__."/test/".'outputResultMan.docx'))
////        ->setOutputFile( $outputDir."\\output.docx" )
//        ->setOutputFormat(Format::TEXT_RTF);
//
//
//    // Run converter
//    try {
//        $result = $converter->convert($parameters);
//    } catch (\Mnvx\Lowrapper\LowrapperException $e) {
////        echo $e;
//        $result = "Error occurred while generating file, please try again or contact system admin.";
//    }
//
////    $htmlToRtfConverter = new HtmlToRtf\HtmlToRtf($report);
////    return trim($htmlToRtfConverter->getRTF());
//    return $result;
//}

/**
 * Generates RTF text from HTML
 * @param $html string html text
 * @param $row array file entry from db
 * @return string RTF
 */

function generateHTMLReport($html, $row)
{
    $initials = strtolower(substr($_SESSION['fname'], 0, 1)) . strtolower(substr($_SESSION['lname'], 0, 1));

//    $report = '<body style="font-family: Arial, Helvetica, sans-serif">';

    $report = '<table><tr><td><b>' . 'Job Number: ' . '</b></td><td>' . $row['job_id'] . '</td>';
    $report .= '<tr><td><b>' . 'Author Name: ' . '</b></td><td>' . $row['file_author'] . '</td>';
    $report .= '<tr><td><b>' . 'Typist Name: ' . '</b></td><td>' . $initials . '</td>';
    $report .= '<tr><td><b>' . 'Job Type: ' . '</b></td><td>' . ucfirst($row['file_work_type']) . '</td>';
    $report .= '<tr><td><b>' . 'Job Length: ' . '</b></td><td>';

    $report .= gmdate("H:i:s", $row['audio_length']);
    $report .= "</td>";
    $report .= '<tr><td><b>' . 'Date Dictated: ' . '</b></td><td>' . $row['file_date_dict'] . '</td>';
    $report .= '<tr><td><b>' . 'Date Transcribed: ' . '</b></td><td>' . $row['file_transcribed_date'] . '</td>';
    $report .= '<tr><td><b>' . 'File Comments: ' . '</b></td><td>' . $row['file_comment'] . '</td>';
    $report .= '<tr><td><b>' . 'Typist Comments: ' . '</b></td><td>' . $row['typist_comments'] . '</td>
</table>';

    $report .= '';
    $report .= '<br/><div class="vtex-report-body">';
    $report .= html_entity_decode($html, ENT_QUOTES);
    $report .= "</div>";
//    $report .= "</body>";

    return $report;
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

?>

</div>
</body>


</html>