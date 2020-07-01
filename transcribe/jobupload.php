<!DOCTYPE html>
<html lang="en">

<?php
//require_once ('rtf3/src/HtmlToRtf.php');
include('data/parts/head.php');
include('rtf3/src/HtmlToRtf.php');
include('data/parts/constants.php');
include_once("gaTrackingCode.php");

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] != "2" && $_SESSION['role'] != "1") {
        header('location:accessdenied.php');
    }
} else {
        header('location:accessdenied.php');
}

if (isset($_SESSION['fname']) && isset($_SESSION['lname'])) {
    $popName = $_SESSION['fname'] . " " . $_SESSION['lname'];
} else {
    $popName = "";
}

//$version_control = "1.0";
?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <noscript>
        <meta http-equiv="refresh" content="0;url=noscript.php">
    </noscript>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="data/scripts/job_upload.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;1,300&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="data/css/upload_form.css">
    <link rel="stylesheet" href="data/css/job_upload.css">

    <title>vScription Transcribe Pro Dictation Upload</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
    <link href='data/css/upload_form2.css?v=<?php echo $version_control ?>' type='text/css' rel='stylesheet'/>
    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>

    <!--  MDC Components  -->
    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>

<!--    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">-->
<!--    <link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet">-->

    <!--	Font 	-->
    <!--	<link href="https://fonts.googleapis.com/css?family=Oxygen&display=swap" rel="stylesheet">-->
    <!--	<link href="https://fonts.googleapis.com/css?family=Oxygen&display=swap" rel="stylesheet">-->

    <link rel="stylesheet" href="data/main/jquery-ui.css">
    <script src="data/main/jquery.js"></script>
    <script src="data/main/garlic.js"></script>
    <script src="data/main/jquery-ui.js"></script>

    <!--	Scroll Bar Dependencies    -->

    <script src="data/scrollbar/jquery.nicescroll.js"></script>
    <!--	///// End of scrollbar depdns   /////-->


    <link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">
    <script src="data/dialogues/jquery-confirm.min.js"></script>

</head>

<body>

<div id="container" style="width: 100%">
    <div class="form-style-5">

        <table id="header-tbl">
            <tr>
                <td id="navbtn" align="left" colspan="1">
                    <!--                        Logged in as: --><?php //echo $_SESSION['uEmail']?><!-- |-->
                    <!--                    </div>-->

                    <a class="logout" href="main.php"><i class="fas fa-arrow-left"></i> Go back to job list</a>
                </td>

                <td id="logbar" align="right" colspan="1">
                    Logged in as: <?php echo $_SESSION['uEmail'] ?> |
                    <!--                    </div>-->
                    <a class="logout" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </td>

            </tr>
            <tr class="spacer"></tr>
            <tr style="margin-top: 50px">
                <td class="title" align="left" width="450px">
                    <legend class="page-title">vScription Transcribe Pro Dictation Upload</legend>
                </td>
                <!--<td align="right" rowspan="2" id="fix-td">

                    </td>-->

                <td width="300px">
                    <img src="data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
                </td>
            </tr>


        </table>

        <div class="grid-wrapper">

            <table width="100%" class="data-tbl">

                <tr>
                    <td class="upload_cell">
                        <div class="box box4">

                            <h3>Upload Instructions</h3>
                            <ul class="ulInstructions">
                                <li>1. &nbsp;Click Choose File</li>
                                <li>2. Choose the file(s) to upload</li>
                                <li>3. Enter the file information under the Upload Demographics section. <strong>Note:</strong> If uploading multiple files at once, all files will have the same demographics entered on the right.</li>
                                <p class="upload_limits"><strong><i>(Maximum 10 files at once total files size must be less than 128MB)</i></strong></p>
                            </ul>
                        </div>
                        <div class="box box5">
                            <form class="upload" id="upload_form" method="post" enctype="multipart/form-data">
                                <!--                                <label for="upload_btn">Choose Files to Upload (wav, mp3, dss, ds2, ogg)</label>-->

                                <label class="mdc-button mdc-button--unelevated upload_btn_lbl" for="upload_btn">
                                    <div class="mdc-button__ripple"></div>
                                    <i class="material-icons mdc-button__icon" aria-hidden="true"
                                    >insert_drive_file</i
                                    >
                                    <span class="mdc-button__label">Choose Files to Upload (wav, mp3, dss, ds2, ogg)</span>
                                </label>


                                <!--                                <input type="button" class="clear_btn" value="Clear Files" name="Clear" enabled />-->
                                <!--                                <br>-->
                                <button class="mdc-button mdc-button--unelevated foo-button clear_btn" disabled>
                                    <div class="mdc-button__ripple"></div>
                                    <i class="material-icons mdc-button__icon" aria-hidden="true"
                                    >clear</i
                                    >
                                    <span class="mdc-button__label">Clear Files</span>
                                </button>

                                <button class="mdc-button mdc-button--unelevated foo-button submit_btn" type="submit"
                                        value="Upload File(s)" disabled>
                                    <div class="mdc-button__ripple"></div>
                                    <i class="material-icons mdc-button__icon" aria-hidden="true"
                                    >cloud_upload</i
                                    >
                                    <span class="mdc-button__label">Upload File(s)</span>
                                </button>

                                <input id="upload_btn" type="file" name="file[]"
                                       accept=".wav, .mp3, .dss, .ds2, .ogg" multiple/>

                                <input type="hidden" name="<?php echo ini_get("session.upload_progress.name"); ?>" value="job_upload"/>
                                <!--                                <input class="submit_btn" type="submit" value="Upload File(s)" name="Upload" disabled />-->
                            </form>
                        </div>
                        <div class="box box6">
                            <h3>Selected Files:</h3>
                            <div class="preview">
                                <p>No files currently selected for upload</p>
                            </div>

                        </div>
                    </td>
                    <!--       add vertical  center line        -->
                    <td class="upload_cell">
                        <div class="box box7">
                            <h3>Upload demographics</h3>
                            <div class="upload_fields">

                                <label for="demo_author" class="job_upload_form_lbl">Author Name</label>
                                <input class="demo_author" type="text">

                                <label for="demo_job_type" class="job_upload_form_lbl">Job Type</label>
                                <select id="demo_job_type">
                                    <option value="interview"> Interview</option>
                                    <option value="focus_group">Focus Group</option>
                                    <option value="notes">Notes</option>
                                    <option value="letter">Letter</option>
                                    <option value="other">Other</option>
                                </select>

                                <label for="demo_dictdate" class="job_upload_form_lbl">Dictated Date</label>
                                <input class="demo_dictdate" type="date">

                                <label for="demo_speaker_type" class="job_upload_form_lbl">Speaker Type</label>
                                <select id="demo_speaker_type">
                                    <option value="0">--Please Select--</option>
                                    <option value="1">Single Speaker</option>
                                    <option value="2">Multiple Speakers</option>
                                </select>
                                <label for="demo_comments" class="job_upload_form_lbl">Comments</label>
                                <textarea name="demo_comments" id="demo_comments" cols="30" rows="4"></textarea>
                            </div>
                        </div>
                    </td>

                </tr>

<!--                <tr>
                    <td colspan="2" class="upload_cell">
                        <div class="box box8">
                            <h3 class="insert_test">Upload Messages</h3>
                            <div class="msgs_container">
                                <div class="upload_success_message">
                                    <i class="fas fa-check-circle"></i>
                                    <p>Upload(s) Successful! ...Will automatically redirect to Job List in 3 seconds
                                    </p>
                                </div>
                                <div class="upload_failed_message">
                                    <i class="fas fa-ban"></i>
                                    <p>Upload(s) Failed. Please try again.</p>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>-->

            </table>

        </div>


    </div>
</div>

<!-- The Modal -->
<div id="modal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <h2>Uploading <span id="progressTxt"></span></h2>
        <p><i>Please wait while your file(s) are being uploaded..</i></p>

        <div role="progressbar" class="mdc-linear-progress mdc-linear-progress--closed" aria-label="Upload Progress Bar" aria-valuemin="0" aria-valuemax="1" aria-valuenow="0">
            <div class="mdc-linear-progress__buffer">
                <div class="mdc-linear-progress__buffer-bar"></div>
                <div class="mdc-linear-progress__buffer-dots"></div>
            </div>
            <div class="mdc-linear-progress__bar mdc-linear-progress__primary-bar">
                <span class="mdc-linear-progress__bar-inner"></span>
            </div>
            <div class="mdc-linear-progress__bar mdc-linear-progress__secondary-bar">
                <span class="mdc-linear-progress__bar-inner"></span>
            </div>
        </div>

        <div class="previewModal">
<!--            <p>No files currently selected for upload</p>-->
        </div>

        <div class="modal-buttons">
            <button class="mdc-button mdc-button--unelevated foo-button cancel_upload" id="confirmUpload">
                <div class="mdc-button__ripple"></div>
                <i class="material-icons mdc-button__icon" aria-hidden="true"
                >done_all</i
                >
                <span class="mdc-button__label">OK</span>
            </button>

            <button class="mdc-button mdc-button--unelevated foo-button cancel_upload" id="cancelUpload">
                <div class="mdc-button__ripple"></div>
                <i class="material-icons mdc-button__icon" aria-hidden="true"
                >clear</i
                >
                <span class="mdc-button__label">Cancel</span>
            </button>
        </div>
<!--        <div class="mdc-data-table">-->

<!--            <table class="mdc-data-table__table jobs_tbl" aria-label="Jobs List">-->
<!--                -->
<!--            </table>-->
<!--        </div>-->

        <!--<div class="tblButtons">
            popup's close button
            <button class="jobOpen">Open</button>
            <button class="close">Close</button>
        </div>-->
    </div>

</div>

</body>

</html>