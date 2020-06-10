<?php
//session_start();
session_start(['cookie_lifetime' => 86400,'cookie_secure' => true,'cookie_httponly' => true]);

include('data/parts/ping.php');

if(!isset($_SESSION['loggedIn']))
{
	header('location:logout.php');
	exit();
}
if(isset($_SESSION['counter']))
{
	unset($_SESSION['counter']);
}

/*
else {
	
}*/