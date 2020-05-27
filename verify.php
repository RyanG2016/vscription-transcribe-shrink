<?php
session_start();
include("data/parts/config.php");
include('data/parts/constants.php');

$_SESSION['lastPing'] = date("Y-m-d H:i:s");
//we have a message
$msg = "";
$error = "";
$showmsg = false;
$passed = false;
$mark = false;
if(isset($_GET['token']))
{
	//check alogrithm
//	$email = strtolower($a["email"]);//tbc
	$token = $_GET['token'];
	$ct = date("Y-m-d H:i:s");
	$sct = strtotime(date("Y-m-d H:i:s")); //current time stamp in seconds
	$timestamp = strtotime($ct) + 60*60;
	$onehourahead = date("Y-m-d H:i:s", $timestamp);

	$ip = getenv('HTTP_CLIENT_IP')?:
		  getenv('HTTP_X_FORWARDED_FOR')?:
		  getenv('HTTP_X_FORWARDED')?:
		  getenv('HTTP_FORWARDED_FOR')?:
		  getenv('HTTP_FORWARDED')?:
		  getenv('REMOTE_ADDR');
	$action = "Password Reset";


	$sql = "SELECT *, DATE_ADD(time, INTERVAL '24:0' HOUR_MINUTE) as expire FROM `tokens` WHERE identifier=? AND used=0 and token_type=5 and DATE_ADD(time, INTERVAL '24:0' HOUR_MINUTE) > NOW()";
	
	
	$sql2 = "UPDATE tokens SET used=1 WHERE identifier = ?";
	$sql3 = "UPDATE users SET account_status=1 WHERE email = ?";
	$stmt2 = mysqli_prepare($con, $sql2);
	$stmt3 = mysqli_prepare($con, $sql3);
	
	
	if($stmt = mysqli_prepare($con, $sql) ) //token query
	{
		mysqli_stmt_bind_param($stmt, "s", $token);
		mysqli_stmt_bind_param($stmt2, "s", $token);

		if(mysqli_stmt_execute($stmt)){
			$result = mysqli_stmt_get_result($stmt);

			// Check number of rows in the result set
			if(mysqli_num_rows($result) > 0){ //token exists

				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

					$email = $row['email'];
					$_SESSION['uEmail'] = $email;
					$time = $row['time'];
					$stime = strtotime($time);
					$used = $row['used'];
					
					mysqli_stmt_bind_param($stmt3, "s", $email);
					
					if($used == 0)
					{//token available
						mysqli_stmt_execute($stmt2);
						mysqli_stmt_execute($stmt3);
						
						$mark = true;
						
						/*$_SESSION['msg'] = "Email verified! you can now login.";
						$_SESSION['error'] = false;
						$_SESSION['src'] = 0;
						redirect("index.php");*/ 
						
					}
					
					else{// token was used
						$_SESSION['msg'] = "Link doesn\'t exist or expired.";
//						$_SESSION['msg'] = "Token used before.";
						$_SESSION['error'] = true;
						$_SESSION['src'] = 4;
						redirect("index.php");
					}

				}


			}
			else{ //token doesn't exist
				$_SESSION['msg'] = "Link doesn\'t exist or expired.";
				$_SESSION['error'] = true;
				$_SESSION['src'] = 0;
				redirect("index.php");
				
			}
		}
		else{
						//failed to execute stmt

			}
	} else{// couldn't prepare statements
		 	//echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);

	}


	// Close statement
	mysqli_stmt_close($stmt)  or die( "Error in bind_param: (" .$con->errno . ") " . $con->error);
}//end isset token
else{ //token isn't set
	
	redirect("index.php");
}
?>


<!DOCTYPE html>
<html <?php echo $chtml ?>>


	
	
<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>

	<title>Verify Email</title>

	<noscript><meta http-equiv="refresh" content="0;url=noscript.php"></noscript> 

	<link rel="stylesheet" type="text/css" href="data/login/vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="data/login/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="data/login/fonts/iconic/css/material-design-iconic-font.min.css">
	<link rel="stylesheet" type="text/css" href="data/login/vendor/animate/animate.css">
	<link rel="stylesheet" type="text/css" href="data/login/vendor/css-hamburgers/hamburgers.min.css">
	<link rel="stylesheet" type="text/css" href="data/login/vendor/animsition/css/animsition.min.css">
	<link rel="stylesheet" type="text/css" href="data/login/vendor/select2/select2.min.css">
	
	<link rel="stylesheet" type="text/css" href="data/login/vendor/daterangepicker/daterangepicker.css">
	<link rel="stylesheet" type="text/css" href="data/login/css/util.css">
	<link rel="stylesheet" type="text/css" href="data/login/css/main.css">
	
	<script src="data/login/vendor/jquery/jquery-3.2.1.min.js"></script>
	<!--	Scroll bar  	-->
	<script src="data/scrollbar/jquery.nicescroll.js"></script>
	
	
<!--<link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">-->
<link rel="stylesheet" href="data/dialogues/jquery-confirm.min2.css">
<script src="data/dialogues/jquery-confirm.min.js"></script>

<!--	show dialog   -->
<script type="text/javascript">
<?php if($showmsg){
	
	?>
	
		$(document).ready(function() {
	
		var error = '<?php echo $error?>';
		var msg = '<?php echo $msg?>';
		var tit = error==1 || error=='1'?"Error":"Success";
		$.confirm({
		title: tit,
		 type: error==1 || error=='1'?"red":"green",
		content: msg,
		buttons: {
			confirm: {
			btnClass: 'btn-red',
				text:'Ok'
			},
		}
	});
	
		
});
	
	<?php }?>
	
	
<?php if($mark){
	
	?>
	
		/*$_SESSION['msg'] = "Email verified! you can now login.";
						$_SESSION['error'] = false;
						$_SESSION['src'] = 0;*/
	
		$(document).ready(function() {
	
	var error = false;
		var msg = 'Email verified! redirecting...';
		var tit = "Success";
		$.confirm({
		title: tit,
		 type: "green",
		content: msg,
		buttons: {
			ok: {
				btnClass: 'btn-green',
				text: 'ok'
			}
		},//btns
		onContentReady: function () {
        // when content is fetched & rendered in DOM
//        alert('onContentReady');
		setTimeout(function(){ location.href = 'confirm.php'; }, 2500);
        
    }
			
	});
	
		
});
	
	<?php }?>

</script>
	
	
</head>

<body>
<!--Header include -->

<!-- The content of your page would go here. -->

<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<form class="login100-form validate-form" method="get">

					
					<span class="login100-form-title p-b-20">
						<img src="data/images/Logo_vScription_Transcribe_Stacked.png" style="height: 110px"/>
					</span>
					
					<span class="login100-form-title p-b-26">
<!--						Email Verification-->
						Verifying Email Please Wait...
					</span>
						
			
				</form>

			</div>
		</div>
	</div>
	
	
	<script src="data/login/vendor/animsition/js/animsition.min.js"></script>
	<script src="data/login/vendor/bootstrap/js/popper.js"></script>
	<script src="data/login/vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="data/login/vendor/select2/select2.min.js"></script>
	<script src="data/login/vendor/daterangepicker/moment.min.js"></script>
	<script src="data/login/vendor/daterangepicker/daterangepicker.js"></script>
	<script src="data/login/vendor/countdowntime/countdowntime.js"></script>
	<script src="data/login/js/reset.js"></script>
	
	
</body>

</html>
