<?php

require '../../../../api/bootstrap.php';

include('../../../data/parts/session_settings.php');

require('../../../data/parts/ping.php');
use Src\Controller\LoginController;
if(!isset($_SESSION['loggedIn']))
{
    // check for basic auth
    if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER["PHP_AUTH_PW"]))
    {
        // Auth Headers Available -> try to login
        $loginController = new LoginController($dbConnection, $_SERVER["REQUEST_METHOD"]);
        $result = $loginController->processSilentRequest();

        // if login OK the script will resume..
        $jsonResponse = json_decode($result["body"], true);
        if ($jsonResponse["error"] != false) { // login failed
            header($result['status_code_header']);
            echo $result['body'];
            exit();
        }
    }
    else{ // no basic auth sent
        header("HTTP/1.1 401 ACCESS DENIED");
        $body = json_encode([
            'error' => true,
            'msg' => "You don't have permission to access this web page, make sure you are logged in."
        ]);
        echo $body;
        exit();
    }
}
if(isset($_SESSION['counter']))
{
    unset($_SESSION['counter']);
}


use Src\Controller\FileController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// all of our endpoints start with /api/v1/file
// everything else results in a 404 Not Found
if ($uri[3] !== 'file') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

// the file id is, of course, optional and must be a number:
$fileId = null;
if (isset($uri[4])) {
    $fileId = (int)$uri[4];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

// pass the request method and user ID to the PersonController and process the HTTP request:
$controller = new FileController($dbConnection, $requestMethod, $fileId);
$controller->processRequest();