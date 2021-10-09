<?php

require '../../../../api/bootstrap.php';

include_once('../../../data/parts/session_settings.php');

require('../../../data/parts/ping.php');

/*if(!isset($_SESSION['loggedIn']))
{
    header("HTTP/1.1 401 Unauthorized");
    header("Content-Type: application/json; charset=UTF-8");
    
    echo json_encode([
        'error' => false,
        'msg' => "Not Logged in.",
        'logged_in' => false
    ]);
    exit();
}*/

use Src\Controller\sessionController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// all of our endpoints start with /api/v1/session
// everything else results in a 404 Not Found
if ($uri[3] !== 'sessions') {
    header("HTTP/1.1 404 Not Found");
    exit();
}
$uri = array_slice($uri, 4);

$requestMethod = $_SERVER["REQUEST_METHOD"];

//if (!isset($_SESSION['role']) || $_SESSION['role'] != \Src\Enums\ROLES::SYSTEM_ADMINISTRATOR) {
/*if (!isset($_SESSION['role'])) {
    // Only logged in users can access API endpoint
    header("HTTP/1.1 401 ACCESS DENIED");
    exit();
}*/

// pass the request method and user ID to the PersonController and process the HTTP request:
$controller = new sessionController($dbConnection, $requestMethod, $uri);
$controller->processRequest();