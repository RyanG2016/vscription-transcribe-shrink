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

/**
 * Generates a random token of 78 characters length
 * used for verification emails
 * @return string generated token
 */
function genToken()
{
    $length = 78;
    try {
        return bin2hex(random_bytes($length));
    } catch (Exception $e) {
        return false;
    }
}

// * @param $mailType int reset-password: 0 | sign-up -> 1 | password reset -> 4 | verify email: 5
/**
 * @param $mailType int reset-password 0 | verify email: 5 | document-complete: 10 | job added: 15
 * @param $user_email string user email address
 * @param string $token user generated token to verify or update something
 * @return bool true -> OK | false -> failed to send email
 */
function sendEmail($mailType, $user_email, $token = "")
{
    include_once(__DIR__ . '/../../../transcribe/data/parts/constants.php');
    include_once(__DIR__ . "/../../../mail/mail_init.php");
    global $cbaselink;
    global $link;
    global $mail;
    global $emHTML;
    global $emPlain;
    global $email;
    $email = $user_email;

    $link = "$cbaselink/verify.php?token=$token";
    try {
        switch ($mailType) {
            case 0:
                include(__DIR__ . '/../../../mail/templates/reset_pwd.php');
                $sbj = "Password Reset";
                break;

            case 5:
                include(__DIR__ . '/../../../mail/templates/verify_your_email.php');
                $sbj = "Email Verification";
//                $mail->addCC("sales@vtexvsi.com");
                break;

            case 10:
                include(__DIR__ . '/../../../mail/templates/document_complete.php');
                $sbj = "New Document(s) Ready for Download";
//                $mail->addCC("sales@vtexvsi.com");
                break;

            case 15:
                include(__DIR__ . '/../../../mail/templates/job_ready_for_typing.php');
                $sbj = "New Job(s) Ready for Typing";
//                $mail->addCC("sales@vtexvsi.com");
                break;

            default:
                $sbj = "vScription Transcribe Pro";
                break;
        }

        $mail->addAddress($email); //recipient
        $mail->Subject = $sbj;
        $mail->Body = $emHTML;
        $mail->AltBody = $emPlain;

        $result = $mail->send();
        return $result;
    } catch (Exception $e) {
//        $_SESSION['error'] = true;  //error=1 in session
//        $_SESSION['msg'] = "Email couldn\'t be sent at this time please try again. {$mail->ErrorInfo}";
        return false;
    }
} // send Email end