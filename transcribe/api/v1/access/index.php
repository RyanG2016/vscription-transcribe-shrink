<?php

require '../../../../api/bootstrap.php';

include_once('../../../data/parts/session_settings.php');

require('../../../data/parts/ping.php');
require "../parts/checkAuth.php"; // <-- checking for basic auth before request & if the user is already logged in


use Src\Controller\accessController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
//header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Allow-Methods: POST, GET, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// all of our endpoints start with /api/v1/speakerTypes
// everything else results in a 404 Not Found
if ($uri[3] !== 'access') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

// the speakerType id is, of course, optional and must be a number:
$accessId = null;
if (isset($uri[4])) {
    $accessId = $uri[4];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];
//if ($requestMethod == "POST") { // aka inserting new access
//}
if (!isset($_SESSION['role']) || $_SESSION['role'] != "1") {
    if(!isset($_REQUEST['out']))
    {
        // System Administrator ONLY can add new types
        header("HTTP/1.1 401 ACCESS DENIED");
        exit();
    }else{ // marginal access to get current logged in user allowed access permissions
        $controller = new accessController($dbConnection, $requestMethod, $accessId);
        $controller->getPublicAccessForCurrentLoggedUser();
        exit();
    }
}else{ // sys admin, access allowed
    if(isset($_REQUEST['out'])){
        $controller = new accessController($dbConnection, $requestMethod, $accessId);
        $controller->getPublicAccessForCurrentLoggedUser();
    }else{
        $controller = new accessController($dbConnection, $requestMethod, $accessId);
        $controller->processRequest();
    }
}