<?php
//session_start();
use Src\Helpers\sessionHelper;

require_once __DIR__ . "/../../../api/bootstrap.php";


//$ctime = $_SERVER['REQUEST_TIME'];
//echo date("Y-m-d H:i:s");
//echo date_default_timezone_get();

$_SESSION['lastPing'] = date("Y-m-d H:i:s");

	/**
	* interaction timeout, specified in seconds, currently set to 1 day of inactivity
	*/
//	$timeout_duration = 86400;
	/**
	* Here we look for the user's LAST_ACTIVITY timestamp. If
	* it's set and indicates our $timeout_duration has passed,
	* blow away any previous $_SESSION data and start a new one.
	*/
/*	if (isset($_SESSION['lastPing'])){
	   
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
		
	}*/
 
    if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != 1)
    {

        $kicked 	= $_SESSION['kicked']??false;
        $kick_src 	= $_SESSION['kick_src']??0;
        $kick_msg 	= $_SESSION['kick_msg']??'';

        // kick user out
        session_unset();
        session_destroy();
        include('session_settings.php'); // start a new empty session
    //        session_regenerate_id(true); // tb

        // re-add kick reason
        $_SESSION['kicked'] 	= $kicked;
        $_SESSION['kick_src'] 	= $kick_src;
        $_SESSION['kick_msg'] 	= $kick_msg;

//        header('location:/index.php');
//        exit();
    }


    if(isset($_SESSION['sess_expire_at']) && (strtotime($_SESSION['sess_expire_at']) < time()))
    {
        $sh = new sessionHelper($dbConnection);
        $expired = $sh->isExpiredFromDB(session_id()); // automatically updates session variable if not expired
        if($expired){

            // kick user out
            session_unset();
            session_destroy();
            include('session_settings.php'); // start a new empty session
            //        session_regenerate_id(true); // done automatically on new logins

            // re-add kick reason
            $_SESSION['kicked'] 	= true;
            $_SESSION['kick_src'] 	= 1;
            $_SESSION['kick_msg'] 	= 'Session has expired.';
        }

//        header('location:/index.php');
//        exit();
    }



	/**
	* Finally, update LAST_ACTIVITY so that our timeout
	* is based on it and not the user's login time.
	*/

