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
                case "acc_id":
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
                    $addedEnum ++;
                    if(!$firstMatch) {$filter .= " AND ";}
                    $filter .= "$key = '$value'";
                    $firstMatch = false;
                    break;
            }
        }

        if($addedEnum > 0) {return $filter;} else {return "";}
    }
    else{
        return "";
    }
}
