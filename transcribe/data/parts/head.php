<?php

include('session_settings.php');

require('ping.php');

if(!isset($_SESSION['loggedIn']))
{
	header('location:logout.php');
	exit();
}
else if(!isset($_SESSION['landed']) || $_SESSION['landed'] == false)
{
    header('location:landing.php');
    exit();
}
if(isset($_SESSION['counter']))
{
	unset($_SESSION['counter']);
}

/*
else {
	
}*/