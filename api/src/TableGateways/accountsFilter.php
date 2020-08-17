<?php


function parseAccountParams($addWhereClause = false){
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
                case "acc_id":
                case "enabled":
                case "billable":
                case "acc_name":
                case "acc_retention_time":
                case "acc_creation_date":
                case "bill_rate1":
                case "bill_rate1_type":
                case "bill_rate1_TAT":
                case "bill_rate1_desc":
                case "bill_rate1_min_pay":
                case "bill_rate2":
                case "bill_rate2_type":
                case "bill_rate2_TAT":
                case "bill_rate2_desc":
                case "bill_rate2_min_pay":
                case "bill_rate3":
                case "bill_rate3_type":
                case "bill_rate3_TAT":
                case "bill_rate3_desc":
                case "bill_rate3_min_pay":
                case "bill_rate4":
                case "bill_rate4_type":
                case "bill_rate4_TAT":
                case "bill_rate4_desc":
                case "bill_rate4_min_pay":
                case "bill_rate5":
                case "bill_rate5_type":
                case "bill_rate5_TAT":
                case "bill_rate5_desc":
                case "bill_rate5_min_pay":
                case "lifetime_minutes":
                case "work_types":
                case "next_job_tally":
                case "act_log_retention_time":
                case "job_prefix":
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
                            unprocessableAccFilterResponse();
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

function accountSqlInjectionCheckPassed($array){
    // Sql Injection Check
    foreach ($array as $key => $value)
    {
        switch($key){

            case "acc_id":
            case "enabled":
            case "billable":
            case "acc_name":
            case "acc_retention_time":
            case "acc_creation_date":
            case "bill_rate1_type":
            case "bill_rate1_desc":
            case "bill_rate2_type":
            case "bill_rate2_desc":
            case "bill_rate3_type":
            case "bill_rate3_desc":
            case "bill_rate4_type":
            case "bill_rate4_desc":
            case "bill_rate5_type":
            case "bill_rate5_desc":
            case "lifetime_minutes":
            case "work_types":
            case "next_job_tally":
            case "act_log_retention_time":
            case "job_prefix":
            case "bill_rate1":
            case "bill_rate1_TAT":
            case "bill_rate1_min_pay":
            case "bill_rate2":
            case "bill_rate2_TAT":
            case "bill_rate2_min_pay":
            case "bill_rate3":
            case "bill_rate3_TAT":
            case "bill_rate3_min_pay":
            case "bill_rate4":
            case "bill_rate4_TAT":
            case "bill_rate4_min_pay":
            case "bill_rate5":
            case "bill_rate5_TAT":
            case "bill_rate5_min_pay":
                if (
                    $value != 0 && empty(trim($value)) ||
                    strpos($value, '%') !== FALSE
//                    ||strpos($value, '_')
                ) {
                    return false;
                }

                break;

            default: // prevent any other parameters
                return false;
                break;
        }
    }
    return true;
}

function unprocessableAccFilterResponse()
{
    header('HTTP/1.1 422 Unprocessable Filter');
    echo json_encode([
        'error' => true,
        'msg' => 'Invalid Filter Input'
    ]);
    exit();
}