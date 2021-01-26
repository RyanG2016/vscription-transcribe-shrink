<?php

require '../../../../api/bootstrap.php';

include('../../../data/parts/session_settings.php');

require('../../../data/parts/ping.php');
require "../parts/checkAuth.php"; // <-- checking for basic auth before request & if the user is already logged in
if(isset($_SESSION['counter']))
{
    unset($_SESSION['counter']);
}

use Src\Controller\AccountController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
//header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Allow-Methods: POST,GET,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// all of our endpoints start with /api/v1/accounts
// everything else results in a 404 Not Found
if ($uri[3] !== 'accounts') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

// the account id is, of course, optional and must be a number:
$accountId = null;
if (isset($uri[4])) {
    $accountId = (int)$uri[4];
}
$uri = array_slice($uri, 4);

$requestMethod = $_SERVER["REQUEST_METHOD"];

// pass the request method and user ID to the PersonController and process the HTTP request:
$controller = new AccountController($dbConnection, $requestMethod, $accountId, $uri);
if(isset($_REQUEST["out"]) || $_SESSION['role'] != 1)
{
    $controller->processPublicRequest();
}else{
    $controller->processRequest();
}