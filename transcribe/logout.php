
<?php
include('data/parts/session_settings.php');
include('data/parts/config.php');
include("data/parts/common_functions.php");
// Getting logout time in db
isset($_SESSION['uEmail'])?$uemail = $_SESSION['uEmail']:$uemail = "";
if(isset($_SESSION['loggedIn']))
{
	$uemail = $_SESSION['uEmail'];

    // log LOGOUT to act_log
    $a = Array(
        'email' => $uemail,
        'activity' => 'Logout',
        'actPage' => 'logout.php',
        'actIP' => getIP(),
        'acc_id' => 0
    );
    $b = json_encode($a);
    insertAuditLogEntry($con, $b);

    $rmb = false;
    if(isset( $_SESSION['remember'] ) )
    {
        $rmb = $_SESSION['remember'];
    }
    session_unset();

    if($rmb)
    {
        $_SESSION['remember']=true;
        $_SESSION['uEmail']=$uemail;
    }
		//session_destroy();

	$_SESSION['msg']="Please login to continue";

    redirect("index.php");
}
else{// not even loggedIn
	// go to default page (login page)
	$rmb = false;
	if(isset( $_SESSION['remember'] ) )
	{
		$rmb = $_SESSION['remember'];
	}		
	session_unset();
	session_destroy();
    // session_regenerate_id(true);

	if($rmb)
	{
		$_SESSION['remember']=true;
		$_SESSION['uEmail']=$uemail;
	}
    redirect("index.php");
}