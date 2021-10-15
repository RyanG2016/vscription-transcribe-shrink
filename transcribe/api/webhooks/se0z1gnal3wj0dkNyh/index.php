<?php

/*
 * Webhook endpoint for REV.AI notifications
 * options:
 *      POST /<random string>/incoming        -> rev.ai incoming webhook notification endpoint
 * */

use Src\Controller\SRQueueController;

require '../../../../api/bootstrap.php';

include_once('../../../data/parts/session_settings.php');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);
//    header("HTTP/1.1 404 Not Found");
//    exit();

// <opt> url suffix page
$suffix = null;
if (isset($uri[4])) {
    $suffix = $uri[4];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

// pass the request method and user ID to the PersonController and process the HTTP request:
$controller = new SRQueueController($dbConnection, $requestMethod, $suffix);
$controller->processRequest();

/*if(isset($_SESSION["role"]) && $_SESSION["role"] != 1)
{
    $controller->processPublicRequest();
}else{

}*/