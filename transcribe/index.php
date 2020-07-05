<?php
include('data/parts/session_settings.php');
include("data/parts/config.php");
include('data/parts/constants.php');
include('data/parts/ping.php');


if(isset($_SESSION['loggedIn']))
{
	unset($_SESSION['counter']);
    session_regenerate_id(true);
	//redirect to main
	if ($_SESSION['role'] == "2" || $_SESSION['role'] == "1") {
		//User is a System or Client Administrator
		redirect("main.php");
	} else if ($_SESSION['role'] == "3"){
		//User is a Transcriptionist
		redirect("transcribe.php");
	} else {
		redirect("policy.php");
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

	<link rel="stylesheet" type="text/css" href="data/login/vendor/select2/select2.min.css">
	<link rel="stylesheet" type="text/css" href="data/login/css/util.css?v=<?php echo $version_control ?>">
	<link rel="stylesheet" type="text/css" href="data/login/css/main.css?v=<?php echo $version_control ?>">

	<script src="data/login/vendor/jquery/jquery-3.2.1.min.js"></script>

	<!--	Tooltip 	-->
    <link rel="stylesheet" type="text/css" href="data/tooltipster/css/tooltipster.bundle.min.css" />
    <link rel="stylesheet" type="text/css" href="data/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-shadow.min.css" />
	<script type="text/javascript" src="data/tooltipster/js/tooltipster.bundle.min.js"></script>

	<!--	Scroll bar  	-->
	<script src="data/scrollbar/jquery.nicescroll.js"></script>


	<!--<link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">-->
	<link rel="stylesheet" href="data/dialogues/jquery-confirm.min2.css">
	<script src="data/dialogues/jquery-confirm.min.js"></script>



	<?php if ( isset( $_SESSION['src' ] ) ) {
	$source =  $_SESSION['src' ];
	
/*	if($source == 3 || $source == 1 || $source == 0) //mail - reset pwd info / Signup / Login
	{
		
	}*/
	
	if(isset($_SESSION['counter']))
	{
		$counter = $_SESSION['counter'];
	}
	else{
		$counter = "0";
	}
	
	?>
	<!--	show dialog   -->
	<script type="text/javascript">
		$(document).ready(function() {

			var src = '<?php echo $_SESSION['src']?>';
			var error = '<?php echo $_SESSION['error']?>';
			var msg = '<?php echo $_SESSION['msg']?>';
			var counter = <?php echo isset($_SESSION['counter'])?$_SESSION['counter']:0 ?>;
			if (counter > 0 && counter < 5 && <?php echo $source?> == 9) {
				msg = msg + "<br/><br/><b>You have (" + (6 - counter) + ") tries left.</b>";
			}
			var tit = error == '1' ? "Error" : "Success";
			if (src != 5) {
				$.confirm({
					title: tit,
					type: error == '1' ? "red" : "green",
					content: msg,
					buttons: {
						confirm: {
							btnClass: error ? 'btn-red' : 'btn-green',
							text: 'Ok'
						},
					}
				});
			} else { //src :5, verify email
				$.confirm({
					title: tit,
					type: error == '1' ? "red" : "green",
					content: msg,
					buttons: {
						confirm: {
							btnClass: error ? 'btn-red' : 'btn-green',
							text: 'Ok'
						},
						resend: {
							btnClass: 'btn-green',
							text: 'Resend Email',
							action: function() {
								var a1 = {
									email: '<?php echo $uEmail?>'
								};
								$.post("data/parts/backend_search.php", {
									reqcode: 50,
									args: JSON.stringify(a1)
								}).done(function(data) {
									//alert(data);
									location.href = 'index.php';
								});

							}
						}
					}
				});
			}


		});

	</script>

	<?php }?>


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
						<img src="data/images/Logo_vScription_Transcribe_Pro_Stacked.png" style="height: 110px" />
					</span>

					<span id="title" class="login100-form-title p-b-26">
						Welcome
					</span>

					<!----------------------NAME----------------->
					<table>
						<tr>

							<td>

								<div class="wrap-input100 validate-input" data-validate="First Name is required." id="fnamediv" style="display: none">
									<input class="input100" type="text" name="fname">
									<span class="focus-input100" data-placeholder="First Name"></span>
								</div>

							</td>
							<td width="3px">
							</td>
							<td>

								<div class="wrap-input100 validate-input" data-validate="Last Name is required." id="lnamediv" style="display: none">
									<input class="input100" type="text" name="lname">
									<span class="focus-input100" data-placeholder="Last Name"></span>
								</div>

							</td>

						</tr>

					</table>

					<!----------------------EMAIL----------------->
					<div class="wrap-input100 validate-input" id="em" data-validate="Valid email is: a@b.c" style="margin-bottom: 2px">
						<input class="input100" type="text" name="email" value="<?php echo $uEmail?>" tabindex="1">
						<span class="focus-input100" data-placeholder="Email"></span>
					</div>


					<div class="text-right" id="newsletter" style="margin-bottom: 25px;">
						<span class="txt1">
							Remember me?
						</span>

						<input type="checkbox" name="newsletter" <?php echo isset($_SESSION['remember']) ? "checked" : ""?>>
						<!--		checkbox-->
					</div>

					<!----------------------PASSWORD----------------->
					<div class="wrap-input100 validate-input" id="passwordDiv" data-validate="Enter password">
						<span class="btn-show-pass">
							<i class="zmdi zmdi-eye"></i>
						</span>
						<input class="input100" type="password" name="password" tabindex="2">
						<span class="focus-input100" data-placeholder="Password"></span>
					</div>
					<div class="text-right" id="forgotpwd">
						<span class="txt1">
							Forgot your password?
						</span>

						<a class="txt2" href="#">
							Reset
						</a>
					</div>

					<!----------------------Country----------------->
					<div class="wrap-input100 validate-input" id="countryDiv" data-validate="Country is required" style="display: none">
						<select class="select100" id="country" name="country">
						</select>
						<input class="input100" type="text" id="countryIp" name="countryIp" style="display: none">

						<span class="focus-input100"></span>

					</div>

					<!----------------------State----------------->
					<div class="wrap-input100 validate-input" id="stateDiv" data-validate="Province/State is required" style="display: none">
						<select class="select100" id="state" name="state">
							<option></option>
							<option>2</option>
							<option>3</option>
						</select>

						<input class="input100" type="text" id="stateIp" name="stateIp" style="display: none">

						<span class="focus-input100"></span>

					</div>


					<!----------------------city----------------->
					<div class="wrap-input100 validate-input" data-validate="City is required." id="cityDiv" style="display: none">
						<input class="input100" type="text" name="city">
						<span class="focus-input100" data-placeholder="City"></span>
					</div>

					<!----------------------Industry----------------->
					<div class="wrap-input100 validate-input" id="industryDiv" data-validate="Industry is required" style="display: none">
						<select class="select100" id="industry" name="industry">
							<option></option>
							<option>Healthcare</option>
							<option>Legal</option>
							<option>Financial Services</option>
							<option>Insurance</option>
							<option>Law Enforcement</option>
							<option>Other</option>
						</select>

						<input class="input100" type="text" id="industryIp" name="industryIp" style="display: none">

						<span class="focus-input100"></span>

					</div>

					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
							<div class="login100-form-bgbtn"></div>
							<button class="login100-form-btn">
								Login
							</button>
						</div>
					</div>

					<div class="text-center p-t-15" id="info" hidden>
						<span class="txt1" id="btmtxt1">
							Don’t have an account?
						</span>

						<a class="txt2" href="#" id="btmtxt2" hidden>
							Sign Up
						</a>
					</div>
					<input name="method" value="0" style="display: none">
				</form>
				<div class="text-right p-t-10" id="policy">
					<a class="txt2" href="./policy.php" id="btmtxt2" target="_blank">
						Privacy Policy
					</a>
				</div>
				<!--				</div>-->
			</div>
		</div>
	</div>

	<!--	<script src="data/login/vendor/animsition/js/animsition.min.js"></script>-->
	<script src="data/login/vendor/bootstrap/js/popper.js"></script>
	<script src="data/login/vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="data/login/vendor/select2/select2.min.js"></script>
	<!--	 <script src="data/login/vendor/daterangepicker/moment.min.js"></script> -->
	<!--	 <script src="data/login/vendor/daterangepicker/daterangepicker.js"></script>-->
	<!--	<script src="data/login/vendor/countdowntime/countdowntime.js"></script>-->
	<script src="data/scripts/login.min.js"></script>



</body>
<noscript>
	For full functionality of this site, it is necessary to enable JavaScript.
	You can find a step-by-step instruction at <a href="https://www.enable-javascript.net">enable-javascript.net</a> to enable JavaScript in your web browser.
</noscript>

</html>
<?php

		unset($_SESSION['src'],$_SESSION['msg'],$_SESSION['error']);
?>
