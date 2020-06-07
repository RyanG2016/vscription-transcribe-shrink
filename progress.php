<?php
session_start(['cookie_lifetime' => 86400,'cookie_secure' => true,'cookie_httponly' => true]);
$key = ini_get("session.upload_progress.prefix") . "upload_progress";
echo json_encode($_SESSION[$key]);