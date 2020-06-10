<?php 

include('data/parts/config.php');
include('data/parts/head.php');

if(isset($_SESSION['fname']) && isset($_SESSION['lname']))
{
    $popName = $_SESSION['fname'] . " " . $_SESSION['lname'];
    $initials = strtolower(substr($_SESSION['fname'],0,1)) . strtolower(substr($_SESSION['lname'],0,1));
}
else{
    $popName = "";
}

function generateEmailNotifications($sqlcon) {
    $con = $sqlcon;
  	$sql = "SELECT email FROM users WHERE 
		account = (SELECT account from users WHERE email = '" . $_SESSION['uEmail'] . "') AND 
        email_notification = 1 AND plan_id = 3"; 

//    $sql = "SELECT * from users;";
	
	if($stmt = mysqli_prepare($con, $sql))
	{
		if(mysqli_stmt_execute($stmt)){
			$result = mysqli_stmt_get_result($stmt);
			// Check number of rows in the result set
			if(mysqli_num_rows($result) > 0){
                //echo "We found some rows";
				// Fetch result rows as an associative array
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                    //echo strval($row['email']);
					$recipients[]=$row['email'];
				}
			}
			else {
				// If there are no records in the DB for this account

				echo "No recipients are configured to received these notifications";
			}
		}
		else{
			echo "The SQL Call failed";
        }
        //echo $recipients;
        //echo json_encode($recipients);
        //$data = json_encode($recipients);
        //echo json_encode($receipients['email']);
        foreach($recipients as $item) {
            echo $item . "<br />";
            $a = Array (
                "email" => $item
            );
            sendEmail(10, $a,"", true);
        } 
        //mailtest($data);
    }
    else {
        echo "ERROR: Could not execute $sql. " . mysqli_error($con->error) .'<br>';
        die( "Error in execute: (" .$con->errno . ") " . $con->error);
    }
    //$_SESSION['email']
}

function mailtest($a) {
    $args = json_decode($a);
    //echo "Here are the arguments " . $args;
    foreach($args as $item) {
        echo $item . "<br />";
    } 

}

function sendEmail($mailType,$a,$token,$appendmsg)//0:login-default, 1:signup, 4:resetpwd 5:signup verify
{
    //$args = json_decode($a);
	include('data/parts/constants.php');
	include("mail.php");
    $email = strtolower($a["email"]);
    echo "Sending to: " . $email;
	$_SESSION['src'] = $mailType;
	

//	$_SESSION['msg'] = $_SESSION['msg']; 
	$link = "$cbaselink/verify.php?token=$token";
	
	switch($mailType)
	{
		case 0:
			include('data/parts/reset_email_template.php');
			$sbj = "Password Reset";
			$_SESSION['src'] = 2; //TDO
				break;
		case 5:
			include('data/parts/verify_email_temp.php');
			$sbj = "Email Verification";
			$_SESSION['src'] = 2;
			$mail->addCC("sales@vtexvsi.com");
				break;
		case 10:
			include('data/parts/document_complete_template.php');
			$sbj = "New Document Ready for Download";
			$_SESSION['src'] = 2; 
			$mail->addCC("sales@vtexvsi.com");
		break;
		case 15:
			include('data/parts/job_ready_for_typing_template.php');
			$sbj = "New Job Ready for Typing";
			$_SESSION['src'] = 2; 
			$mail->addCC("sales@vtexvsi.com");
		default:
			$sbj = "vScription Transcribe Pro";
				break;
	}
	
	$mail->addAddress("$email"); //recipient
	$mail->Subject = $sbj;
	$mail->Body    = $emHTML;
	$mail->AltBody = $emPlain;
	
	
	try{
			$mail->send();
			$_SESSION['msg'] = $_SESSION['msg'];
			if(!$appendmsg)
			{
				$_SESSION['error'] = false; //outputs empty error in session
				$_SESSION['msg'] = "Email sent."; 
			}
			else{
				$_SESSION['msg'] = $_SESSION['msg'] . "<br/><br/>" . "Email sent."; 
			}
		
		} catch (Exception $e) {
			if(!$appendmsg)
			{
				$_SESSION['error'] = true;  //error=1 in session
				$_SESSION['msg'] = "Email couldn\'t be sent at this time please try again. {$mail->ErrorInfo}";
			}
			else{
				$_SESSION['msg'] = $_SESSION['msg'] . "<br/><br/>" . "Email couldn\'t be sent at this time please try again. {$mail->ErrorInfo}";
			}
		}
} //send Email end

//echo "Calling emailNotification Function";
generateEmailNotifications($con);
?>