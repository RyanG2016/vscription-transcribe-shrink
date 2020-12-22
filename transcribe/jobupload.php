<!DOCTYPE html>
<html lang="en">

<?php
//require_once ('rtf3/src/HtmlToRtf.php');
$vtex_page = 404;
include('data/parts/head.php');
include('rtf3/src/HtmlToRtf.php');
include('data/parts/constants.php');
require '../api/bootstrap.php';

use Src\TableGateways\AccountGateway;

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
$accountGateway = new AccountGateway($dbConnection);
$workTypes = $accountGateway->getWorkTypes($_SESSION["accID"]);

//$version_control = "1.0";
?>

<head>
    <?php include_once("gaTrackingCode.php");?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <noscript>
        <meta http-equiv="refresh" content="0;url=noscript.php">
    </noscript>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/js/tempusdominus-bootstrap-4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css" />

    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>


    <title>vScription Transcribe Pro Dictation Upload</title>

    <link href='data/css/upload_form2.css?v=<?php echo $version_control ?>' type='text/css' rel='stylesheet'/>

    <!--  MDC Components  -->
    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>

    <!--	Scroll Bar Dependencies    -->
    <script src="data/scrollbar/jquery.nicescroll.js"></script>

    <link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">
    <script src="data/dialogues/jquery-confirm.min.js"></script>
	
	<!-- Enjoyhint library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/kineticjs/5.2.0/kinetic.js"> </script>
    <link href="data/thirdparty/enjoyhint/enjoyhint.css" rel="stylesheet">
    <script src="data/thirdparty/enjoyhint/enjoyhint.min.js"></script>

	<?php $tuts=(isset($_SESSION['tutorials']))?$_SESSION['tutorials']:'{}'; ?>
    <script type="text/javascript">
        var tutorials='<?php echo $tuts;?>';
    </script>
	
	 <script src="data/scripts/job_upload.js"></script>
    <link rel="stylesheet" href="data/css/upload_form.css">
    <link rel="stylesheet" href="data/css/job_upload.css">
	
	

</head>

<body>
<?php include_once "data/parts/nav.php" ?>
<div id="container" style="width: 100%">
    <div class="form-style-5">

        <table id="header-tbl">
            <tr>
                <td id="navbtn" align="left" colspan="1">
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

            <form class="upload needs-validation" id="upload_form" method="post" enctype="multipart/form-data" novalidate>
            <table width="100%" class="data-tbl">

                <tr>
                    <td class="upload_cell">
                        <div class="box box4">

                            <h3>Upload Instructions</h3>
                            <ul class="ulInstructions">
                                <li>1. &nbsp;Click Choose File</li>
                                <li>2. Choose the file(s) to upload</li>
                                <li>3. Enter the file information under the Upload Demographics section.</li>

                                <small class="text-muted ">
                                    &emsp;<strong>Note:</strong> If uploading multiple files at once, all files will have the same demographics entered on the right.
                                </small>

                                <br>
                                <small class="upload_limits"><strong><i>&emsp;(Maximum 10 files at once total files size must be less than 128MB)</i></strong></small>
                            </ul>
                        </div>
                        <div class="box box5">

                                <label class="mdc-button mdc-button--unelevated upload_btn_lbl" for="upload_btn">
                                    <div class="mdc-button__ripple"></div>
                                    <i class="fas fa-cloud-upload-alt"></i>
<!--                                    <span class="mdc-button__label">Choose Files to Upload (wav, mp3, m4a, ds2, ogg)</span>-->
                                    <span class="mdc-button__label">Upload</span>
                                </label><small class="text-muted">Allowed: (wav, mp3, m4a, ds2, ogg)</small>

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
                                       accept=".wav, .mp3, .m4a, .ds2, .ogg" multiple/>

                                <input type="hidden" name="<?php echo ini_get("session.upload_progress.name"); ?>" value="job_upload"/>

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

                                <div class="form-group">
                                    <label for="demo_author">Author Name</label>
                                    <input type="text" class="form-control demo_author" id="demo_author" >
                                </div>


                                <div class="form-group">
                                    <label for="demo_job_type">Job Type</label>
                                    <select class="form-control" id="demo_job_type">
                                        <?php
                                        foreach ($workTypes as $type)
                                        {
                                            $type = trim($type);
                                            echo '<option value="'.strtolower($type).'">'.$type.'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                            <div class="form-group">
                                                <label for="dictdatetime">Dictated Date</label>
                                                <div class="input-group date" id="dictdatetime" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input dictdatetimeTxt" id="dictdatetimeTxt" data-target="#dictdatetime" required/>
                                                    <div class="input-group-append" data-target="#dictdatetime" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>

                                                    <div class="valid-feedback">
                                                        Looks good!
                                                    </div>
                                                    <div class="invalid-feedback">
                                                        Please select a valid date.
                                                    </div>
                                                </div>
                                            </div>

                                <div class="form-group">
                                    <label for="demo_speaker_type">Speaker Type</label>
                                    <select class="form-control" id="demo_speaker_type">
                                        <option value="1">Single Speaker</option>
                                        <option value="2">Multiple Speakers</option>
                                    </select>
                                </div>

                                <div class="form-row">

                                    <div class="form-group col-md-4">
                                        <label for="user_field_1">User Field 1</label>
                                        <input type="text" class="form-control user_field_1" id="user_field_1" name="user_field_1" placeholder="(optional)">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="user_field_2">User Field 2</label>
                                        <input type="text" class="form-control user_field_2" id="user_field_2" name="user_field_2" placeholder="(optional)">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="user_field_3">User Field 3</label>
                                        <input type="text" class="form-control user_field_3" id="user_field_3" name="user_field_3" placeholder="(optional)">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="demo_comments">Comments</label>
                                    <textarea name="demo_comments" class="form-control" id="demo_comments" rows="4" placeholder="(optional)"></textarea>
                                </div>


                            </div>
                        </div>
                    </td>

                </tr>
            </table>
            </form>

        </div>


    </div>
</div>

<!-- The Modal -->
<div id="modal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <h2 style="margin-top: 20px !important;">Uploading <span id="progressTxt"></span></h2>
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
                <i class="material-icons mdc-button__icon" aria-hidden="true">done_all</i>
                <span class="mdc-button__label">OK</span>
            </button>

            <button class="mdc-button mdc-button--unelevated foo-button cancel_upload" id="cancelUpload">
                <div class="mdc-button__ripple"></div>
                <i class="material-icons mdc-button__icon" aria-hidden="true">clear</i>
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
