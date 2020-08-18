<?php
include_once __DIR__. "/../../../transcribe/data/parts/constants.php";
function getIP()
{
    return getenv('HTTP_CLIENT_IP') ?:
        getenv('HTTP_X_FORWARDED_FOR') ?:
            getenv('HTTP_X_FORWARDED') ?:
                getenv('HTTP_FORWARDED_FOR') ?:
                    getenv('HTTP_FORWARDED') ?:
                        getenv('REMOTE_ADDR');
}

function encodeStr($str) // encodes string entities as (') to show correctly in html
{
    return htmlentities($str, ENT_QUOTES);
}

function generateResponse($data, $error, $empty = false)
{
    $a = array(
        'data' => $data,
        'no_result' => $empty,
        'error' => $error
    );
    return json_encode($a);
}

function generateApiResponse($msg = false, $error = false, $data = false)
{
    $a = array(
        'data' => $data,
        'msg' => $msg,
        'error' => $error
    );
    if (!$data) {
        $a = array(
            'msg' => $msg,
            'error' => $error
        );
    }
    return json_encode($a);
}

// == codes == //
// 3xx -> sign-up codes
// 301 -> user already exists
function generateApiHeaderResponse($msg = false, $error = false, $data = false, $code = 0)
{
    $a = array(
        'data' => $data,
        'msg' => $msg,
        'error' => $error,
        'code' => $code
    );

    if (!$data) {
        $a = array(
            'msg' => $msg,
            'error' => $error,
            'code' => $code
        );
    }

    $response['status_code_header'] = $error ? "HTTP/1.1 422 Unprocessable Entity" : "HTTP/1.1 200 OK";
    $response['body'] = json_encode($a);
    return $response;
}

function vtexCurlGet($url)
{

    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $url,
        // getenv("BASE_LINK").'/api/v1/conversions'
        // CURLOPT_URL => 'https://pro.vtex/api/v1/conversions',
        CURLOPT_USERAGENT => 'Files API',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 2,
        //    CURLOPT_COOKIESESSION => false
        //    CURLOPT_COOKIE => $strCookie,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        )
    ]);

    // todo disable for production the 2 lines below
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    // Close request to clear up some resources
    curl_close($curl);

    $jsonArrayResponse = json_decode($resp, true);
//    print_r($jsonArrayResponse);
    return $jsonArrayResponse["error"];
}


function vtexCurlPost($url, $postRequestArray = null)
{

    /*$postRequest = array(
        'firstFieldData' => 'foo',
        'secondFieldData' => 'bar'
    );*/

    $curl = curl_init();

    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $url,
        CURLOPT_POSTFIELDS => $postRequestArray,
        // getenv("BASE_LINK").'/api/v1/conversions'
        // CURLOPT_URL => 'https://pro.vtex/api/v1/conversions',
        CURLOPT_USERAGENT => 'Files API',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 2,
        //    CURLOPT_COOKIESESSION => false
        //    CURLOPT_COOKIE => $strCookie,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        )
    ]);

    // todo disable for production the 2 lines below
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    // Close request to clear up some resources
    curl_close($curl);

    $jsonArrayResponse = json_decode($resp, true);

    /*
     * Array
        (
            [msg] => Convert Record Created
            [error] =>
        )
    */

    return $jsonArrayResponse["error"];
//    print_r($jsonArrayResponse);
}

function random_filename($extension = '')
{
    // default to this files directory if empty...
//    $dir = !empty($directory) && is_dir($directory) ? $directory : dirname(__FILE__);

    $dir = __DIR__ . "/../../../transcribe/workingTemp/";

    $filename = uniqid(time() . "_", true) . $extension;

    while (file_exists($dir . $filename)) {
        $filename = uniqid(time() . "_", true) . $extension;
    }
    return $filename;
}