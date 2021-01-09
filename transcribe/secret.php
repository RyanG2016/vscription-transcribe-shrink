<?php

use Src\TableGateways\tokenGateway;

require __DIR__. '/../api/bootstrap.php';
include('data/parts/session_settings.php');
include('data/parts/constants.php');

$_SESSION['lastPing'] = date("Y-m-d H:i:s");
$error = false;
$msg = false;

if(isset($_GET['s']))
{
    $tokenGateway = new tokenGateway($dbConnection);
    $result = $tokenGateway->evaluateToken($_GET["s"]);
    $error = $result["error"];
    $msg = $result["msg"];

}//end isset token
else{ //token isn't set

	redirect("index.php");
	exit();
}
?>


<!DOCTYPE html>
<html lang="en">
	
<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>

	<title>Loading..</title>

	<noscript><meta http-equiv="refresh" content="0;url=noscript.php"></noscript> 

	<link rel="stylesheet" type="text/css" href="data/login/css/util.css">
	<link rel="stylesheet" type="text/css" href="data/login/css/main.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <link rel="stylesheet" href="data/css/parts/modal_overlay.css">
    <link rel="stylesheet" href="data/css/parts/global.css">

<!--	show dialog   -->
<script type="text/javascript">
<?php if($msg){
	
	?>

$(document).ready(function () {
    $("#loadingText").html(<?php echo "'$msg'" ?>);
    $(".spinner").hide();

    setTimeout(function() {
        location.href = "index.php";
    }, 1500);
});
	
<?php }?>


</script>
	
	
</head>

<body>

<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<form class="login100-form validate-form" method="get">

					
					<span class="login100-form-title p-b-20">
						<img src="data/images/Logo_vScription_Transcribe_Stacked.png" style="height: 110px"/>
					</span>
					
					<span class="login100-form-title p-b-26">
<!--						Email Verification-->
						Processing Please Wait...
					</span>
						
			
				</form>

			</div>
		</div>
	</div>

<div class="overlay" id="overlay" style="">
    <div class="loading-overlay-text" id="loadingText">Please wait..</div>
    <div class="spinner">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
        <div class="bounce3"></div>
    </div>
</div>
</body>

</html>
