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
                case "access_id":
                case "acc_id":
                case "uid":
                case "username":
                case "acc_role":
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
                                $filter .= "access.$key = '$opt'";
                                $firstMatch = false;
                            }
                            $filter .= ")";

                        }else{
                            unprocessableFilterResponse();
                        }

                    }else if(gettype($value) == "string"){
                        if(!$firstMatch) {$filter .= " AND ";}
                        $filter .= "access.$key = '$value'";
                        $firstMatch = false;
                    }
                    break;


                case "acc_name":
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
                                $filter .= "a.$key = '$opt'";
                                $firstMatch = false;
                            }
                            $filter .= ")";

                        }else{
                            unprocessableFilterResponse();
                        }

                    }else if(gettype($value) == "string"){
                        if(!$firstMatch) {$filter .= " AND ";}
                        $filter .= "a.$key = '$value'";
                        $firstMatch = false;
                    }
                    break;


                case "role_name":
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
                                $filter .= "r.$key = '$opt'";
                                $firstMatch = false;
                            }
                            $filter .= ")";

                        }else{
                            unprocessableFilterResponse();
                        }

                    }else if(gettype($value) == "string"){
                        if(!$firstMatch) {$filter .= " AND ";}
                        $filter .= "r.$key = '$value'";
                        $firstMatch = false;
                    }
                    break;

                case "email":
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
                                $filter .= "u.$key = '$opt'";
                                $firstMatch = false;
                            }
                            $filter .= ")";

                        }else{
                            unprocessableFilterResponse();
                        }

                    }else if(gettype($value) == "string"){
                        if(!$firstMatch) {$filter .= " AND ";}
                        $filter .= "u.$key = '$value'";
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

function sqlInjectionCheckPassed($array) {
    // Sql Injection Check
    foreach ($array as $key => $value)
    {
        switch($key){

            case "username":
                if (
                    empty(trim($value)) ||
                    strpos($value, '%') !== FALSE
//                    ||strpos($value, '_')
                ) {
                    return false;
                }

                break;
            case "acc_role":
            case "access_id":
            case "acc_id":
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

function unprocessableFilterResponse()
{
    header('HTTP/1.1 422 Unprocessable Filter');
    echo json_encode([
        'error' => true,
        'msg' => 'Invalid Filter Input'
    ]);
    exit();
}