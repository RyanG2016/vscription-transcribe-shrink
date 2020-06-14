<?php
    //This file is very sensitive to any echos as they get output to the rtf and prevents them from opening
    //Make sure there are no echos in this file. (Aside from login echo)
    include('data/parts/head.php');
    include('data/parts/constants.php');
    include('data/parts/config.php');

    if (isset($_SESSION['fname']) && isset($_SESSION['lname'])) {
        $popName = $_SESSION['fname'] . " " . $_SESSION['lname'];
    } else {
        $popName = "";
        echo "You don't have permission to access this file";
        /* Redirect to accessdenied.php */
    }
    $job_id = $_GET['job_id'];

    /*-----Get existing data for job --------*/

    $sql3 = "SELECT times_text_downloaded_date, text_downloaded_date FROM files WHERE job_id = ?
    AND acc_id = (SELECT account FROM users WHERE email = '" .$_SESSION['uEmail'] ."')";
    if($stmt3 = mysqli_prepare($con, $sql3))
    {
        mysqli_stmt_bind_param($stmt3, "s", $job_id);
        if(mysqli_stmt_execute($stmt3) ){
            $result = mysqli_stmt_get_result($stmt3);
            // Check number of rows in the result set
            if(mysqli_num_rows($result) > 0){
                // Fetch result rows as an associative array						
                while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                    $times_downloaded = $row['times_text_downloaded_date'];
                    $times_downloaded++;
                    $text_downloaded_date = $row['text_downloaded_date'];
                }
            } else {
                //echo "<p>No matches found for job " .$job_id . "</p>";
            }
        } else {
            //echo "Error executing " .$sql3;
        }
    }  else 
    {
            //echo "ERROR: Could not prepare to execute $sql1. " . mysqli_error($con);
            //die( "Error in excute: (" .$con->errno . ") " . $con->error);
    }
    /*------Update download statistics ------*/
    if (is_null($text_downloaded_date)) {
        $text_downloaded_date = date("Y-m-d H:i:s");
    } 
    $sql1 = "UPDATE FILES SET times_text_downloaded_date=?, text_downloaded_date=?
       WHERE job_id = ? AND acc_id = (SELECT account FROM users WHERE email = '" .$_SESSION['uEmail'] ."')";

    if($stmt1 = mysqli_prepare($con, $sql1))
    {
        if( !$stmt1->bind_param("sss", $times_downloaded, $text_downloaded_date, $job_id)   )
        {
                // die( "Error in bind_param: (" .$con->errno . ") " . $con->error);
        }
        $B = mysqli_stmt_execute($stmt1);
        if($B){
        $result = mysqli_stmt_get_result($stmt1);
        }
        else {
            //echo "ERROR: Could not able to execute $sql1. " . mysqli_error($con);
            //die( "Error in excute: (" .$con->errno . ") " . $con->error);
        }
    }
    else
    {
        //echo "ERROR: Could not able to execute $sql1. " . mysqli_error($con);
    }


    /*------Generate File download ------*/
    $sql = "SELECT job_document_rtf FROM files WHERE job_id=? AND acc_id = (SELECT account FROM users WHERE email = '" .$_SESSION['uEmail'] ."')";

    if($stmt = mysqli_prepare($con, $sql))
    {
        mysqli_stmt_bind_param($stmt, "s", $job_id);

        if(mysqli_stmt_execute($stmt) ){
            $result = mysqli_stmt_get_result($stmt);
            // Check number of rows in the result set
            if(mysqli_num_rows($result) > 0){
                //echo "We found at least one row for job " . $job_id;
                // Fetch result rows as an associative array						
                while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                    $filename = $job_id;
					$keys = array_keys( $row );
                    header('Content-Disposition: attachment; filename="'.$filename.'.rtf"');
                    header('Content-Type: text/plain'); # Don't use application/force-download - it's not a real MIME type, and the Content-Disposition header is sufficient
                    header('Content-Length: ' . strlen($row[$keys[0]]));
                    header('Connection: close');
                    echo $row[$keys[0]];
                }
            }
        } else {
            // error connecting to DB
        }
}  else {
    		//echo "<p>No matches found for job " .$job_id . "</p>";
    }
?>
