<?php

//strtolower

include("config.php");
include(__DIR__ . "/../../../mail/mail_init.php");
include("common_functions.php");

//Should this go into the case statement? It was in the formsave.php file
require_once('../../rtf3/src/HtmlToRtf.php');
require_once('../regex.php');
//////////

include('session_settings.php');
include('constants.php');
$lang2 = 'en';

//$_SESSION['lastPing'] = date("Y-m-d H:i:s");
include("ping.php");

if (isset($_REQUEST["reqcode"])) {
    $code = $_REQUEST["reqcode"];


    if (isset($_REQUEST['args'])) {

        $args = $_REQUEST['args'];
        $a = json_decode($args, true);
    }


    switch ($code) {

        // Load Job Details for Player //
        // Also move audio file to working directory

        // this whole case needs adjustments as it is used in three different places some of them doesn't even need the tmp random file to be copied

        // currently known use to me is loading a job into the player in transcribe.js <<AKA LOAD BUTTON>>
        case 7:

            $a = json_decode($args, true);
            $file_id = $a['file_id'];
            $sql = "SELECT *
				FROM files WHERE file_id = ? and acc_id = ? and file_status != 3";

            if ($stmt = mysqli_prepare($con, $sql)) {
                mysqli_stmt_bind_param($stmt, "ii", $file_id, $_SESSION['accID']);

                if (mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);

                    // Check number of rows in the result set
                    if (mysqli_num_rows($result) > 0) {
                        // Fetch result rows as an associative array

                        // If user has permission and file record exist
                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

                            // check if the job record has a file saved on the server
                            if ($row['filename'] != "") {
                                // file exists

                                $tmpName = $row['tmp_name'];
                                /** checking first if there's already a tmp file for this job on the server */
                                if ($tmpName != null && $tmpName != "") {
                                    // Temp file name already exists in the db .. check if it's still on the server workingTmp directory
                                    if (checkIfTmpFileExists($tmpName)) {

                                        // pass the old file and exit this case

                                        $jobDetails = array(
                                            "file_id" => $row['file_id'],
                                            "job_id" => $row['job_id'],
                                            "file_author" => $row['file_author'],
                                            "origFilename" => $row['filename'],
                                            "suspendedText" => htmlentities($row['job_document_html'], ENT_QUOTES),
                                            "tempFilename" => $tmpName, /** REUSING OLD TMP FILE */
                                            "file_date_dict" => $row['file_date_dict'],
                                            "file_work_type" => $row['file_work_type'],
                                            "last_audio_position" => $row['last_audio_position'],
                                            "file_status" => $row['file_status'],
                                            "file_speaker_type" => $row['file_speaker_type'],
                                            "file_comment" => $row['file_comment']
                                        );

                                        echo json_encode($jobDetails);

                                        if ($row['file_status'] == 2 || $row['file_status'] == 0) // if the job was suspended/awaiting update it to being typed status = 1
                                        {
                                            updateJobStatus($con, $file_id, 1);
                                        }

                                        // TO AUDIT LOG (act_log)
                                        recordJobFileLoaded($con);
                                        break;
                                        /** BREAKING FROM THE CASE TO PREVENT FURTHER CODE EXECUTION */
                                    }
                                }

                                /** IF NO TMP FILE AVAILABLE FOR THE JOB CREATE A NEW ONE AND SAVE IT TO DB RECORD */

                                $randFileName = random_filename(".wav");

                                // These paths need to be relative to the PHP file making the call....

                                $path = "../../../uploads/" . $row['filename'];
                                $type = pathinfo($path, PATHINFO_EXTENSION);


                                if (copy('../../../uploads/' . $row['filename'], '../../workingTemp/' . $randFileName)) {

                                    // -> file is copied successfully to tmp -> set tmp value to db

                                    $jobDetails = array(
                                        "file_id" => $row['file_id'],
                                        "job_id" => $row['job_id'],
                                        "file_author" => $row['file_author'],
                                        "origFilename" => $row['filename'],
                                        "suspendedText" => htmlentities($row['job_document_html'], ENT_QUOTES),
                                        "tempFilename" => $randFileName,
                                        "file_date_dict" => $row['file_date_dict'],
                                        "file_work_type" => $row['file_work_type'],
                                        "last_audio_position" => $row['last_audio_position'],
                                        "file_status" => $row['file_status'],
                                        "file_speaker_type" => $row['file_speaker_type'],
                                        "file_comment" => $row['file_comment']
                                    );

                                    // add audit log entry for job file loaded
                                    recordJobFileLoaded($con);

                                    $statusToUpdate = $row['file_status'];
                                    // update status
                                    if ($row['file_status'] == 2 || $row['file_status'] == 0) // if the job was suspended/awaiting update it to being typed status = 1
                                    {
//                                        updateJobStatus($con, $file_id, 1 );
                                        $statusToUpdate = 1;
                                    }

                                    saveJobTmpFileNameToDbRecord($con, $row['file_id'], $randFileName, $statusToUpdate);

                                    // return the tmp_name & job details back to transcribe
                                    echo json_encode($jobDetails);

                                } else {
                                    //echo "Error moving file" . $randFileName . " to working directory..";
                                    echo false;

                                    break;
                                }

                            } else {
                                echo "No filename found in record --- ERROR"; //This should NEVER happen....Just for testing
                                break;
                            }

                        }
                    } else {
                        // todo file doesn't exist or you don't have permission to access this file
                        echo false;
                        break;
                    }
                } else {
                    // echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
                    break;
                }
            } else {
//					echo "ERROR: Could not prepare statement . " . mysqli_error($con);
                break;
            }
            // Close statement
            mysqli_stmt_close($stmt);

            break;

        // update job status only
        /** @deprecated use POST {api}/files/{file_id}/discard instead */
//        case 16:
//
//            $a = json_decode($args, true);
////			$job_id = $a['job_id'];
//            $file_id = $a['file_id'];
//            $newStatus = $a['new_status'];
//
//            updateJobStatus($con, $file_id, $newStatus);
//
//            break;

        // Download file
        case 17:

            $a = json_decode($args, true);
            $file_id = $a['file_id'];
            $currentAccID = $_SESSION['accID']; // to prevent downloading other files belonging to another account
            $res = downloadJob($con, $file_id, $currentAccID); // true if permission granted and hash is generated (return is the hash val) - false if denied

            echo $res;
            $debug = 1;
            break;

        //CLEAR TEMP AUDIO FILE//
        case 33:

            $a = json_decode($args, true);

            $tempAudioFile = $a['job_id'];
            //Paths need to be relative to the calling PHP file
            if (file_exists('../../workingTemp/' . $tempAudioFile)) {
                if (unlink('../../workingTemp/' . $tempAudioFile)) {
                    echo "Temp Audio File Deleted";
                } else {
                    echo "Error deleting temp audio file";
                };
            };

            break;



        //---------------------------------------------------\\
        //-------------------Select Cases 4xx----------------\\
        //---------------------------------------------------\\
        case 40: //check if user exist. returns 1 if exists, 0 if non (int)

            $a = json_decode($args, true);
            $email = strtolower($a["email"]);

            $sql = "SELECT count(*) FROM users WHERE email= ?";
            if ($stmt = mysqli_prepare($con, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $email);

                if (mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);

                    // Check number of rows in the result set
                    if (mysqli_num_rows($result) > 0) {
                        // Fetch result rows as an associative array

                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

                            echo $row['count(*)']; //returns int
                        }


                    }
                } else {
//						echo "<p>No matches found</p>";

                }
            } else {
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);

            }


            // Close statement
            mysqli_stmt_close($stmt);

            break;


        case 42: //Reset User Pwd

            $ip = getenv('HTTP_CLIENT_IP') ?:
                getenv('HTTP_X_FORWARDED_FOR') ?:
                    getenv('HTTP_X_FORWARDED') ?:
                        getenv('HTTP_FORWARDED_FOR') ?:
                            getenv('HTTP_FORWARDED') ?:
                                getenv('REMOTE_ADDR');

            $a = json_decode($args, true);
            $email = strtolower($a["email"]);
            $password = $a["password"];
            $password = password_hash($password, PASSWORD_BCRYPT);
            $token = $a["token"];
            $action = "Password Reset";


            //check
            $sql = "SELECT *, DATE_ADD(time, INTERVAL '30:0' MINUTE_SECOND) as expire FROM `tokens` WHERE identifier=? and email=? AND used=0 and token_type=4 and DATE_ADD(time, INTERVAL '30:0' MINUTE_SECOND) > NOW()";

            //update user password
            $sql2 = "Update users set password=? where email = ?";
            $sql3 = "Update tokens set used=1 where identifier = ?";
            $stmt2 = mysqli_prepare($con, $sql2);
            $stmt3 = mysqli_prepare($con, $sql3);
            if ($stmt = mysqli_prepare($con, $sql)) {
                mysqli_stmt_bind_param($stmt, "ss", $token, $email);


                if (mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);


                    if (mysqli_num_rows($result) > 0) { //exists and valid

                        //expire the token
                        mysqli_stmt_bind_param($stmt3, "s", $token);
                        mysqli_stmt_execute($stmt3);

                        //update new password
                        mysqli_stmt_bind_param($stmt2, "ss", $password, $email);
                        mysqli_stmt_execute($stmt2);

                        //insert log entry
                        $a = array(
                            'email' => $email,
                            'activity' => 'RESET PASSWORD',
                            'actPage' => 'index.php',
                            'actIP' => $ip,
                            'acc_id' => 0 // no account id while not logged in
                        );
                        $b = json_encode($a);
                        insertAuditLogEntry($con, $b);


                        //set message and redirect
                        $_SESSION['msg'] = "You can now login with your new password.";
                        $_SESSION['error'] = false;
                        $_SESSION['src'] = 4;
//						redirect("../../index.php");

                    } else { //token doesn't exist
                        //				$_SESSION['msg'] = "Token Doesn\'t Exist";
                        $_SESSION['msg'] = "Link doesn\'t exist or expired.";
                        $_SESSION['error'] = true;
                        $_SESSION['src'] = 4;
//						redirect("../../index.php");

                    }
                } else {
//						echo "<p>No matches found</p>";

                }
            } else {
//					echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);

            }
            $ip = getIP2();

            $a = array(
                'email' => $_SESSION['uEmail'],
                'activity' => 'Password reset requested',
                'actPage' => 'index.php',
                //'actPage' => header('Location: '.$_SERVER['REQUEST_URI']),   //This isn't working. For now am going to hardcode the page into the function call
                'actIP' => $ip,
                'acc_id' => 0
            );
            $b = json_encode($a);
            insertAuditLogEntry($con, $b);

            // Close statement
            mysqli_stmt_close($stmt);
            mysqli_stmt_close($stmt2);

            break;


        /** Upload Progress Watcher **/
        case 65:

            $suffix = $_REQUEST['suffix'];
            $key = ini_get("session.upload_progress.prefix") . $suffix;
            if (isset($_SESSION[$key])) {
                echo json_encode($_SESSION[$key]);
            } else {
                echo 'starting';
            }

            break;

        /** Password hashing **/
        case 66:

            $a = json_decode($args, true);

            $password = $a['pwd'];
            echo password_hash($password, PASSWORD_BCRYPT);

            break;

        /** Cancel Current Pending Upload If any **/
        case 67:

            $suffix = "job_upload";
            $key = ini_get("session.upload_progress.prefix") . $suffix;
            $_SESSION[$key]["cancel_upload"] = true;

            break;


        // Cases starting from 200 related to reports
        case 200:
            confirmAdminPermission();

            $rptStartDate = $a['startDate'];
            $rptEndDate = $a['endDate'];
            $acc_id = $a['accID'];
            $sql = "SELECT 
       file_id,
		job_id, 
		file_author, 
		file_work_type, 
		file_date_dict, 
		audio_length, 
		file_transcribed_date,
		file_comment
    FROM 
		files
	WHERE 
		file_status  = '3' AND 
		isBillable = '1' AND
		billed = '0' AND 
        acc_id = ? AND
		file_transcribed_date BETWEEN ? AND ?";

            $billRatesObj = getBillRates($con, $acc_id);
            $billRates = json_decode($billRatesObj, true);
            $billRate1 = $billRates['billrate1'];
            $bill_rate1_type = $billRates['bill_rate1_type'];
            if ($stmt = mysqli_prepare($con, $sql)) {
                mysqli_stmt_bind_param($stmt, "iss", $a["accID"], $a['startDate'], $a['endDate']);
                if (mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);
                    $secsTotal = 0;
                    $minsTotal = 0;
                    $html = "";

                    if (mysqli_num_rows($result) > 0) {
                        $num_rows = mysqli_num_rows($result);

                        $htmlHeader = "<h3>Billing Report Date: $rptStartDate to $rptEndDate </h3>";

                        $htmlTblHead = "<table class='report'><thead><tr id='header'><th class='jobnum'>Job Number</th><th class='author'>Author</th><th class='jobtype'>Job Type</th><th class='datedict'>Date Dictated</th><th class='audiolength'>Audio Length</th><th class='transdate'>Transcribed Date</th><th class='comments'>Comments</td></th></tr></thead><tbody>";

                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                            $alSeconds = round($row['audio_length']);
                            $alMinutes = sprintf('%02d:%02d:%02d', ($alSeconds / 3600), ($alSeconds / 60 % 60), $alSeconds % 60);
                            $html .=
                                "<td>" . $row['job_id'] . "</td>" .
                                "<td class='left'>" . $row['file_author'] . "</td>" .
                                "<td class='left'>" . $row['file_work_type'] . "</td>" .
                                "<td class='num'>" . $row['file_date_dict'] . "</td>" .
                                //"<td class='num'>" . $row['audio_length']. "</td>" .
                                "<td class='num'>" . $alMinutes . "</td>" .
                                "<td class='right'>" . $row['file_transcribed_date'] . "</td>" .
                                "<td class='right'>" . $row['file_comment'] . "</td>" .
                                "</tr>";

                            $secsTotal += $row['audio_length'];
                        }
                        // And now the totals:
                        //$htmlfoot = "</tbody><tfoot><tr>Total Minutes:". $minsTotal . "</tr></tfoot></table>";
                        //Convert seconds to minutes for report
                        $seconds = round($secsTotal);
                        $minsTotal = sprintf('%02d:%02d:%02d', ($seconds / 3600), ($seconds / 60 % 60), $seconds % 60);
                        $totalInMins = round(($seconds / 60), 2);
                        $rptGenDate = date("Y-m-d H:i:s");
                        $totalBillableAmount = number_format(round(($totalInMins * $billRate1), 2), 2);
                        $htmltablefoot = "</tbody></table>";
                        $htmlfoot1 = "<p class='mt-3'><b>Report generated on:</b> $rptGenDate <b></br>Total Jobs:</b> $num_rows</br>";
                        $htmlfoot2 = "<b>Total Length (hh:mm:ss):</b> $minsTotal ($totalInMins minutes) with a rate of $$billRate1/min</br>";
                        $htmlfoot3 = "<b>Total Billable Amount is: $$totalBillableAmount</b></p>";
                        $data = html_entity_decode($htmlHeader . $htmlTblHead . $html . $htmltablefoot . $htmlfoot1 . $htmlfoot2 . $htmlfoot3);
                    } else {
                        $data = "No Results Found";
                    }
                    echo generateResponse($data, false);

                }
                $a = array(
                    'email' => $_SESSION['uEmail'],
                    'activity' => 'Client Admin Billing Report Run for period ' . $a['startDate'] . ' to ' . $a['endDate'],
                    'actPage' => 'billing_report.php',
                    'actIP' => getIP2(),
                    'acc_id' => $_SESSION['accID']
                );
                $b = json_encode($a);
                insertAuditLogEntry($con, $b);
            }
            break;

        // Typist Billing Report
        case 201:
            confirmAdminPermission();
            $rptStartDate = $a['startDate'];
            $rptEndDate = $a['endDate'];
            $typist = $a['typist'];
            // We dont' want to include account here as even if they work for multiple accounts they still get paid the same
            //We can get more granular on reports if necessary
            $sql = "SELECT 
			   	file_id,
				job_id, 
				file_author, 
				file_work_type, 
				file_date_dict, 
				audio_length, 
				file_transcribed_date,
				files.acc_id,
                a.bill_rate1_min_pay,
                a.acc_name,
				file_comment
			FROM 
				files
            INNER JOIN accounts a on files.acc_id = a.acc_id 
			WHERE 
				file_status  = '3' AND 
				isBillable = '1' AND
				billed = '0' AND 
				job_transcribed_by = ? AND
				file_transcribed_date BETWEEN ? AND ?			
				";


            if ($stmt = mysqli_prepare($con, $sql)) {
                mysqli_stmt_bind_param($stmt, "sss", $a['typist'], $a['startDate'], $a['endDate']);
                if (mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);
                    $secsTotal = 0;
                    $minsTotal = 0;
                    $html = "";

                    if (mysqli_num_rows($result) > 0) {
                        $num_rows = mysqli_num_rows($result);

                        $htmlHeader = "<h3>Billing Report Date: $rptStartDate to $rptEndDate for $typist</h3>";

                        $htmlTblHead = "<table class='report'>
                                            <thead>
                                            <tr id='header'>
                                                <th class='jobnum'>Job Number</th>
                                                <th class='author'>Author</th>
                                                <th class='jobtype'>Job Type</th>
                                                <th class='datedict'>Date Dictated</th>
                                                <th class='audiolength'>Audio Length</th>
                                                <th class='transdate'>Transcribed Date</th>
                                                <th class='typ_account'>Account</th>
                                                <th class='bill_rate'>Bill Rate</th>
                                                <th class='bill'>Bill</th>
                                                <th class='comments'>Comments
                                                </td></th></tr>
                                            </thead>
                                        <tbody>";

                        $totalPayable = 0; // reset bill
                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                            $typistBillRate1ForCurFile = $row['bill_rate1_min_pay'];
                            $currentFileSeconds = $row['audio_length'];
                            $alSeconds = round($currentFileSeconds);
                            $alMinutes = sprintf('%02d:%02d:%02d', ($alSeconds / 3600), ($alSeconds / 60 % 60), $alSeconds % 60);

                            $currentFileBill = ($currentFileSeconds/60) * $typistBillRate1ForCurFile;
                            $totalPayable += $currentFileBill;

                            $html .=
                                "<td>" . $row['job_id'] . "</td>" .
                                "<td class='left'>" . $row['file_author'] . "</td>" .
                                "<td class='left'>" . $row['file_work_type'] . "</td>" .
                                "<td class='num'>" . $row['file_date_dict'] . "</td>" .
                                //"<td class='num'>" . $row['audio_length']. "</td>" .
                                "<td class='num'>" . $alMinutes . "</td>" .
                                "<td class='right'>" . $row['file_transcribed_date'] . "</td>" .
                                "<td class='right'>" . $row['acc_name'] . "</td>" .
                                "<td class='right'>x" . $row['bill_rate1_min_pay'] . "</td>" .
                                "<td class='right'>$" . number_format(round(($currentFileBill), 2), 2) . "</td>" .
                                "<td class='right'>" . $row['file_comment'] . "</td>" .
                                "</tr>";



                            $secsTotal += $currentFileSeconds;
                        }
                        // And now the totals:
                        //$htmlfoot = "</tbody><tfoot><tr>Total Minutes:". $minsTotal . "</tr></tfoot></table>";
                        //Convert seconds to minutes for report
                        $seconds = round($secsTotal);
                        $minsTotal = sprintf('%02d:%02d:%02d', ($seconds / 3600), ($seconds / 60 % 60), $seconds % 60);
                        $totalInMins = round(($seconds / 60), 2);


                        $rptGenDate = date("Y-m-d H:i:s");
                        $htmltablefoot = "</tbody></table>";
                        $htmlfoot1 = "<br><p class='mt-3'><b>Report generated on:</b> $rptGenDate</br><b>Total Jobs:</b> $num_rows</br>";
                        $htmlfoot2 = "<b>Total Length (hh:mm:ss):</b> $minsTotal ($totalInMins minutes)</br>";
                        $totalPayable = number_format(round(($totalPayable), 2), 2);
                        $htmlfoot3 = "<b>Total Payable for Period: -$$totalPayable</b></p>";
                        $data = html_entity_decode($htmlHeader . $htmlTblHead . $html . $htmltablefoot . $htmlfoot1 . $htmlfoot2 . $htmlfoot3);
                    } else {
                        $data = "No Results Found";
                    }
                    echo generateResponse($data, false);

                }
                $a = array(
                    'email' => $_SESSION['uEmail'],
                    'activity' => 'Typist Billing Report Run for ' . $a['typist'] . ' from ' . $a['startDate'] . ' to ' . $a['endDate'],
                    'actPage' => 'typist_billing.php',
                    'actIP' => getIP2(),
                    'acc_id' => $_SESSION['accID']
                );
                $b = json_encode($a);
                insertAuditLogEntry($con, $b);
            }
            break;

        // get all available typist names for typist_billing selector
        case 202:
            confirmAdminPermission();
            $sql = "SELECT 
                   email,
                    first_name,
                    last_name
                FROM 
                    users
                WHERE 
                    typist  != 0";


            if ($stmt = mysqli_prepare($con, $sql)) {
                if (mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);
                    $html = "";
                    if (mysqli_num_rows($result) > 0) {
                        $num_rows = mysqli_num_rows($result);
                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                            $html .=
                                "<option value=\"" . $row['email'] . "\">" .
                                $row['first_name'] . " " . $row['last_name'] .
                                "</option>";
                        }
                        echo generateResponse($html, false, false);
                    } else {
                        // "No Results Found"
                        $html .= "<option value=\"0\">" .
                            "No Typists Found" .
                            "</option>";
                        $html .= "</select>";
                        echo generateResponse($html, false, true);
                    }
                }
            }

            break;

        // set session variable from transcribe prior to opening mini player
        case 203:
            $_SESSION['popFileID'] = $a['fileID'];
            return true;

            break;

        // get session variable and load (popup/transcribe) (2-way)
        case 204:
            if (!isset($_SESSION['popFileID'])) {
                echo false;
            } else {
                echo $_SESSION['popFileID'];
                unset($_SESSION['popFileID']);
            }

            break;

        /** This updates the job/file details with new updates as Completed/Suspended job **/
        case 205:

            if (isset($_POST)) {
                if (isset($_POST['jobID'])) {
//                    $initials = strtolower(substr($_SESSION['fname'],0,1)) . strtolower(substr($_SESSION['lname'],0,1));
                    $dateTrans = date("Y-m-d H:i:s");

//                    $plainTinyMCEContent = $_POST['report'];

//                    $report = '<b>'.'Job Number: ' .'</b>'. $_POST['jobNo'] .'<br/>';
//                    $report = $report . '<b>'.'Author Name: ' .'</b>'. $_POST['jobAuthorName'].'<br/>';
//                    $report = $report . '<b>'.'Typist Name: ' .'</b>'. $initials .'<br/>';
//                    $report = $report . '<b>'.'Job Type: ' .'</b>'.$_POST['jobType'].'<br/>';
//                    $report = $report . '<b>'.'Job Length: ' .'</b>'.$_POST['jobLengthSecs'].'<br/>';
//                    $report = $report . '<b>'.'Date Dictated: ' .'</b>'.$_POST['jobDateDic'].'<br/>';
//                    $report = $report. '<b>'.'Date Transcribed: ' .'</b>' . $dateTrans .'<br/>';
//                    $report = $report . '<b>'.'Comments: ' .'</b>'.$_POST['jobComments'].'<br/>';

//                    $report = $report.'<br/>';
//                    $report = $report.'<br/>';
//                    $report = $report . $plainTinyMCEContent;


//                    $htmlToRtfConverter = new HtmlToRtf\HtmlToRtf($report);
//                    $convertedRTF = trim($htmlToRtfConverter->getRTF());

                    //DB Insert Code

                    $job_id = $_POST['jobID'];
                    $file_id = $_POST['file_id'];
//                    $audio_length = $_POST['jobLengthSecsRaw'];
                    $audio_elapsed = $_POST['jobElapsedTimeSecs'];
                    $file_status = $_POST['jobStatus'];
                    if ($file_status == 5) {
                        $file_transcribe_date = $dateTrans;
                    } else {
                        $file_transcribe_date = null;
                    }
                    $transcribed_by = $_SESSION['uEmail'];
                    $tmp_name = $_POST['tempFilename'];


                    $sql = "UPDATE FILES SET 
                                last_audio_position=?, 
                                file_status=?, 
								file_transcribed_date=?, 
								job_transcribed_by=?
									WHERE file_id = ?";

                    if ($stmt = mysqli_prepare($con, $sql)) {

                        if (!$stmt->bind_param("iissi", $audio_elapsed, $file_status, $file_transcribe_date, $transcribed_by, $file_id)) {
                            die("Error in bind_param: (" . $con->errno . ") " . $con->error);
                        }
                        $B = mysqli_stmt_execute($stmt);


                        if ($B) {
                            $result = mysqli_stmt_get_result($stmt);

                            // if status is complete -> delete the tmpFile and update DB to empty tmp_name
                            if ($file_status == 5) {
                                deleteTmpFile($con, $file_id, $tmp_name);
                            }

                            echo "Data Updated Successfully!";
                        } else {
                            echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
                            die("Error in excute: (" . $con->errno . ") " . $con->error);
                        }
                    } else {
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);

                    }


                    // Close statement
                    mysqli_stmt_close($stmt);

                    break;
                }
            } else {
                echo "Looks like JobNo is empty";

            }

            break;
    } //switch end

}//if code is set end
else {
//	header('location:../../index.php');
    echo "Bad request"; // DO NOT MODIFY,
    // CRITICAL FOR JOBUPLOAD @job_upload.js TO CHECK FOR FILE UPLOAD EXCEEDING THE LIMIT OF POST_MAX_SIZE
}


// close connection

mysqli_close($con);


//////   <------------- Functions ----------------->  ////////

function updateJobStatus($con, $fileID, $newStatus)
{

    $sql = "UPDATE FILES SET file_status=? WHERE file_id=?";

    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $newStatus, $fileID);

        if (mysqli_stmt_execute($stmt)) {
//			$result = mysqli_stmt_get_result($stmt);
//			echo true;
        } else {
            // couldn't update job status
        }
    } else {
        //	echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
    // Close statement
    mysqli_stmt_close($stmt);


}

function downloadJob($con, $fileID, $accID)
{
    /*-----Get existing data for job --------*/

    $sql3 = "SELECT times_text_downloaded_date, text_downloaded_date FROM files WHERE file_id = ?
    AND acc_id = ?";
    if ($stmt3 = mysqli_prepare($con, $sql3)) {
        mysqli_stmt_bind_param($stmt3, "ii", $fileID, $accID);
        if (mysqli_stmt_execute($stmt3)) {
            $result = mysqli_stmt_get_result($stmt3);
            // Check number of rows in the result set
            if (mysqli_num_rows($result) == 1) {
                // Fetch result rows as an associative array
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
//				$times_downloaded = $row['times_text_downloaded_date'];
//				$times_downloaded++;
//				$text_downloaded_date = $row['text_downloaded_date'];

            } else {
                // TODO PERMISSION DENIED OR FILE DOESN'T EXIST RETURN ERROR MSG
                return false;
            }
        } else {
            //echo "Error executing " .$sql3;
        }
    } else {
        //echo "ERROR: Could not prepare to execute $sql1. " . mysqli_error($con);
        //die( "Error in excute: (" .$con->errno . ") " . $con->error);
    }

    // generate download hash
    $downloadHash = md5(time() . mt_rand(1, 1000000));
    $sql = "INSERT INTO downloads(acc_id, hash, file_id) VALUES(?,?,?)";
    //echo $sql;

    if ($stmt = mysqli_prepare($con, $sql)) {

        $stmt->bind_param("isi", $accID, $downloadHash, $fileID);

        $a = mysqli_stmt_execute($stmt);
        if ($a) {
            return $downloadHash;
        } else {
            //echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
            // todo failed to create download link
            return false;
        }
    }
    return false; // couldn't prepare hash statement at all

}


/* REQUEST CODES */
// INSERT -> 3XX
// Select -> 4XX

//function random_filename($length, $directory = '', $extension = '')
function random_filename($extension = '')
{
    // default to this files directory if empty...
//    $dir = !empty($directory) && is_dir($directory) ? $directory : dirname(__FILE__);

    $dir = "../../workingTemp/";

    $filename = uniqid(time() . "_", true) . $extension;

    while (file_exists($dir . $filename)) {
        $filename = uniqid(time() . "_", true) . $extension;
    }
    return $filename;
}

function checkIfTmpFileExists($tmpName)
{
    $dir = "../../workingTemp/"; // working tmp directory
    return file_exists($dir . $tmpName);
}

function recordJobFileLoaded($con)
{
    //Insert audit detail. Note we will need to look at where we place this to ensure that we don't put it in a place where it may not fire
    // like after a return call or something like that
    //Need to figure out best way to get the acc_id. I think it should be added to the session but what if the user has access to multiple accounts?
    $ip = getIP2();

    $a = array(
        'email' => $_SESSION['uEmail'],
        'activity' => 'Loading audio file into player',
        'actPage' => 'transcribe.php',
        'actIP' => $ip,
        'acc_id' => $_SESSION['accID']
    );
    $b = json_encode($a);
    insertAuditLogEntry($con, $b);
}

function saveJobTmpFileNameToDbRecord($con, $fileID, $newTmpName, $newStatus)
{

    $sql = "UPDATE FILES SET file_status=?, tmp_name = ? WHERE file_id=?";

    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_bind_param($stmt, "isi", $newStatus, $newTmpName, $fileID);

        if (mysqli_stmt_execute($stmt)) {
//			$result = mysqli_stmt_get_result($stmt);
//			echo true;
        } else {
            // couldn't update job status
        }
    } else {
        //	echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
    // Close statement
    mysqli_stmt_close($stmt);
}


function deleteTmpFile($con, $fileID, $tmpName)
{

    $sql = "UPDATE FILES SET tmp_name=null WHERE file_id=?";

    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $fileID);

        if (mysqli_stmt_execute($stmt)) {

            // if removed from db -> delete the file from workingTempDirectory

            $dir = "../../workingTemp/"; // working tmp directory
            unlink($dir . $tmpName);

        } else {
            // couldn't update job status
        }
    } else {
        //	echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
    // Close statement
    mysqli_stmt_close($stmt);
}

function confirmAdminPermission()
{
    if ($_SESSION['role'] != 1) {
        exit();
    } else {
        return true;
    }
}

function getBillRates($con, $acc_id)
{

    $sql = "SELECT bill_rate1,bill_rate1_type,bill_rate1_desc,
	bill_rate2,bill_rate2_type,bill_rate2_TAT,bill_rate2_desc,
	bill_rate3,bill_rate3_type,bill_rate3_TAT,bill_rate3_desc, 
	bill_rate4,bill_rate4_type,bill_rate4_TAT,bill_rate4_desc,
	bill_rate5,bill_rate5_type,bill_rate5_TAT,bill_rate5_desc
	FROM accounts WHERE acc_id  = ?";
    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $acc_id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $billInfo = "";
            if (mysqli_num_rows($result) > 0) {
                $num_rows = mysqli_num_rows($result);
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                    //We're going to send all billing rates even though we're only using one now
                    $billInfo = array(
                        "billrate1" => $row['bill_rate1'],
                        "bill_rate1_type" => $row['bill_rate1_type'],
                        "billrate2" => $row['bill_rate2'],
                        "bill_rate2_type" => $row['bill_rate2_type'],
                        "billrate3" => $row['bill_rate3'],
                        "bill_rate3_type" => $row['bill_rate3_type'],
                        "billrate4" => $row['bill_rate4'],
                        "bill_rate4_type" => $row['bill_rate4_type'],
                        "billrate5" => $row['bill_rate5'],
                        "bill_rate5_type" => $row['bill_rate5_type'],
                    );
                }
                return json_encode($billInfo);
            } else {
                // "No Results Found"
                // Note this should NEVER happen as the billtype1 fields are NOT NULL values
                $billInfo = array(
                    "billrate1" => "0",
                    "bill_rate1_type" => "1"
                );
                return json_encode($billInfo);
            }
        }
    }
}

////////////////////////////////////////
//           Index of                 //
//           Req Codes                //
//------------------------------------//
// 200 -> Get Billing Reports
// 201 -> Get Typist  Reports
// 202 -> Get Typists names for 201
// 203 -> set popFileID to pass to mini player (in sess var)
// 204 -> get popFileID if any and clear the session var
// 205 -> update file status from popup
