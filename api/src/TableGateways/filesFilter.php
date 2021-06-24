<?php

require_once( __DIR__. '/../../../transcribe/rtf3/src/HtmlToRtf.php');
function parseFilesParams($addWhereClause = false)
{
    $addedEnum = 0;
    $firstMatch = true;
    if ($addWhereClause) {
        $filter = " WHERE ";
    } else {
        $filter = " AND ";
    }
    if (sizeof($_GET) > 0) {
        foreach ($_GET as $key => $value) {

            switch ($key) {
                case "file_id":
                case "job_id":
//                case "acc_id":
                case "file_type":
                case "original_audio_type":
                case "file_name":
                case "orig_filename":
                case "file_tag":
                case "file_author":
                case "file_work_type":
                case "file_comment":
                case "file_speaker_type":
                case "file_date_dict":
                case "file_status":
                case "audio_length":
                case "job_upload_date":
                case "text_downloaded_date":
                case "times_text_downloaded_date":
                case "job_transcribed_by":
                case "file_transcribed_date":
                case "typist_comments":
                case "isBillable":
                case "billed":
                case "user_field_1":
                case "user_field_2":
                case "user_field_3":
                    $addedEnum++;

                    // check if filter is multiple values
                    if (gettype($value) == "array") {
                        if (isset($value["mul"])) {
                            $values = preg_split('/,/', $value["mul"], -1, PREG_SPLIT_NO_EMPTY);
                            $filter .= "(";
                            foreach ($values as $opt) {
                                if (!$firstMatch) {
                                    $filter .= " OR ";
                                }
                                $filter .= "$key = '$opt'";
                                $firstMatch = false;
                            }
                            $filter .= ")";

                        } else {
                            unprocessableFilesFilterResponse();
                        }

                    } else if (gettype($value) == "string") {
                        if (!$firstMatch) {
                            $filter .= " AND ";
                        }
                        $filter .= "$key = '$value'";
                        $firstMatch = false;
                    }
                    break;
            }
        }

        if ($addedEnum > 0) {
            return $filter;
        } else {
            return "";
        }
    } else {
        return "";
    }
}


function parseFileUpdateParams($role, $data = null, $db = null)
{
    $addedEnum = 0;
    $firstMatch = true;
    $filter = "";

    if (sizeof($_POST) > 0) {

        // typist data
        if($role == 3)
        {
            $row = $data[0];
            $dateTrans = date("Y-m-d H:i:s");
            $filter .= " SET file_transcribed_date = '$dateTrans'";
            $filter .= " , job_transcribed_by = '".$_SESSION['uEmail']."'";
            if( isset($_POST["job_document_html"]) && !empty($_POST["job_document_html"]) )
            {
                $tinyMCE = $_POST["job_document_html"];
//                $initials = strtolower(substr($_SESSION['fname'],0,1)) . strtolower(substr($_SESSION['lname'],0,1));

//                $report = '<b>'.'Job Number: ' .'</b>'. $row['job_id'] .'<br/>';
//                $report = $report . '<b>'.'Author Name: ' .'</b>'. $row['file_author'].'<br/>';
//                $report = $report . '<b>'.'Typist Name: ' .'</b>'. $initials .'<br/>';
//                $report = $report . '<b>'.'Job Type: ' .'</b>'.ucfirst($row['file_work_type']).'<br/>';
//                $report = $report . '<b>'.'Job Length: ' .'</b>';
//                if(isset($_POST['audio_length']))
//                {
//                    $report = $report . gmdate("H:i:s", $_POST['audio_length']);
//                }else{
//                    $report = $report .gmdate("H:i:s", $row['audio_length']);
//                }
//                $report .= "<br/>";
//                $report = $report . '<b>'.'Date Dictated: ' .'</b>'.$row['file_date_dict'].'<br/>';
//                $report = $report. '<b>'.'Date Transcribed: ' .'</b>' . $dateTrans .'<br/>';
//                $report = $report . '<b>'.'Comments: ' .'</b>'.$row['typist_comments'].'<br/>';

//                $report = $report.'<br/>';
//                $report = $report.'<br/>';
//                $report = $report . $tinyMCE;

//                $htmlToRtfConverter = new HtmlToRtf\HtmlToRtf($report);
//                $convertedRTF = trim($htmlToRtfConverter->getRTF());
                $filter .= " , job_document_html = '".htmlentities($tinyMCE, ENT_QUOTES)."'"; // added inside the switch case below
//                $filter .= " , job_document_rtf = '".base64_encode($convertedRTF)."'";
            }
        }

        foreach ($_POST as $key => $value) {

            if ($role == 1) // website admin
            {
                switch ($key) {
                    case "file_id":
                    case "job_id":
                    case "file_type":
                    case "original_audio_type":
                    case "file_name":
                    case "orig_filename":
                    case "file_tag":
                    case "file_author":
                    case "file_work_type":
                    case "file_comment":
                    case "file_speaker_type":
                    case "file_date_dict":
                    case "file_status":
                    case "audio_length":
                    case "job_upload_date":
                    case "text_downloaded_date":
                    case "times_text_downloaded_date":
                    case "job_transcribed_by":
                    case "file_transcribed_date":
                    case "typist_comments":
                    case "isBillable":
                    case "billed":
                    case "user_field_1":
                    case "user_field_2":
                    case "user_field_3":

                        // -- sql injection check
                        if (
                            $value != 0 && empty(trim($value)) ||
                            strpos($value, '%') !== FALSE
                        ) {
                            break; // not added to sql query
                        }
                        // =====================

                        $addedEnum++;

                        if (gettype($value) == "string" || gettype($value) == "int") {
                            if ($firstMatch) {
                                $filter .= " SET ";
                                $firstMatch = false;
                            } else {
                                $filter .= ", ";
                            }
                            $fixedVal = $db->quote($value);
                            $filter .= "$key = $fixedVal";
                        }
                        break;
                }
            } else if ($role == 2) // client admin
            {
                switch ($key) {
//                case "file_id":
//                case "job_id":
//                case "file_type":
//                case "original_audio_type":
//                case "file_name":
//                case "orig_filename":
                    case "file_tag":
                    case "file_author":
                    case "file_work_type":
                    case "file_comment":
                    case "file_speaker_type":
                    case "file_date_dict":
//                    case "file_status":
                    case "audio_length":
//                    case "job_upload_date":
//                    case "text_downloaded_date":
//                    case "times_text_downloaded_date":
//                    case "job_transcribed_by":
//                    case "file_transcribed_date":
//                    case "typist_comments":
//                    case "isBillable":
//                    case "billed":
                    case "user_field_1":
                    case "user_field_2":
                    case "user_field_3":

                        // -- sql injection check
                        if (
                            $value != 0 && empty(trim($value)) ||
                            strpos($value, '%') !== FALSE
                        ) {
                            break; // not added to sql query
                        }
                        // =====================

                        $addedEnum++;

                        if (gettype($value) == "string" || gettype($value) == "int") {
                            if ($firstMatch) {
                                $filter .= " SET ";
                                $firstMatch = false;
                            } else {
                                $filter .= ", ";
                            }
                            $fixedVal = $db->quote($value);
                            $filter .= "$key = $fixedVal";
                        }
                        break;
                }
            } else if ($role == 3) //typist
            {
                switch ($key) {
//                case "file_id":
//                case "job_id":
//                case "file_type":
//                case "original_audio_type":
//                case "file_name":
//                case "orig_filename":
//                case "file_tag":
//                    case "file_author":
//                    case "file_comment":
//                    case "file_speaker_type":
//                    case "file_date_dict":
//                    case "job_upload_date":
//                    case "text_downloaded_date":
//                    case "times_text_downloaded_date":
//                    case "isBillable":
//                    case "billed":
//                    case "user_field_1":
//                    case "user_field_2":
//                    case "user_field_3":
                    case "file_status":
                    case "audio_length":
                    case "last_audio_position":
//                    case "job_document_html":
                    case "job_document_rtf":
                    case "job_transcribed_by":
                    case "file_transcribed_date":
                    case "file_work_type":
                    case "typist_comments":

                        // -- sql injection check
                        if (
                            $value != 0 && empty(trim($value)) ||
                            strpos($value, '%') !== FALSE
                        ) {
                            break; // not added to sql query
                        }
                        // =====================

                        $addedEnum++;

                        if (gettype($value) == "string" || gettype($value) == "int") {
//                            if ($firstMatch) {
//                                $filter .= " SET ";
//                                $firstMatch = false;
//                            } else {
                                $filter .= ", ";
//                            }
                            $fixedVal = $db->quote($value);
                            $filter .= "$key = $fixedVal";
                        }
                        break;
                }
            }
            /*else{
                Do nothing
            }*/
        }

        if ($addedEnum > 0) {
            return $filter;
        } else {
            return "";
        }
    } else {
        return "";
    }
}

function unprocessableFilesFilterResponse()
{
    header('HTTP/1.1 422 Unprocessable Filter');
    echo json_encode([
        'error' => true,
        'msg' => 'Invalid Update Parameters'
    ]);
    exit();
}