<!DOCTYPE html>
<html lang="en">

<?php
require '../api/vendor/autoload.php';
use Src\Enums\INTERNAL_PAGES;

$vtex_page = INTERNAL_PAGES::JOB_UPLOAD;


include('data/parts/head.php');
include('data/parts/constants.php');
require '../api/bootstrap.php';

use Src\TableGateways\AccountGateway;

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] != "2" && $_SESSION['role'] != "1" && $_SESSION['role'] != "5" && $_SESSION['role'] != "3") {
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.33/moment-timezone-with-data-1970-2030.min.js" integrity="sha512-FOmgceoy0+6TMqXphk6oiZ6OkbF0yKaapTE6TSFwixidHNPt3yVnR3IRIxJR60+JWHzsx4cSpYutBosZ8iBA1g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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


    <title>vScription Transcribe Dictation Upload</title>
    <!--  MDC Components  -->
    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>

    <!--    Scroll Bar Dependencies    -->
    <script src="data/scrollbar/jquery.nicescroll.js"></script>

    <!--    Moment + Jquery confirm  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.33/moment-timezone-with-data-1970-2030.min.js" integrity="sha512-FOmgceoy0+6TMqXphk6oiZ6OkbF0yKaapTE6TSFwixidHNPt3yVnR3IRIxJR60+JWHzsx4cSpYutBosZ8iBA1g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
    
    <!-- Enjoyhint library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/kineticjs/5.2.0/kinetic.js"> </script>
    <link href="data/thirdparty/enjoyhint/enjoyhint.css" rel="stylesheet">
    <script src="data/thirdparty/enjoyhint/enjoyhint.min.js"></script>

    <!-- mediainfo library -->
    <script
      type="text/javascript"
      src="https://unpkg.com/mediainfo.js/dist/mediainfo.min.js"
    ></script>

    <?php $tuts=(isset($_SESSION['tutorials']))?$_SESSION['tutorials']:'{}'; ?>
    <script type="text/javascript">
        var tutorials='<?php echo $tuts;?>';
    </script>
    <script src="data/scripts/parts/ping.min.js" type="text/javascript"></script>
    <?php if($_SESSION['userData']['pre_pay'] == 1):?>
    <link rel="stylesheet" href="data/css/job_upload_prepay.css">
    <?php else:?>
    <link rel="stylesheet" href="data/css/job_upload.css">
    <?php endif;?>
    <!-- <?php echo $_SESSION['userData']['pre_pay'];?> -->

</head>

<body>

<div class="container-fluid h-100 vspt-container-fluid">
        <!--        <div class="w-100 h-100 d-flex flex-nowrap vspt-container-fluid-row">-->
        <div class="vspt-container-fluid-row d-flex">

        <?php include_once "data/parts/nav.php"?>
        <div class="vspt-page-container">
            <div class="row">
                <div class="col
                ">

            <!-- <div class="row">
                <div class="col">
                    <a class="logbar" href="main.php"><i class="fas fa-arrow-left"></i> Go back to job list</a>
                </div>
            </div> -->

            <!-- <div class="row vspt-title-row no-gutters">
                <div class="col align-items-end d-flex">
                    <legend class="page-title mt-auto">
                        <i class="material-icons mdc-button__icon" aria-hidden="true">cloud_upload</i>
                        vScription Transcribe Dictation Upload
                    </legend>
                </div>
                <div class="col-auto">
                    <img src="data/images/Logo_vScription_Transcribe.png" width="300px"/>
                </div>
            </div> -->

            <div class="vtex-card contents">
                <!-- <div class="row">
                    <h3 class="users-tbl-title">Upload Jobs</h3>
                </div> -->
                <div class="vspt-step-progress-container mr-auto ml-auto">
                    <ul class="vspt-step-progressbar" id="vsptProgressList">
                        <li class="active">Add Files</li>
                        <li>Add info</li>
                        <li id="finalStepIconTxt">Pay/Upload</li>
                    </ul>
                </div>
                <form class="upload needs-validation" id="upload_form" method="post" enctype="multipart/form-data" novalidate>
                    <div id="uploadCarousel" class="carousel slide upload-carousel" data-ride="carousel" data-interval="false">
                        <div class="carousel-inner">

                            <div class="row" id="srBalance" style="display:none;">
                                <div class="col text-center">
                                    <small>
                                        <b>Speech To Text Balance </b>
                                        <span class="sr-balance"><span id="srMinutes"></span> min</span>
                                    </small>

                                    <button class="btn btn-primary add-mins-btn" id="addMinsBtn" type="button" onclick="window.open('/packages.php', '_blank')">
                                        <i class="fas fa-plus-circle" ></i> ADD MINS
                                    </button>
                                </div>
                            </div>

                            <div class="carousel-item active">

                                <label class="vspt-drop-upload" id="vsptDropZone" for="filesInput">
                                    <div>
                                        <div class="mb-4"><i class="fad fa-upload" style="font-size: 72px"></i></div>

                                        <div id="vsptDropMainContent">
                                            <span>
                                                <a href="#" id="chooseFile">Click to add one or more files</a> or drag them here
                                            </span>
                                            <div>(wav, mp3, dss, ds2, m4a, mp4, ogg)</div>
                                        </div>


                                        <div id="vsptDropUploadContent" class="vspt-drop-upload-content"></div>
                                        <div id="clear" style="display: none"><a href="#" id="clearBtn">clear</a></div>
                                        <div id="getMediaInfo" style="display: none"><a href="#" id="getMediaInfoBtn">get media info</a></div>

                                    </div>
                                    <br>
                                    <input id="filesInput" type="file" name="file[]"
                                           accept=".wav, .mp3, .m4a, .dss, .ds2, .ogg, .mp4" multiple style="display: none" />
                                </label>

                                <h6 class="upload_limits text-muted">&emsp;Maximum 10 files — total files size must be less than 350MB</h6>

                                <input type="hidden" name="<?php echo ini_get("session.upload_progress.name"); ?>" value="job_upload" />

                                <div class="carousel-nav one">
                                    <div class="row">
                                        <div class="col-md-6 offset-6">
                                                <button class="btn btn-primary p1n-button w-100" id="p1nBtn" type="button">
                                                Next
                                                <i class="fas fa-arrow-right pl-1"></i>
                                                </button>
                                        </div>
                                    </div>
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




                                    <div class="form-row mb-3 d-none" id="speakerTypeDiv">

                                        <div class="input-group col">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" for="demo_speaker_type">Speaker Type</span>
                                            </div>

                                            <select class="form-control" id="demo_speaker_type">
                                                <option selected value="1">Single Speaker</option>
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

                                        <div class="input-group col d-none">
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
                                        <strong>Note:</strong> If uploading multiple files at once, all files will have the same demographics entered above.
                                    </h6>
                                </div>

                                <div class="carousel-nav">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button class="btn btn-primary w-100" id="demoBackBtn" type="button">
                                            <i class="fas fa-arrow-left pr-1"></i>Back
                                            </button>
                                        </div>
                                        <div class="col-md-6">
                                            <button class="btn btn-primary w-100" id="demoNextBtn" type="button">
                                                Next
                                                <i class="fas fa-arrow-right pl-1"></i>
                                            </button>
                                         </div>
                                    </div>
                                </div>
                            </div>
                            <?php if($_SESSION["userData"]["pre_pay"] ==1):?>
                           <!--  <div class="carousel-item">

                                <div class="carousel-inner-container">
                                    <div class="row">
                                        <div class="col-md-6 justify-content-center d-flex">
                                            <p>Total Billed Minutes: 
                                                <span id="total_mins_charge"></span>
                                            </p>
                                        </div>
                                        <div class="col-md-6 justify-content-center d-flex">                                        
                                            <span>Total Price: </span>
                                            <span id="total_charge"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="carousel-nav">
                                    <button class="btn btn-primary" id="payBackBtn" type="button">
                                        Back
                                    </button>

                                    <button class="btn btn-primary" id="payNextBtn" type="button">
                                        Next
                                    </button>
                                </div> -->
                            <!-- </div> -->
                            <?php endif;?>
                            <div class="carousel-item">
                                <div class="page3-container">

                                    <div class="preview">

                                        <table class="que-files">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>File Name</th>
                                                <th>Size</th>
                                                <th>Duration</th>
                                                <th>Status</th>
                                            </tr>
                                            </thead>
                                            <tbody id="queFilesBody">
                                            <tr>
                                                <td colspan="5" style="text-align: center">
                                                    No files currently selected for upload
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <div id="srBar"></div>

                                    </div>
                                    <?php if($_SESSION["userData"]["pre_pay"] ==1):?>                                   
                                        <div class="preview-totals">
                                        <table class="que-files">
                                            <thead>
                                                <tr>
                                                    <th colspan="2" class="preview-header-label">Totals</th>
                                                </tr>
                                            </thead>
                                            <tbody id="queFilesTotals">
                                            <tr>
                                                <td style="text-align: right" class="totals-col col-label">
                                                Total Minutes:
                                                </td>
                                                <td id="sum_sub">
                                                    $0.00
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right" class="totals-col col-label">
                                                Comp Minutes:
                                                </td>
                                                <td id="sum_comp" class="comp-mins">
                                                    -0.00
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right" class="totals-col col-label">
                                                Bill Rate:
                                                </td>
                                                <td id="sum_br" >
                                                    $0.00
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right" class="totals-col grand-total-label">
                                                Total To Bill:
                                                </td>
                                                <td id="sum_gt" class="grand-total">
                                                    $0.00
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="totals-col plus-tax-label">
                                                (Plus applicable taxes)
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php endif;?>
                                    <div class="carousel-nav">
                                        <div class="row">
                                            <div class="col md-6">
                                                <button class="btn btn-primary w-100" id="p3Bbtn" type="button">
                                                <i class="fas fa-arrow-left pr-1"></i>    
                                                Back
                                                </button>
                                            </div>
                                            <div class="col-md-6 w-100">
                                                <button class="mdc-button mdc-button--unelevated foo-button submit_btn w-100" type="submit"
                                                        value="Upload File(s)" disabled>
                                                    <div class="mdc-button__ripple"></div>
                                                    <i class="material-icons mdc-button__icon" aria-hidden="true"
                                                    >cloud_upload</i
                                                    >
                                                    <span class="mdc-button__label" id="mdc-button__label"><?php echo $_SESSION["userData"]["pre_pay"] == 1 ? "Pay and Upload":"Upload File(s)";?></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>
                </form>

                <div class="position-fixed bottom-0 toast-container right-0 p-3" style="z-index: 50000; right: 0; bottom: 0;">

                    <div id="uploadToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
                        <div class="toast-header">
                            <img src="data/images/Logo_only.png" height="24px" class="rounded mr-2">
                            <strong class="mr-auto">Uploader</strong>
                            <!--                                    <small>Just now</small>-->
                            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="toast-body">
                            (.MP4) File type is not supported
                        </div>
                    </div>
                </div>

            </div>
            </div>
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
                <i class="material-icons mdc-button__icon" aria-hidden="true">done</i>
                <span class="mdc-button__label">OK</span>
            </button>

            <button class="mdc-button mdc-button--unelevated foo-button cancel_upload" id="cancelUpload">
                <div class="mdc-button__ripple"></div>
                <i class="material-icons mdc-button__icon" aria-hidden="true">clear</i>
                <span class="mdc-button__label">Cancel</span>
            </button>
        </div>
        <input type="hidden" value = "<?php echo $_SESSION["userData"]["pre_pay"];?>" id="prepay_status">
        <input type="hidden" value = "<?php echo is_null($_SESSION["userData"]["lifetime_minutes"])?0:$_SESSION["userData"]["lifetime_minutes"];?>" id="lifetime_minutes">
        <input type="hidden" value = "<?php echo $_SESSION["userData"]["promo"];?>" id="promo">
        <input type="hidden" value = "<?php echo $_SESSION["userData"]["comp_mins"];?>" id="comp_mins">
        <input type="hidden" value = "<?php echo $_SESSION["userData"]["bill_rate1"];?>" id="bill_rate1">

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
<?php if($_SESSION["userData"]["pre_pay"] == 1):?>
<script src="data/scripts/job_upload_prepay.min.js?v=4"></script>
<?php else:?>
<script src="data/scripts/job_upload.min.js?v=5"></script>
<?php endif;?>
<form action="prepayment.php" method="post" class="hidden" style="display:none" id="prepayForm" target="_blank">
    <input type="text" name="total_files" id="total_files" value = "0">
    <input type="text" name="total_display_minutes" id="total_display_minutes" value = "0">
    <input type="text" name="total_mins" id="total_mins" value = "0">
    <!-- <input type="text" name="comp_price" id="comp_price" value = "3"> -->
    <!-- <input type="text" name="total_price" id="total_price" value = "3"> -->
</form>
