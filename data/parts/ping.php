<?php
//session_start();

$ctime = $_SERVER['REQUEST_TIME'];
//echo date("Y-m-d H:i:s");
//echo date_default_timezone_get();
	/**
	* for a 2 minute timeout, specified in seconds
	*/
	$timeout_duration = 604800;
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
			session_start();
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
