<?php

require '../api/vendor/autoload.php';

include_once('data/parts/session_settings.php');
include("data/parts/config.php");
include('data/parts/constants.php');

include('data/parts/ping.php');


if(isset($_SESSION['loggedIn']))
{
//    session_regenerate_id(true);
	//redirect to main
    if( isset($_SESSION['role']) ) {

        if ($_SESSION['role'] == "1"){
            // User is System Admin
            redirect("panel/");
        }
        else if ($_SESSION['role'] == "2" || $_SESSION['role'] == \Src\Enums\ROLES::AUTHOR) {
            // User is Client Administrator
            redirect("main.php");
        } else if ($_SESSION['role'] == "3"){
            //User is a Transcriptionist
            redirect("transcribe.php");
        } else {
//            redirect("landing.php");
            redirect("settings.php");
        }

    } else {
//		redirect("landing.php");
        redirect("settings.php");
    }

}

isset($_SESSION['uEmail'])?$uEmail = $_SESSION['uEmail']:$uEmail = "";
	
?>


<!DOCTYPE html>
<html <?php echo $chtml ?>>

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" type="image/png" href="data/images/favicon.png" />

	<title>Login</title>
	<!-- Redirect to another page (for no-js support) (place it in your <head>) -->
	<noscript>
		<meta http-equiv="refresh" content="0;url=noscript.php"></noscript>


	<link rel="stylesheet" type="text/css" href="data/login/vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="data/login/vendor/animate/animate.css">

	<link rel="stylesheet" type="text/css" href="data/login/css/util.css?v=<?php echo $version_control ?>">
	<link rel="stylesheet" type="text/css" href="data/login/css/main.css?v=<?php echo $version_control ?>">

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!--	Tooltip 	-->
    <link rel="stylesheet" type="text/css" href="data/tooltipster/css/tooltipster.bundle.min.css" />
    <link rel="stylesheet" type="text/css" href="data/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-shadow.min.css" />
	<script type="text/javascript" src="data/tooltipster/js/tooltipster.bundle.min.js"></script>

	<!--	Scroll bar  	-->
	<script src="data/scrollbar/jquery.nicescroll.js"></script>


	<!--<link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>

</head>

<body>
	<!--Header include -->

	<!-- The content of your page would go here. -->

	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<form class="login100-form validate-form" method="post">
					<!--				<div class="login100-form validate-form">-->

					<span class="login100-form-title p-b-20">
						<!--						<i class="zmdi zmdi-font"></i>-->
						<img src="data/images/Logo_vScription_Transcribe_Stacked.png" style="height: 110px" />
					</span>


					<!----------------------EMAIL----------------->
					<div class="wrap-input100 validate-input" id="em" data-validate="Valid email is: a@b.c" style="margin-bottom: 2px">
						<input class="input100" type="text" name="email" value="<?php echo $uEmail?>" tabindex="1">
						<span class="focus-input100" data-placeholder="Email"></span>
					</div>


					<div class="text-right" id="remember" style="margin-bottom: 25px;">
						<span class="txt1">
							Remember Email
						</span>

						<input type="checkbox" name="remember" <?php echo isset($_SESSION['remember']) ? "checked" : ""?>>
						<!--		checkbox-->
					</div>

					<!----------------------PASSWORD----------------->
					<div class="wrap-input100 validate-input" id="passwordDiv" style="margin-bottom: 0" data-validate="Enter password">
						<span class="btn-show-pass">
							<i class="zmdi zmdi-eye"></i>
						</span>
						<input class="input100" type="password" name="password" tabindex="2">
						<span class="focus-input100" data-placeholder="Password"></span>
					</div>
					<div class="text-right" id="forgotpwd">
						<a class="txt2" href="#">
                            Forgot your password?
						</a>
					</div>


                    <button type="submit" class="btn btn-primary btn-lg mt-4" id="loginBtn">Login</button>
                    <!--<button class="login-btn">
                        Login
                    </button>-->
<!--					<div class="container-login100-form-btn">-->
<!--						<div class="wrap-login100-form-btn">-->
<!--							<div class="login100-form-bgbtn"></div>-->
<!---->
<!--						</div>-->
<!--					</div>-->

					<div class="text-center p-t-3" id="info">
						<a class="txt2" href="signup.php" id="btmtxt2">
							Sign Up
						</a>
					</div>
				</form>
				<div class="text-left p-t-10" id="policy">
					<a class="txt2" href="./policy.php" id="btmtxt2" target="_blank">
						Privacy Policy
					</a>
				</div>
                <?php echo $DEBUG?"<div class='text-right p-t-10 txt4' id='last-commit-hash'><i>10e1d303</i></div>":"" ?>

				<!--				</div>-->
			</div>
		</div>
	</div>

	<script src="data/login/vendor/bootstrap/js/popper.js"></script>
	<script src="data/login/vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="data/login/vendor/select2/select2.min.js"></script>
	<script src="data/scripts/login.min.js?v=2"></script>



</body>
<noscript>
	For full functionality of this site, it is necessary to enable JavaScript.
	You can find a step-by-step instruction at <a href="https://www.enable-javascript.net">enable-javascript.net</a> to enable JavaScript in your web browser.
</noscript>

</html>
<?php

		unset($_SESSION['src'],$_SESSION['msg'],$_SESSION['error']);
?>
