<?php 
include("data/parts/config.php");
include('data/parts/head.php');
include ('data/parts/constants.php');

	if(isset($_SESSION['fname']) && isset($_SESSION['lname']))
	{
		$popName = $_SESSION['fname'] . " " . $_SESSION['lname'];
	}
	else{
		$popName = "";
	}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="data/main/joblistscripts.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;1,300&display=swap" rel="stylesheet">
    <link href='data/fontawesome/css/all.css?v=<?php echo $version_control ?>' type='text/css' rel='stylesheet' />
    <link rel="stylesheet" href="data/css/vs-style.css">
    <title>vScription Transcribe Pro Job Lister</title>
</head>

<body>
    <div class="grid-wrapper">
        <div class="box box1 page_title">
            <h1>vScription Transcribe Pro Job Lister</h1>
        </div>
        <div class="box box2">
            <div class="logged_in_info">
                <div class="logged_in_user">

                    Logged in as: <?php echo $popName ?> |</div>
                <div class="logout_btn"><a href="logout.php">Logout</a></div>
            </div>
        </div>
        <div class="box box3">
            <img class="logo" src="data/images/Logo_vScription_Transcribe.png" alt="vScription Transcribe Pro Logo">
        </div>
        <div class="box box9">

            <h3 class="getList">Job List</h3>
            <div class="joblist">
                Job table goes here
            </div>

        </div>
        <div class="box box8">
            <div class="listControl">
                <label for="refresh" class="refresh_lbl">Refresh</label>
                <input type="button" id="refresh_btn" class="refresh">
                <label for="refresh" class="upload_lbl">Upload Jobs</label>
                <input type="button" id="newupload_btn" class="newupload">
            </div>
        </div>
    </div>

</body>

</html>
