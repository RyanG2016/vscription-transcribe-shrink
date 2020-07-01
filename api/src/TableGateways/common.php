<?php
function getIP()
{
    return getenv('HTTP_CLIENT_IP')?:
        getenv('HTTP_X_FORWARDED_FOR')?:
            getenv('HTTP_X_FORWARDED')?:
                getenv('HTTP_FORWARDED_FOR')?:
                    getenv('HTTP_FORWARDED')?:
                        getenv('REMOTE_ADDR');
}

function encodeStr($str) // encodes string entities as (') to show correctly in html
{
    return htmlentities($str, ENT_QUOTES);
}

function generateResponse($data, $error, $empty=false)
{
    $a = Array(
        'data' => $data,
        'no_result' => $empty,
        'error' => $error
    );
    return json_encode($a);
}