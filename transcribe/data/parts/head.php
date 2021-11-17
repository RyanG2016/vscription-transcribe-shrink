<?php

include_once('session_settings.php');
include('zohoASAP.php');
require_once('ping.php');

if(!isset($_SESSION['loggedIn']))
{
	header('location:logout.php');
	exit();
}

//else if((!isset($_SESSION['landed']) || $_SESSION['landed'] == false ) && $vtex_page != \Src\Enums\INTERNAL_PAGES::LANDING)
if((!isset($_SESSION['landed']) || $_SESSION['landed'] == false ) && $vtex_page != \Src\Enums\INTERNAL_PAGES::SETTINGS)
{
    header('location:settings.php');
    exit();
}


/*
else {
	
}*/