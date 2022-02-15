<?php

/*
 * base url /payments
 * options:
 *      POST /payments/create          -> create payment and send receipt email to $POST[email]
 * */

require '../../../../api/bootstrap.php';

include_once('../../../data/parts/session_settings.php');

require('../../../data/parts/ping.php');
require "../parts/checkAuth.php"; // <-- checking for basic auth before request & if the user is already logged in


use Src\Controller\PaymentController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
//header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Allow-Methods: POST,GET,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

error_log("The request URI is:" . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),0);

// all of our endpoints start with /api/v1/payments
// everything else results in a 404 Not Found
if ($uri[3] !== 'payments') {
    header("HTTP/1.1 404 Not Found");
    exit();
}
$uri = array_slice($uri, 4);

$requestMethod = $_SERVER["REQUEST_METHOD"];

// pass the request method and user ID to the PersonController and process the HTTP request:
$controller = new PaymentController($dbConnection, $requestMethod, $uri);
$controller->processRequest();