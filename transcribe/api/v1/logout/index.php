<?php

require '../../../../api/bootstrap.php';

include('../../../data/parts/session_settings.php');

require('../../../data/parts/ping.php');

if(!isset($_SESSION['loggedIn']))
{
    header("HTTP/1.1 200 OK");
    echo json_encode([
        'error' => false,
        'msg' => "Not Logged in."
    ]);
    exit();
}

use Src\Controller\LogoutController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// all of our endpoints start with /api/v1/file
// everything else results in a 404 Not Found
if ($uri[3] !== 'logout') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

// pass the request method and user ID to the PersonController and process the HTTP request:
$controller = new LogoutController($dbConnection, $requestMethod);
$controller->processRequest();