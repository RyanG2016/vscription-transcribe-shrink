<?php

require '../../../../api/bootstrap.php';

include('../../../data/parts/common_functions.php');

//require('../../../data/parts/ping.php');
//require "../parts/checkAuth.php"; // <-- checking for basic auth before request & if the user is already logged in
if(isset($_SESSION['counter']))
{
    unset($_SESSION['counter']);
}

// todo check if not local host - exit
$currIP = getIP();
if($currIP != "127.0.0.1" && $currIP != "localhost")
{
    header("HTTP/1.1 404 NOT FOUND");
    exit();
}

use Src\Controller\conversionController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
//header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Allow-Methods: POST,GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);
// all of our endpoints start with /api/v1/roles
// everything else results in a 404 Not Found
if ($uri[3] !== 'conversions') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

// the role id is, of course, optional and must be a number:
$roleId = null;
if (isset($uri[4])) {
    $roleId = (int)$uri[4];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

// pass the request method and user ID to the PersonController and process the HTTP request:
$controller = new conversionController($dbConnection, $requestMethod, $roleId);
$controller->processRequest();