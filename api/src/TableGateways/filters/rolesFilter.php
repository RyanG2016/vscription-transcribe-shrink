<?php


function parseRolesParams($addWhereClause = false){
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
                case "role_name":
                case "role_desc":
                case "role_id":
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
                            unprocessableRolesFilterResponse();
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

function unprocessableRolesFilterResponse()
{
    header('HTTP/1.1 422 Unprocessable Filter');
    echo json_encode([
        'error' => true,
        'msg' => 'Invalid Filter Input'
    ]);
    exit();
}