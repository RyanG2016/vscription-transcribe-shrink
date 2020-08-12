<?php


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


function parseFileUpdateParams($role)
{
    $addedEnum = 0;
    $firstMatch = true;
    $filter = "";

    if (sizeof($_POST) > 0) {
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
                            $filter .= "$key = '$value'";
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
                            $filter .= "$key = '$value'";
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
//                    case "file_status":
//                    case "audio_length":
//                    case "job_upload_date":
//                    case "text_downloaded_date":
//                    case "times_text_downloaded_date":
//                    case "job_transcribed_by":
//                    case "file_transcribed_date":
//                    case "isBillable":
//                    case "billed":
//                    case "user_field_1":
//                    case "user_field_2":
//                    case "user_field_3":
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
                            if ($firstMatch) {
                                $filter .= " SET ";
                                $firstMatch = false;
                            } else {
                                $filter .= ", ";
                            }
                            $filter .= "$key = '$value'";
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