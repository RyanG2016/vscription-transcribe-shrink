<?php
//session_start();

$ctime = $_SERVER['REQUEST_TIME'];
//echo date("Y-m-d H:i:s");
//echo date_default_timezone_get();
	/**
	* interaction timeout, specified in seconds, currently set to 1 day of inactivity
	*/
	$timeout_duration = 86400;
	/**
	* Here we look for the user's LAST_ACTIVITY timestamp. If
	* it's set and indicates our $timeout_duration has passed,
	* blow away any previous $_SESSION data and start a new one.
	*/
	if (isset($_SESSION['lastPing'])){
	   
		if( ($ctime - strtotime($_SESSION['lastPing'])  ) > $timeout_duration)
		{
			session_unset();
			session_destroy();
			include('session_settings.php');
			$_SESSION['cleared']='session timeout';
		}
		else{
			$_SESSION['cleared']='session intact';
		}
//		echo $_SESSION['cleared'];
		
	}

	/**
	* Finally, update LAST_ACTIVITY so that our timeout
	* is based on it and not the user's login time.
	*/

	$_SESSION['lastPing'] = date("Y-m-d H:i:s");

?>
