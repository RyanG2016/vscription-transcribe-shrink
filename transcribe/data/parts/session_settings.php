<?php
date_default_timezone_set('America/Winnipeg');
session_name('pvs');
session_start(['cookie_lifetime' => 31536000,'cookie_secure' => true,'cookie_httponly' => true]); // 1 Year
$tt = 1;