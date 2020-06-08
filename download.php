<?php
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

    $sql = "SELECT job_document_rtf FROM files WHERE job_id=?";

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
