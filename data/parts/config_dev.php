<?php
//date_default_timezone_set('America/Winnipeg');
date_default_timezone_set('Africa/Cairo');
define('DB_SERVER','localhost');
define('DB_USER','admin');
define('DB_PASS' ,'admin');
define('DB_NAME', 'vtexvsi_transcribe');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //todo disable in production
$con = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
mysqli_set_charset($con,"utf8");
// Check connection
if (mysqli_connect_errno())
{
 echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

function redirect($url) {
    ob_start();
    header('Location: '.$url);
    ob_end_flush();
    die();
}
?>
