<!DOCTYPE html>
<html lang="en">

<?php
//require_once ('rtf3/src/HtmlToRtf.php');
require '../api/vendor/autoload.php';
use Src\Enums\INTERNAL_PAGES;

$vtex_page = INTERNAL_PAGES::JOB_UPLOAD;


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

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.0/moment.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <!--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/js/tempusdominus-bootstrap-4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css" />-->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!--    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>-->
    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>


    <title>vScription Transcribe Pro Dictation Upload</title>

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

	 <script src="data/scripts/job_upload.min.js?v=1"></script>
    <link rel="stylesheet" href="data/css/job_upload.css">
	
	

</head>

<body>


<div class="container-fluid d-flex h-auto vspt-container-fluid">
    <div class="row w-100 h-100 vspt-container-fluid-row no-gutters" style="white-space: nowrap">

        <?php include_once "data/parts/nav.php"?>

        <div class="vspt-page-container vspt-col-auto-fix">

            <div class="row">
                <div class="col">
                    <a class="logbar" href="main.php"><i class="fas fa-arrow-left"></i> Go back to job list</a>
                </div>
            </div>

            <div class="row vspt-title-row no-gutters">
                <div class="col align-items-end d-flex">
                    <legend class="page-title mt-auto">
                        <i class="material-icons mdc-button__icon" aria-hidden="true">cloud_upload</i>
                        vScription Transcribe Pro Dictation Upload
                    </legend>
                </div>
                <div class="col-auto">
                    <img src="data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
                </div>
            </div>

            <div class="vtex-card contents">
                <div class="vspt-step-progress-container mr-auto ml-auto">
                    <ul class="vspt-step-progressbar" id="vsptProgressList">
                        <li class="active">Add Files</li>
                        <li>Add info</li>
                        <li>Upload</li>
                    </ul>
                </div>
                <form class="upload needs-validation" id="upload_form" method="post" enctype="multipart/form-data" novalidate>
                    <div id="uploadCarousel" class="carousel slide upload-carousel" data-ride="carousel" data-interval="false">
                        <div class="carousel-inner">

                            <div class="carousel-item active">

                                <div class="row" id="srBalance" style="display:none;">
                                    <div class="col text-center">
                                        <small>
                                            <b>Speech To Text Balance </b>
                                            <span class="sr-balance"><span id="srMinutes"></span> min</span>
                                        </small>

                                        <button class="btn btn-primary add-mins-btn" type="button" onclick="window.open('/packages.php', '_blank')">
                                            <i class="fas fa-plus-circle" ></i> ADD MINS
                                        </button>
                                    </div>
                                </div>

                                <label class="vspt-drop-upload" id="vsptDropZone" for="filesInput">
                                    <div>
                                        <div class="mb-4"><i class="fad fa-upload" style="font-size: 72px"></i></div>

                                        <div id="vsptDropMainContent">
                                            <span>
                                                <a href="#" id="chooseFile">Choose a file</a> or drag it here
                                            </span>
                                            <div>(wav, mp3, ds2, m4a, ogg)</div>
                                        </div>


                                        <div id="vsptDropUploadContent" class="vspt-drop-upload-content"></div>
                                        <div id="clear" style="display: none"><a href="#" id="clearBtn">clear</a></div>

                                    </div>
                                    <br>
                                    <input id="filesInput" type="file" name="file[]"
                                           accept=".wav, .mp3, .m4a, .ds2, .ogg" multiple style="display: none" />
                                </label>

                                <h6 class="upload_limits text-muted">&emsp;Maximum 10 files — total files size must be less than 128MB</h6>

                                <input type="hidden" name="<?php echo ini_get("session.upload_progress.name"); ?>" value="job_upload" />

                                <div class="carousel-nav one">
                                    <button class="btn btn-primary p1n-button" id="p1nBtn" type="button">
                                        Next
                                    </button>
                                </div>
                            </div>

                            <div class="carousel-item">

                                <div class="carousel-inner-container">

                                    <div class="form-row">
                                        <div class="input-group col mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" for="demo_author">Author Name</span>
                                            </div>
                                            <input type="text" class="form-control" id="demo_author" placeholder="" value="<?php echo $_SESSION['fname'] . ' ' . $_SESSION['lname'] ?>">
                                        </div>
                                    </div>


                                    <div class="form-row">

                                        <div class="input-group col mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" for="demo_job_type">Job Type</span>
                                            </div>

                                            <select class="form-control" id="demo_job_type">
                                                <?php
                                                foreach ($workTypes as $type) {
                                                    $type = trim($type);
                                                    echo '<option value="' . strtolower($type) . '">' . $type . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-row mb-3">

                                        <div class="input-group col date" data-target-input="nearest">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text" id="dictDateLbl">
                                                    Dictated Date
                                                </div>
                                            </div>

                                            <input type="text" class="form-control flatpickr flatpickr-input"
                                                   id="dictDatePicker" placeholder="Select Date.." data-input required>


                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                            <div class="invalid-feedback">
                                                Please select a valid date.
                                            </div>
                                        </div>
                                    </div>




                                    <div class="form-row mb-3" id="speakerTypeDiv">

                                        <div class="input-group col">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" for="demo_speaker_type">Speaker Type</span>
                                            </div>

                                            <select class="form-control" id="demo_speaker_type">
                                                <option value="1">Single Speaker</option>
                                                <option value="2">Multiple Speakers</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="form-row mb-3">

                                        <div class="input-group col">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" for="user_field_1">User Field 1</span>
                                            </div>
                                            <input type="text" class="form-control user_field_1" id="user_field_1"
                                                   name="user_field_1" placeholder="(optional)">
                                        </div>

                                        <div class="input-group col">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" for="user_field_2">User Field 2</span>
                                            </div>
                                            <input type="text" class="form-control user_field_2" id="user_field_2"
                                                   name="user_field_2" placeholder="(optional)">
                                        </div>

                                        <div class="input-group col">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" for="user_field_3">User Field 3</span>
                                            </div>
                                            <input type="text" class="form-control user_field_3" id="user_field_3"
                                                   name="user_field_3" placeholder="(optional)">
                                        </div>

                                    </div>

                                    <div class="form-row">

                                        <div class="input-group col mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" for="demo_comments">Comments</span>
                                            </div>
                                            <textarea name="demo_comments" class="form-control" id="demo_comments" rows="4"
                                                      placeholder="(optional)"></textarea>
                                        </div>

                                    </div>

                                    <h6 class="text-muted">
                                        <strong>Note:</strong> If uploading multiple files at once, all files will have the same demographics entered on the right.
                                    </h6>
                                </div>

                                <div class="carousel-nav">
                                    <button class="btn btn-primary" id="demoBackBtn" type="button">
                                        Back
                                    </button>

                                    <button class="btn btn-primary" id="demoNextBtn" type="button">
                                        Next
                                    </button>
                                </div>
                            </div>

                            <div class="carousel-item">

                                <div class="page3-container">
                                    <h3>Selected Files:</h3>
                                    <div class="preview">
                                        <p>No files currently selected for upload</p>
                                    </div>

                                    <div class="carousel-nav">
                                        <button class="btn btn-primary" id="p3Bbtn" type="button">
                                            Back
                                        </button>

                                        <button class="mdc-button mdc-button--unelevated foo-button submit_btn" type="submit"
                                                value="Upload File(s)" disabled>
                                            <div class="mdc-button__ripple"></div>
                                            <i class="material-icons mdc-button__icon" aria-hidden="true"
                                            >cloud_upload</i
                                            >
                                            <span class="mdc-button__label">Upload File(s)</span>
                                        </button>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>
                </form>

            </div>

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

<?php include_once "data/parts/footer.php"?>

</body>

</html>
