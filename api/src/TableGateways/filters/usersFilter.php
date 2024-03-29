<?php


function parseUserParams($addWhereClause = false){
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
                case "id":
                case "first_name":
                case "last_name":
                case "email":
                case "city":
                case "state":
                case "country":
                case "address":
                case "zipcode":
                case "registeration_date":
                case "last_ip_address":
                case "account_status":
                case "last_login":
                case "newsletter":
                case "email_notification":
                case "account":
                case "enabled":
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
                                $filter .= "users.$key = '$opt'";
                                $firstMatch = false;
                            }
                            $filter .= ")";

                        }else{
                            unprocessableUserFilterResponse();
                        }

                    }else if(gettype($value) == "string"){
                        if(!$firstMatch) {$filter .= " AND ";}
                        $filter .= "users.$key = '$value'";
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

function sqlInjectionUserCheckPassed($array){
    // Sql Injection Check
    foreach ($array as $key => $value)
    {
        switch($key){

//            case "id":
            case "first_name":
            case "last_name":
            case "email":
            case "country":
            case "state":
            case "city":
            case "address":
            case "zipcode":
            case "last_ip_address":
            case "account_status":
            case "last_login":
            case "trials":
            case "unlock_time":
            case "shortcuts":
            case "dictionary":
            case "email_notification":
            case "account":
                if (
                    strpos($value, '%') !== FALSE
//                    ||strpos($value, '_')
                ) {
                    return false;
                }

                break;
            case "enabled":
            case "newsletter":
                if (
                    $value != 0 && empty(trim($value)) ||
                    strpos($value, '%') !== FALSE ||
                    strpos($value, '_') ||
                    !is_numeric($value)
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

function sqlInjectionCreateCheckPassed($array){
    // Sql Injection Check
    foreach ($array as $key => $value)
    {
        switch($key){
//            case "shortcuts":
//            case "dictionary":
//            case "email_notification":
//            case "account":
//            case "last_ip_address":
//            case "plan_id":
//            case "account_status":
//            case "trials":
//            case "unlock_time":
//            case "id":
            case "first_name":
            case "last_name":
            case "email":
            case "last_login":
                if (
                    empty(trim($value)) ||
                    strpos($value, '%') !== FALSE
                ) {
                    return false;
                }

                break;

            case "city":
            case "state":
            case "address":
            case "country":
            case "zipcode":

            if (
                    strpos($value, '%') !== FALSE
                ) {
                    return false;
                }

                break;

//            case "country_id":
//            case "state_id":
            case "enabled":
            case "newsletter":
                if (
                    $value != 0 && empty(trim($value)) ||
                    strpos($value, '%') !== FALSE ||
                    strpos($value, '_') ||
                    !is_numeric($value)
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

function sqlInjectionUpdateDefAccessCheckPassed($array){
    // Sql Injection Check
    foreach ($array as $key => $value)
    {
        switch($key){

            case "acc_id":
            case "acc_role":
            case "uid":
                if (
                    $value != 0 && empty(trim($value)) ||
                    strpos($value, '%') !== FALSE ||
                    strpos($value, '_') ||
                    !is_numeric($value)
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

function unprocessableUserFilterResponse()
{
    header('HTTP/1.1 422 Unprocessable Filter');
    echo json_encode([
        'error' => true,
        'msg' => 'Invalid Filter Input'
    ]);
    exit();
}