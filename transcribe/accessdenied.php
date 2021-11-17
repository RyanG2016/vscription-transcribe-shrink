<!DOCTYPE html>
<?php include('data/parts/constants.php'); ?>
<html <?php echo $chtml ?>>

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" type="image/png" href="data/images/favicon.png" />

	<title>Access Denied</title>
	<!-- Redirect to another page (for no-js support) (place it in your <head>) -->
	<noscript>
		<meta http-equiv="refresh" content="0;url=noscript.php"></noscript>

	<link rel="stylesheet" type="text/css" href="data/login/css/util.css?v=<?php echo $version_control ?>">
	<link rel="stylesheet" type="text/css" href="data/login/css/main.css?v=<?php echo $version_control ?>">

	<script src="data/login/vendor/jquery/jquery-3.2.1.min.js"></script>

</head>

<body>
	<!--Header include -->


	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">

					<span class="denied100-form-title p-b-20">
						<img src="data/images/Logo_vScription_Transcribe_Stacked.png" style="height: 110px" />
					</span>
					<span id="title" class="denied100-form-title p-b-26">
						<h1>ACCESS DENIED</h1>
						<p>You don't have access to that page. Contact your system administrator.</p>
					</span>
				<div class="text-right p-t-10" id="back">
					<a class="txt3" href="index.php" id="btmtxt2" target="_blank">
						 Back To Home Page
					</a>
				</div>
				<!--				</div>-->
			</div>
		</div>
	</div>
</body>
<noscript>
	For full functionality of this site, it is necessary to enable JavaScript.
	You can find a step-by-step instruction at <a href="https://www.enable-javascript.net">enable-javascript.net</a> to enable JavaScript in your web browser.
</noscript>

</html>
