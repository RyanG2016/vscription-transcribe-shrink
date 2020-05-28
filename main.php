<!DOCTYPE html>
<html lang="en">

<?php
//require_once ('rtf3/src/HtmlToRtf.php');
include('data/parts/head.php');
include ('rtf3/src/HtmlToRtf.php');
include ('data/parts/constants.php');

	if(isset($_SESSION['fname']) && isset($_SESSION['lname']))
	{
		$popName = $_SESSION['fname'] . " " . $_SESSION['lname'];
	}
	else{
		$popName = "";
	}

//$version_control = "1.0";
?>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
    <noscript><meta http-equiv="refresh" content="0;url=noscript.php"></noscript>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="data/main/script.js"></script>
    <link rel="stylesheet" href="data/css/upload_form.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="data/main/joblistscripts.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;1,300&display=swap" rel="stylesheet">
    <link href='data/fontawesome/css/all.css?v=<?php echo $version_control ?>' type='text/css' rel='stylesheet' />
    <link rel="stylesheet" href="data/css/vs-style.css">

    <title>vScription Transcribe Pro Dictation Upload</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
    <link href='data/main/upload_form.css?v=<?php echo $version_control ?>' type='text/css' rel='stylesheet'/>
<!--    <link href='data/main/buttons.css?v=--><?php //echo $version_control ?><!--' type='text/css' rel='stylesheet'/>-->

    <link href='data/fontawesome/css/all.css?v=<?php echo $version_control ?>' type='text/css' rel='stylesheet'/>
<!--    <link href='ableplayer/styles/ableplayer.css?v=--><?php //echo $version_control ?><!--' type='text/css' rel='stylesheet'/>-->

    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet">
	
	<!--	Font 	-->
<!--	<link href="https://fonts.googleapis.com/css?family=Oxygen&display=swap" rel="stylesheet">-->
<!--	<link href="https://fonts.googleapis.com/css?family=Oxygen&display=swap" rel="stylesheet">-->

    <link rel="stylesheet" href="data/main/jquery-ui.css">
    <script src="data/main/jquery.js"></script>
    <script src="data/main/garlic.js"></script>
    <script src="data/main/jquery-ui.js"></script>

    <script src='tinymce/js/tinymce/tinymce.min.js?v=<?php echo $version_control ?>'></script>
    <script src='data/main/tinymceFree.js?v=<?php echo $version_control ?>'></script>
    <script src="tinymce/js/tinymce/plugins/mention/plugin.js?v=<?php echo $version_control ?>"></script>
    <link rel="stylesheet" type="text/css" href="tinymce/js/tinymce/plugins/mention/css/autocomplete.css">
    <link rel="stylesheet" type="text/css" href="tinymce/js/tinymce/plugins/mention/css/rte-content.css">


	<script src="data/main/main.js?v=<?php echo $version_control ?>" > </script>
	
<!--	Scroll Bar Dependencies    -->

	<script src="data/scrollbar/jquery.nicescroll.js"></script>
<!--	///// End of scrollbar depdns   /////-->

	
<link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">
<script src="data/dialogues/jquery-confirm.min.js"></script>

</head>
<body >

<div id="container" style="width: 100%">
	<div class="form-style-5">

        <table id="header-tbl" >
            <tr>
                <td id="logbar" align="right" colspan="2">
                    Logged in as: <?php echo $_SESSION['uEmail']?> |</div>
                    <a class="logout" onclick="location.href = 'logout.php'">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </td>
            </tr>
            <tr class="spacer"></tr>
            <tr style="margin-top: 50px">
                <td class="title" align="left" width="450px">
                    <legend>vScription Transcribe Pro Job Lister</legend>
                </td>
                <!--<td align="right" rowspan="2" id="fix-td">

                </td>-->

                <td width="305">
                    <img src="data/images/Logo_vScription_Transcribe_Pro.png" width="300px" />
                </td>
            </tr>


        </table>

    <table>
        <tr>
            <td colspan="1"></td>
            <td colspan="3">
                <h3 class="getList">Job List</h3>
            </td>
        </tr>
        <tr>
            <td colspan="1"  style="vertical-align: top">

                <div class="box box8" >
<!--                    <div class="listControl">-->
                        <label for="refresh" class="refresh_lbl">Refresh</label>
                        <input type="button" id="refresh_btn" class="refresh">
                        <label for="refresh" class="upload_lbl">Upload Jobs</label>
                        <input type="button" id="newupload_btn" class="newupload">
<!--                    </div>-->
                </div>

            </td>

            <td colspan="3">
                <div class="box box9">


                    <div class="joblist">
<!--                        Job table goes here-->
                    </div>

                </div>
            </td>
        </tr>
    </table>



	</div>
</div>

   
</body>

</html>