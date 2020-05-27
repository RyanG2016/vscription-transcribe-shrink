<?php
session_start();

include('data/parts/ping.php');

if(!isset($_SESSION['loggedIn']))
{
	header('location:logout.php');
}
if(isset($_SESSION['counter']))
{
	unset($_SESSION['counter']);
}

//if user is logged in check for timeout
else {
	
}
?>