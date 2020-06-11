<!DOCTYPE html>
<html lang="en">

<?php
//require_once ('rtf3/src/HtmlToRtf.php');
include('data/parts/head.php');
include ('rtf3/src/HtmlToRtf.php');
include ('data/parts/constants.php');
include_once("gaTrackingCode.php");

	if(isset($_SESSION['fname']) && isset($_SESSION['lname']))
	{
		$popName = $_SESSION['fname'] . " " . $_SESSION['lname'];
		$initials = strtolower(substr($_SESSION['fname'],0,1)) . strtolower(substr($_SESSION['lname'],0,1));
	}
	else{
		$popName = "";
	}

//$version_control = "1.0";
?>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<noscript>
		<meta http-equiv="refresh" content="0;url=noscript.php"></noscript>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>vScription</title>
	<link rel="shortcut icon" type="image/png" href="data/images/favicon.png" />

	<!--The tablesort.css is causing some styling conflicts with main page-->
<!--	<link href='data/thirdparty/scripts/css/styles.css?v=--><?php //echo $version_control ?><!--' type='text/css' rel='stylesheet' />-->
<!--	<link href='data/thirdparty/scripts/css/tablesort.css?v=--><?php //echo $version_control ?><!--' type='text/css' rel='stylesheet' />-->

	<link href='data/css/transcribe.css?v=<?php echo $version_control ?>' type='text/css' rel='stylesheet' />
	<link href='data/main/buttons.css?v=<?php echo $version_control ?>' type='text/css' rel='stylesheet' />

	<link href='data/fontawesome/css/all.css?v=<?php echo $version_control ?>' type='text/css' rel='stylesheet' />
	<link href='ableplayer/styles/ableplayer.css?v=<?php echo $version_control ?>' type='text/css' rel='stylesheet' />

	<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet">

	<!--	Font 	-->
	<!--	<link href="https://fonts.googleapis.com/css?family=Oxygen&display=swap" rel="stylesheet">-->
	<!--	<link href="https://fonts.googleapis.com/css?family=Oxygen&display=swap" rel="stylesheet">-->


	<?php

    $set = 0;
    if(isset($_GET))
    {
        $set = 1;
        echo '<script>
            //clear();
            </script>';

        if(isset($_GET['jobNo']) && !empty($_GET['jobNo']))
        {
            $n1 = $_GET['jobNo'];
        }
        else
        {
            $n1 = '';
        }

        if(isset($_GET['jobType']) && !empty($_GET['jobType']))
        {
            $jt = $_GET['jobType'];
        }
        else
        {
            $jt = '';
        }
        echo $jt;

        if (isset($_GET['authorName']) && !empty($_GET['authorName']))
        {
            $n2 = $_GET['authorName'];

        }
        else {
            $n2 = '';
        }


        if (isset($_GET['TypistName']) && !empty($_GET['TypistName'])) {
            $cl = $_GET['TypistName'];
        } else {
            $cl = '';
        }

        if (isset($_GET['comments']) && !empty($_GET['comments']))
        {
            $ph = $_GET['comments'];
        }
        else{
            $ph = '';
        }


        if(isset($_GET['DateDic']) && !empty($_GET['DateDic']))
        {
            $dateD = $_GET['DateDic'];
        }
        else{
            $dateD = '';
        }

        if(isset($_GET['DateTra']) && !empty($_GET['DateTra']))
        {
            $dateT = $_GET['DateTra'];
        }
        else{
            $dateT = date("d/m/Y");
        }

    }


    ?>

	<link rel="stylesheet" href="data/main/jquery-ui.css">
	<script src="data/main/jquery.js"></script>
	<script src="data/main/garlic.js"></script>
	<script src="data/main/jquery-ui.js"></script>

	    <!--  MDC Components  -->
    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>

	<script src='tinymce/js/tinymce/tinymce.min.js?v=<?php echo $version_control ?>'></script>
	<script src='data/scripts/tinymce.js?v=<?php echo $version_control ?>'></script>
	<script src="tinymce/js/tinymce/plugins/mention/plugin.js?v=<?php echo $version_control ?>"></script>
	<link rel="stylesheet" type="text/css" href="tinymce/js/tinymce/plugins/mention/css/autocomplete.css">
	<link rel="stylesheet" type="text/css" href="tinymce/js/tinymce/plugins/mention/css/rte-content.css">




	<?php
    require "phpspellcheck/include.php";

    $mySpell = new SpellCheckButton();
    $mySpell->InstallationPath = "phpspellcheck/";
    $mySpell->Fields = "EDITORS";
    ?>

	<!--	Able Player dependencies   -->
	<script src="ableplayer/thirdparty/js.cookie.js"></script>
	<!-- JavaScript -->
	<script src="ableplayer/build/ableplayer.js?v=<?php echo $version_control ?>"></script>
	<!--	///// End of Able Player deps   /////-->

	<!--	Transcribe Window    -->
<!--	<script src="data/main/transcribe.js"></script>-->

	<!--	Scroll Bar Dependencies    -->

	<script src="data/scrollbar/jquery.nicescroll.js"></script>
	<!--	///// End of scrollbar depdns   /////-->

	<link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">
	<script src="data/dialogues/jquery-confirm.min.js"></script>

    <!--  Data table Jquery helping libs  -->
    <link rel="stylesheet" type="text/css" href="data/libs/DataTables/datatables.css"/>
    <script type="text/javascript" src="data/libs/DataTables/datatables.js"></script>

    <script type="application/javascript">

        $(document).ready(function() {
        });

    </script>

</head>
<!-- <?php //include_once("analyticstracking.php") ?> -->

<body>
	<div id="message_bar">For best experience and foot control support please download the <a href="https://pro.vscription.com/downloads/vScription_Transcribe_Installer.msi" target="_blank" title="Download Latest Version of vScription Transcribe">vScription Transcribe Application</a></div>
	<div id="updated_version_bar">There is a newer version of the vScription Transcribe application available. You can <a href="https://pro.vscription.com/downloads/vScription_Transcribe_Installer.msi" target="_blank" title="Download Latest Version of vScription Transcribe">download it here </a></div>
    <script src="data/scripts/parts/constants.js" type="text/javascript"></script>
	<script src="data/main/transcribe.js"> </script>

	<div id="container" style="width: 100%">
		<div class="form-style-5">
		<form class="validate-form" method="post" name="form" data-persist="garlic" id="form" enctype="multipart/form-data">
		
				<table id="header-tbl">
					<tr>
						<td id="mainlogo-td" rowspan="2">
							<img id="mainlogo" src="data/images/Logo_vScription_Transcribe_Pro_Stacked.png" />

						</td>
						<td align="right" rowspan="2" id="fix-td">

						</td>
						<td width="225px">
							<span class="top-links" id="control">
								<a href="#" title="">Hide Text Area</a>
							</span>
						</td>
						<td id="help-td" align="right" width="225px">

							<a class="button-blue" onclick="location.href = 'logout.php'">
								<i class="fas fa-sign-out-alt"></i>
								Logout
								<!--					<strong>Logout</strong>-->
							</a>

						</td>

					</tr>
					<tr>
						<td id="audio-td" align="right" width="450px" colspan="2">

							<audio id="audio1" width="450" data-able-player preload="auto" data-seek-interval="2">
								<!--			  <source type="audio/ogg" id="audsrc2" src="ableplayer/media/Test_D7813875.ogg"/>-->
								<!--			  <source type="audio/mpeg" id="audsrc" src="ableplayer/media/Test_D7813875.mp3"/>-->
							</audio>


						</td>
					</tr>

				</table>

				<table width="100%" style="padding-bottom: 5px" id="demo-tbl">
					<tr>
						<td id="demo-td">
							<legend><span class="number">1</span> Demographics </legend>
						</td>


						<td align="right" style="width:1%; white-space:nowrap;">

							<a class="button" id="loadBtn">
								<i class="fas fa-cloud-upload-alt"></i>
								Load
							</a>
							<!--<input type="file" id="fileLoadDiag" style="display: none" accept="audio/vnd.wave, audio/wav, audio/wave, audio/mpeg,audio/ogg,audio/x-wav" />-->
						</td>

						<td align="right" width="114px" style="width:1%; white-space:nowrap;">
							<a class="button noHover disabled" id="completeBtn">
								<i class="fas fa-check-circle"></i>
								<!--								<strong>Complete</strong>-->
								Complete
							</a>
						</td>
					</tr>
				</table>

				<fieldset class="tooltip" style="padding-bottom: 0">

					<!--	 Row 1	    -->
					<input type="text" class="job" id="jobNo" name="jobNo" placeholder="Job/File ID" title="Job/File ID" <?php if($set == 1 && !empty($n1)) {echo 'value="'.$n1."\"";} ?> readonly="readonly">
					<input type="text" id="authorName" name="authorName" placeholder="Author Name" title="Author Name" <?php if($set == 1 && !empty($n2)) {echo 'value="'.$n2."\"";} ?>>
					<!--            <input type="text" id="TypistName" name="TypistName" placeholder="Typist Name" title="Typist Name" <?php //if($set == 1 && !empty($cl)) {echo 'value="'.$cl."\"";} ?>  >-->
					<input type="text" id="TypistName" name="TypistName" placeholder="Typist Name" title="Typist Name" value="<?php echo $popName ?>" readonly="readonly">

					</br>
					<!--	Row 2	    -->

					<!--		Job Type	-->
					<input type="text" id="jobType" class="jobt" name="jobType" placeholder="Job Type" title="Job Type" <?php if($set == 1 && !empty($jt)) {echo 'value="'.$jt."\"";} ?>>
					<!--		Date Dictated	-->
					<input type="text" name="DateDic" id="date" placeholder="Date Dictated" title="Date Dictated" <?php if($set == 1 && !empty($dateD)) {echo 'value="'.$dateD."\"";} ?>>
					<!--		Date Transcripted	-->
					<input type="text" name="DateTra" id="dateT" placeholder="Date Transcribed" title="Date Transcribed" <?php if($set == 1 && !empty($dateT)) {echo 'value="'.$dateT."\"";}else{echo date("d/m/Y");} ?>>
					<!--		Comments	-->
					<input type="text" id="comments" name="comments" placeholder="Comments" title="Comments" <?php if($set == 1 && !empty($ph)) {echo 'value="'.$ph."\"";} ?>>

				</fieldset>
				<fieldset>
					<table id="rep-tbl">
						<tr>
							<td>
								<legend id="leg"><span class="number">2</span> Report Body</legend>
							</td>

							<td id="nr">
								<span class="top-links" id="help">
									<a href="https://vscription.helpdocsonline.com/home" target="_blank" title="">Need help</a>
									<i class="fal fa-question-circle fa-sm"></i>
								</span>
							</td>

							<td id="nr">
								<button class="button-green" id="saveBtn" type="submit">
									<i class="fas fa-save"></i>
									<!--				<strong>Save and Complete</strong>-->
									Save and Complete
								</button>
							</td>
							<td id="nr">
								<a class="button-orange" id="suspendBtn">
									<i class="fas fa-hourglass-half"></i>
									<!--				<strong>Suspend</strong>-->
									Suspend
								</a>
							</td>
							<td id="nr">
								<a class="button-red" onclick="clearWithConfirm();">
									<i class="fas fa-trash-alt"></i>
									<!--					<strong>Discard</strong>-->
									Discard
								</a>
							</td>
						</tr>
					</table>
					<div id="accord">
						<h3>Shortcuts</h3>
						<div>
							<!--					 <img id="norm" src="data/images/f248.png" /> <&lt;DRSPELLING>>&nbsp;&nbsp; <img id="norm" src="data/images/f348.png" /> <&lt;PATSPELLING>>&nbsp;&nbsp; -->
							<!--					&nbsp;&nbsp; <img id="norm" src="data/images/at.png" /> <php //echo $atShortcut ?>-->

							<legend id="tip"><img id="norm" src="data/images/f248.png" />
								<&lt;INAUDIBLE>> &nbsp;&nbsp; <img id="norm" src="data/images/slash.png" /> <?php echo $slashShortcut  ?>
							</legend>
						</div>
					</div>

					<div id="divv"><textarea id="report" name="report" placeholder="" rows="25" class="area"></textarea></div>
					<div class="userinfo">
						<p class=userinfolbl>Logged in as:  <span class="typistemail" style="margin-left:4px;"> <?php echo $_SESSION["uEmail"]?></span></p>
					</div>

				</fieldset>

				<div align="center">

					<!--	Buttons old alignment 	-->

				</div>

			</form>


		</div>
	</div>

    <!-- The Modal -->
    <div id="modal" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <h2>Job Picker</h2>
            <p><i>Filtering Jobs with Status of: Awaiting Transcription, In Progress and Suspended.</i></p>
            <div class="mdc-data-table">
            <!--Job table goes here-->
                <table class="mdc-data-table__table jobs_tbl" aria-label="Jobs List">

                </table>
            </div>

            <!--<div class="tblButtons">
                popup's close button
                <button class="jobOpen">Open</button>
                <button class="close">Close</button>
            </div>-->
        </div>

    </div>


</body>

</html>
