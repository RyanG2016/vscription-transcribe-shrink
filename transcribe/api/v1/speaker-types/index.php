<?php

require '../../../../api/bootstrap.php';

include('../../../data/parts/session_settings.php');

require('../../../data/parts/ping.php');
require "../parts/checkAuth.php"; // <-- checking for basic auth before request & if the user is already logged in
if(isset($_SESSION['counter']))
{
    unset($_SESSION['counter']);
}

use Src\Controller\SpeakerTypeController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
//header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Allow-Methods: POST,GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// all of our endpoints start with /api/v1/speakerTypes
// everything else results in a 404 Not Found
if ($uri[3] !== 'speaker-types') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

// the speakerType id is, of course, optional and must be a number:
$speakerTypeId = null;
if (isset($uri[4])) {
    $speakerTypeId = (int)$uri[4];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod == "POST") { // aka inserting new speaker type
    if ($_SESSION['role'] != "1") {
    // System Administrator ONLY can add new types
        header("HTTP/1.1 401 ACCESS DENIED");
        exit();
    }
}

// pass the request method and user ID to the PersonController and process the HTTP request:
$controller = new SpeakerTypeController($dbConnection, $requestMethod, $speakerTypeId);
$controller->processRequest();