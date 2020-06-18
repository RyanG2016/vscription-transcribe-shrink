<?php
//session_start();
include('session_settings.php');

require('ping.php');

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