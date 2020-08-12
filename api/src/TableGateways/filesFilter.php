<?php


function parseParams($addWhereClause = false){
    $addedEnum = 0;
    $firstMatch = true;
    if($addWhereClause){
        $filter = " WHERE ";
    }else{
        $filter = " AND ";
    }
    if(sizeof($_GET) > 0)
    {
        foreach ($_GET as  $key=>$value){

            switch ($key)
            {
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
                    $addedEnum ++;

                    // check if filter is multiple values
                    if(gettype($value) == "array")
                    {
                        if(isset($value["mul"]))
                        {
                            $values = preg_split('/,/', $value["mul"], -1, PREG_SPLIT_NO_EMPTY);
                            $filter .= "(";
                            foreach ($values as $opt)
                            {
                                if(!$firstMatch) {$filter .= " OR ";}
                                $filter .= "$key = '$opt'";
                                $firstMatch = false;
                            }
                            $filter .= ")";

                        }else{
                            unprocessableFilterResponse();
                        }

                    }else if(gettype($value) == "string"){
                        if(!$firstMatch) {$filter .= " AND ";}
                        $filter .= "$key = '$value'";
                        $firstMatch = false;
                    }
                    break;
            }
        }

        if($addedEnum > 0) {return $filter;} else {return "";}
    }
    else{
        return "";
    }
}

function unprocessableFilterResponse()
{
    header('HTTP/1.1 422 Unprocessable Filter');
    echo json_encode([
        'error' => true,
        'msg' => 'Invalid Filter Input'
    ]);
    exit();
}