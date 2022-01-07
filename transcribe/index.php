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


<!doctype html>
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


	<!-- Start Bootstrap 5 -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

    <!-- End Bootstrap 5 -->


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

<link rel="stylesheet" type="text/css" href="data/login/css/new_custom.css">

	<!--<link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>

</head>

<body>
	<!--Header include -->

	<!-- The content of your page would go here. -->

	<div class="limiter">
		<div class="row_row">


			<div class="colmd6L d-none d-md-block">


					<span class="login100-form-title p-b-20">
						<!--						<i class="zmdi zmdi-font"></i>-->
						<img src="data/images/Logo_vScription_Transcribe_Stacked.png" style="height: 110px" />
					</span>
					<span id="copyright">
						Copyright @2022
					</span>

			 </div>


			<div class="colmd6R settings_col_md_5">
				<form class="login100-form validate-form" method="post">
					<!--				<div class="login100-form validate-form">-->
					<img src="data/images/Logo_vScription_Transcribe_Stacked.png" alt="" id="login-resp-logo" class="d-md-none">
                    <h3 class="mt-5 mb-5"> <b> Sign In </b> </h3>

					<!----------------------EMAIL----------------->
					<div class="wrap-input100 validate-input" id="em" data-validate="Valid email is: a@b.c" style="margin-bottom: 2px">
						<input class="input100" type="text" name="email" value="<?php echo $uEmail?>" tabindex="1">
						<span class="focus-input100" data-placeholder="Email"></span>

						<svg class="email_viewbox_erter" viewBox="0 0 84 80" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M2.58826 29.5372C2.58942 28.7878 2.78289 28.0513 3.15015 27.398C3.51741 26.7448 4.04618 26.1967 4.68584 25.8063L42 3L79.3142 25.8063C79.9538 26.1967 80.4826 26.7448 80.8499 27.398C81.2171 28.0513 81.4106 28.7878 81.4118 29.5372V73.0653C81.4118 74.2267 80.9504 75.3406 80.1292 76.1618C79.3079 76.9831 78.1941 77.4444 77.0327 77.4444H6.96734C5.80594 77.4444 4.6921 76.9831 3.87086 76.1618C3.04962 75.3406 2.58826 74.2267 2.58826 73.0653V29.5372Z" stroke="black" stroke-width="5"/>
<path d="M2.58826 31.4641L42 55.549L81.4118 31.4641" stroke="black" stroke-width="5" stroke-linecap="round"/>
</svg>


					</div>


					<div class="text-right" id="remember" style="margin-bottom: 0px;">
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

<svg class="email_viewbox_erter" viewBox="0 0 75 82" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M63.8 33.4H10.6C6.40264 33.4 3 36.8026 3 41V71.4C3 75.5974 6.40264 79 10.6 79H63.8C67.9974 79 71.4 75.5974 71.4 71.4V41C71.4 36.8026 67.9974 33.4 63.8 33.4Z" stroke="black" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M14.4 33.4V14.4C14.4 11.3765 15.6011 8.4769 17.739 6.33898C19.8769 4.20107 22.7765 3 25.8 3H48.6C51.6235 3 54.5231 4.20107 56.661 6.33898C58.7989 8.4769 60 11.3765 60 14.4V18.2" stroke="black" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
</svg>


					</div>
					<div class="text-right" id="forgotpwd">
						<a class="txt2" href="#">
                            Forgot your password?
						</a>
					</div>


                    <button type="submit" class="btn btn-primary btn-lg mt-4" id="loginBtn_login41">Login</button>
                    <!--<button class="login-btn">
                        Login
                    </button>-->
<!--					<div class="container-login100-form-btn">-->
<!--						<div class="wrap-login100-form-btn">-->
<!--							<div class="login100-form-bgbtn"></div>-->
<!---->
<!--						</div>-->
<!--					</div>-->

					<div class="row p-t-20 m-0 " style="width: 100%">
                        <div class="col" id="hasaccount" style="padding: 0">
                            <div class="text-left">
                                Don't have an account? <a class="txt2" href="signup.php" id="loginHyperLink" style="font-weight: bold; text-decoration: none;">
                                    Sign Up
                                </a>
                            </div>
                        </div>
                      
                    </div>
				</form>

				<!-- <div class="text-left p-t-10" id="policy">
					<a class="txt2" href="./policy.php" id="btmtxt2" target="_blank">
						Privacy Policy
					</a>
				</div> -->
              <!--   <?php echo $DEBUG?"<div class='text-right p-t-10 txt4' id='last-commit-hash'><i>10e1d303</i></div>":"" ?> -->

				<!--				</div>-->
			</div>


		</div>
	</div>

	<!-- <script src="data/login/vendor/bootstrap/js/popper.js"></script> -->
	<!-- <script src="data/login/vendor/bootstrap/js/bootstrap.bundle.js"></script> -->
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
