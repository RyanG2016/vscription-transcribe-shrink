<?php 

include('data/parts/config.php');

function generateEmailNotifications($sqlcon) {
    $con = $sqlcon;
/* 	$sql = "SELECT email FROM users WHERE 
		account = (SELECT account from users WHERE email = '" . $_SESSION['email'] . "') AND 
        email_notification = 1 AND plan_id = 3"; */
 
 /*     $sql = "SELECT email FROM users WHERE 
     account = (SELECT account from users WHERE email = 'ryan.gaudet@gmail.com') AND 
        email_notification = 1 AND plan_id = 3;"; */

    $sql = "SELECT * from users;";
	
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
        echo json_encode($recipients);
        $data = json_encode($recipients);
        //echo json_encode($receipients['email']);
        mailtest($data);
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

//echo "Calling emailNotification Function";
generateEmailNotifications($con);
?>