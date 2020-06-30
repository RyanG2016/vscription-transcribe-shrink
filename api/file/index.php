<?php

require '../bootstrap.php';

use Src\Controller\FileController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// all of our endpoints start with /person
// everything else results in a 404 Not Found
if ($uri[2] !== 'file') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

// the file id is, of course, optional and must be a number:
$fileId = null;
if (isset($uri[3])) {
    $fileId = (int)$uri[3];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

// pass the request method and user ID to the PersonController and process the HTTP request:
$controller = new FileController($dbConnection, $requestMethod, $fileId);
$controller->processRequest();