<?php

include('session_settings.php');
include('zohoASAP.php');
//include('inspectlet.php');
require('ping.php');

if(!isset($_SESSION['loggedIn']))
{
	header('location:logout.php');
	exit();
}
//else if((!isset($_SESSION['landed']) || $_SESSION['landed'] == false ) && $vtex_page != \Src\Enums\INTERNAL_PAGES::LANDING)
else if((!isset($_SESSION['landed']) || $_SESSION['landed'] == false ) && $vtex_page != \Src\Enums\INTERNAL_PAGES::SETTINGS)
{
    header('location:settings.php');
    exit();
}
if(isset($_SESSION['counter']))
{
	unset($_SESSION['counter']);
}

/*
else {
	
}*/