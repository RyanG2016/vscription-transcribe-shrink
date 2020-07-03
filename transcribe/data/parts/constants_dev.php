<?php
/*if(session_status() != 1)
{
	session_start();
}*/
if(isset($_SESSION['data']['lang']))
{
	$lang = $_SESSION['data']['lang'];
}
else
{
	$_SESSION['data']['lang'] = 'en';
	$lang = 'en';
}
//echo 'lang = '. $lang;
switch($lang)
{
	case 'en':
		$chtml = 'lang="en"';
		
		//OPTIONS
		$version_control ="2.5";
		$fileNameSuffix = "-NEEDSREVIEW";
		$slashShortcut = "Expand Word";
		$atShortcut = "Dr. List";
		$clogin = "Login";
		$cemail = "Email";
		$cpassword = "Password";
		$cbaselink = "https://vscriptiontranscribeupload.local"; //for password reset without ending slash
		
		$msgVerifyAccount= "Please verify your account first by following the link that was sent to your Email.";
		

		
		break;
		
//	case 'ar':
		
//		break;
}