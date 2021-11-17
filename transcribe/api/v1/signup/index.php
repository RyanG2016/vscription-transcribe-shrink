<?php

/*
 * base url /signup
 * options:
 *      POST /signup/resend -> resend verification email to user
 * */

require '../../../../api/bootstrap.php';

include_once('../../../data/parts/session_settings.php');

require('../../../data/parts/ping.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if(isset($_SESSION['loggedIn']))
{
    header("HTTP/1.1 200 OK");
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        'error' => false,
        'msg' => "Already LoggedIn"
    ]);
    exit();
}

use Src\Controller\SignupController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
//header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// all of our endpoints start with /api/v1/file
// everything else results in a 404 Not Found
if ($uri[3] !== 'signup') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$option = null;
if (isset($uri[4])) {
    $option = $uri[4];
}
$rawURI = array_slice($uri, 4, count($uri));

// pass the request method and user ID to the PersonController and process the HTTP request:
$controller = new SignupController($dbConnection, $requestMethod, $option, $rawURI);
$controller->processRequest();