<!DOCTYPE html>
<html lang="en">

<?php
require '../api/vendor/autoload.php';
use Src\Enums\INTERNAL_PAGES;

$vtex_page = INTERNAL_PAGES::TRANSCRIBE;
//require_once ('rtf3/src/HtmlToRtf.php');
require '../api/bootstrap.php';

use Src\TableGateways\AccountGateway;

include('data/parts/head.php');
include('rtf3/src/HtmlToRtf.php');
include('data/parts/constants.php');

if (!isset($_SESSION['role']) || ($_SESSION['role'] != "3" && $_SESSION['role'] != "1" && $_SESSION['role'] != "2")) {
//User is a System Administrator or Typist
    ob_start();
    header('Location: ' . "index.php");
    ob_end_flush();
    die();
}

if (isset($_SESSION['fname']) && isset($_SESSION['lname'])) {
    $popName = $_SESSION['fname'] . " " . $_SESSION['lname'];
    $initials = strtolower(substr($_SESSION['fname'], 0, 1)) . strtolower(substr($_SESSION['lname'], 0, 1));
} else {
    $popName = "";
}

$accountGateway = new AccountGateway($dbConnection);
$workTypes = $accountGateway->getWorkTypes($_SESSION["accID"]);

//$version_control = "1.0";
?>

<head>

    <?php include_once("gaTrackingCode.php"); ?>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <noscript>
        <meta http-equiv="refresh" content="0;url=noscript.php">
    </noscript>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>vScription</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>



    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>
    <link href='ableplayer/styles/ableplayer.css?v=<?php echo $version_control ?>' type='text/css' rel='stylesheet'/>

    <?php

    $set = 0;
    if (isset($_GET)) {
        $set = 1;
        echo '<script>
            //clear();
            </script>';

        if (isset($_GET['jobNo']) && !empty($_GET['jobNo'])) {
            $n1 = $_GET['jobNo'];
        } else {
            $n1 = '';
        }

        if (isset($_GET['jobType']) && !empty($_GET['jobType'])) {
            $jt = $_GET['jobType'];
        } else {
            $jt = '';
        }
        echo $jt;

        if (isset($_GET['authorName']) && !empty($_GET['authorName'])) {
            $n2 = $_GET['authorName'];

        } else {
            $n2 = '';
        }


        if (isset($_GET['TypistName']) && !empty($_GET['TypistName'])) {
            $cl = $_GET['TypistName'];
        } else {
            $cl = '';
        }

        if (isset($_GET['comments']) && !empty($_GET['comments'])) {
            $ph = $_GET['comments'];
        } else {
            $ph = '';
        }

        if (isset($_GET['user_field_1']) && !empty($_GET['user_field_1'])) {
            $uf1 = $_GET['user_field_1'];
        } else {
            $uf1 = '';
        }
        if (isset($_GET['user_field_2']) && !empty($_GET['user_field_2'])) {
            $uf2 = $_GET['user_field_2'];
        } else {
            $uf2 = '';
        }
        if (isset($_GET['user_field_3']) && !empty($_GET['user_field_3'])) {
            $uf3 = $_GET['user_field_3'];
        } else {
            $uf3 = '';
        }


        if (isset($_GET['DateDic']) && !empty($_GET['DateDic'])) {
            $dateD = $_GET['DateDic'];
        } else {
            $dateD = '';
        }

        if (isset($_GET['DateTra']) && !empty($_GET['DateTra'])) {
            $dateT = $_GET['DateTra'];
        } else {
            $dateT = date("d-M-Y");
        }

    }


    ?>

    <script type="text/javascript">
        var rl = <?php echo $_SESSION["role"] ?>;
    </script>

    <!--    JQuery    -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="data/main/jquery-ui.css">

<!--    <script src="data/main/garlic.js"></script>-->
    <script src="data/main/jquery-ui.js"></script>
    <script src="data/thirdparty/scripts/moment.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>

    <!--  MDC Components  -->
<!--    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">-->
<!--    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>-->

    <!-- BOOTSTRAP -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
            integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV"
            crossorigin="anonymous"></script>


    <!--  Data table Jquery helping libs  -->
    <link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/4.0.0/material-components-web.min.css"/>

    <!--  Datatables  -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

    <!--  css  -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" crossorigin="anonymous">


    <script src='tinymce/tinymce.min.js?v=<?php echo $version_control ?>'></script>

    <script src='data/scripts/tinymce.min.js?v=<?php echo $version_control ?>'></script>
    <script src="tinymce/plugins/mention/plugin.js?v=<?php echo $version_control ?>"></script>
    <link rel="stylesheet" type="text/css" href="tinymce/plugins/mention/css/autocomplete.css">
    <link rel="stylesheet" type="text/css" href="tinymce/plugins/mention/css/rte-content.css">
    <!---->
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

    <!--	Scroll Bar Dependencies    -->

    <script src="data/scrollbar/jquery.nicescroll.js"></script>
    <!--	///// End of scrollbar depdns   /////-->

    <link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">
    <script src="data/dialogues/jquery-confirm.min.js"></script>

    <!--	Tooltip 	-->
    <link rel="stylesheet" type="text/css" href="data/tooltipster/css/tooltipster.bundle.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="data/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-punk.min.css"/>
    <script type="text/javascript" src="data/tooltipster/js/tooltipster.bundle.min.js"></script>

    <link href='data/css/transcribe.css?v=2' type='text/css' rel='stylesheet'/>

    <!-- Enjoyhint library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/kineticjs/5.2.0/kinetic.js"> </script>
    <link href="data/thirdparty/enjoyhint/enjoyhint.css" rel="stylesheet">
    <script src="data/thirdparty/enjoyhint/enjoyhint.min.js"></script>

	<?php $tuts=(isset($_SESSION['tutorials']))?$_SESSION['tutorials']:'{}'; ?>
    <script type="text/javascript">
        var tutorials='<?php echo $tuts;?>';
    </script>

</head>

<body>

<script src="data/scripts/parts/constants.js" type="text/javascript"></script>
<script src="data/scripts/transcribe.min.js?v=12"></script>

<div id="updated_version_bar">There is a newer version (v<span></span>) of the vScription Transcribe Controller
    available -> <a href="" target="_blank">download</a></div>

<div class="container-fluid d-flex h-auto vspt-container-fluid">
    <div class="row w-100 h-100 vspt-container-fluid-row no-gutters" style="white-space: nowrap">

        <?php include_once "data/parts/nav.php"?>

        <div class="vspt-page-container vspt-col-auto-fix">

            <div class="vtex-card contents m-0">
                <button type="button" class="btn btn-primary btn-sm pop-btn float-right" id="pop"><i class="fas fa-external-link-alt"></i></button>

                <form class="validate-form" method="post" name="form" id="form" enctype="multipart/form-data">

                    <div class="form-row">
                        <div class="col-xl col-lg-12 demographics-col">
                            <legend><span class="number">1</span> Demographics &nbsp;<i class="fas fa-chevron-down vtex-glow" id="demoExpand"></i></legend>
                            <div id="demoItems">
                                <div class="form-row mt-2">

                                    <div class="input-group mb-3 col-xl-3 col-lg-3 col-md-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="jobNo-addon1">#</span>
                                        </div>
                                        <input type="text" class="form-control" id="jobNo" placeholder="Job/File ID"
                                               name="jobNo" <?php if ($set == 1 && !empty($n1)) {
                                            echo 'value="' . $n1 . "\"";
                                        } ?> readonly>
                                    </div>

                                    <div class="input-group mb-3 col-xl-5 col-lg-6 col-md-6 ">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="authorName-addon1">Author</span>
                                        </div>
                                        <input type="text" id="authorName" class="form-control" name="authorName"
                                               title="Author Name" <?php if ($set == 1 && !empty($n2)) {
                                            echo 'value="' . $n2 . "\"";
                                        } ?> readonly>
                                    </div>

                                    <div class="input-group mb-3 col-xl-4 col-lg-3 col-md-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="jobType-addon1">Job Type</span>
                                        </div>


                                        <select class="form-control mt-0" id="jobType" name="jobType" disabled>
                                            <?php
                                            foreach ($workTypes as $type)
                                            {
                                                $type = trim($type);
                                                echo '<option value="'.strtolower($type).'">'.$type.'</option>';
                                            }

                                            ?>
                                        </select>

                                    </div>

                                    <div class="input-group mb-3 col-xl-6 col-lg-6 col-md-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="date-addon1"><i class="fas fa-calendar-alt"></i> &nbsp;Dictated</span>
                                        </div>
                                        <input type="text" name="DateDic" class="form-control" id="date"
                                               title="Date Dictated" <?php if ($set == 1 && !empty($dateD)) {
                                            echo 'value="' . $dateD . "\"";
                                        } ?> readonly>
                                    </div>

                                    <div class="input-group mb-3 col-xl-6 col-lg-6 col-md-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="dateT-addon1"><i class="fas fa-calendar-alt"></i> &nbsp;Transcribed</span>
                                        </div>
                                        <input type="text" name="DateTra" id="dateT" class="form-control"
                                               title="Date Transcribed" <?php if ($set == 1 && !empty($dateT)) {
                                            echo 'value="' . $dateT . "\"";
                                        } else {
                                            echo date("d-M-Y");
                                        } ?> readonly>
                                    </div>


                                    <div class="col-12 user-fields" id="userFields">
                                        <div class="form-row">
                                            <div class="input-group mb-3 col-xl-4 col-lg-4 col-md-4">
                                                <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                      id="user_field_1-addon1">User Field 1</span>
                                                </div>
                                                <input type="text" id="user_field_1" name="user_field_1"
                                                       class="form-control"
                                                       title="User Field 1" <?php if ($set == 1 && !empty($uf1)) {
                                                    echo 'value="' . $uf1 . "\"";
                                                } ?> readonly>
                                            </div>

                                            <div class="input-group mb-3 col-xl-4 col-lg-4 col-md-4">
                                                <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                      id="user_field_2-addon1">User Field 2</span>
                                                </div>
                                                <input type="text" id="user_field_2" name="user_field_2"
                                                       class="form-control"
                                                       title="User Field 2" <?php if ($set == 1 && !empty($uf2)) {
                                                    echo 'value="' . $uf2 . "\"";
                                                } ?> readonly>
                                            </div>

                                            <div class="input-group mb-3 col-xl-4 col-lg-4 col-md-4">
                                                <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                      id="user_field_3-addon1">User Field 3</span>
                                                </div>
                                                <input type="text" id="user_field_3" name="user_field_3"
                                                       class="form-control"
                                                       title="User Field 3" <?php if ($set == 1 && !empty($uf3)) {
                                                    echo 'value="' . $uf3 . "\"";
                                                } ?> readonly>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="input-group mb-3 col-xl-6 col-lg-6 col-md-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="comments-addon1"><i class="fas fa-comment-dots"></i> &nbsp;Typist</span>
                                        </div>
                                        <input type="text" id="comments" name="comments" class="form-control"
                                               title="Typist Comments" <?php if ($set == 1 && !empty($ph)) {
                                            echo 'value="' . $ph . "\"";
                                        } ?> disabled>
                                    </div>

                                    <div class="input-group mb-3 col-xl-6 col-lg-6 col-md-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="file_comment-addon1"><i class="fas fa-comment-dots"></i> &nbsp;Job</span>
                                        </div>
                                        <input type="text" id="file_comment" name="file_comment" class="form-control"
                                               title="File Comments" <?php if ($set == 1 && !empty($ph)) {
                                            echo 'value="' . $ph . "\"";
                                        } ?> readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col audio-col">
                            <audio id="audio1" width="450" data-able-player preload="auto" data-seek-interval="2" data-transcript-src="transcript">
                            </audio>
                        </div>
                    </div>

                    <table width="100%" style="padding-bottom: 5px" id="demo-tbl">
                        <tr>

                            <td align="right">

							<span class="controller-status" id="statusTxt">
                                <i>connecting to controller please wait...</i>
                            </span>

                                <button class="btn btn-sm load-button" id="loadBtn" name="loadBtn" type="button">

                                    <i class="fas fa-cloud-download"></i>
                                    Load
                                </button>
                            </td>

                        </tr>
                    </table>

                    <fieldset>
                        <table id="rep-tbl">
                            <tr>
                                <td>
                                    <legend id="leg"><span class="number">2</span> Report Body</legend>
                                </td>

                                <td id="nr">
                                    <button class="btn btn-sm save-button" id="saveBtn" name="saveBtn" type="submit" disabled>
                                        <i class="fas fa-save"></i> Save and Complete
                                    </button>
                                </td>
                                <td id="nr">
                                    <button class="btn btn-sm suspend-button" id="suspendBtn" type="submit" name="suspendBtn" disabled>
                                        <i class="fas fa-pause-circle"></i> Suspend
                                    </button>
                                </td>
                                <td id="nr">
                                    <button class="btn btn-sm discard-button" id="discardBtn" name="discardBtn" type="button" disabled>
                                        <i class="fas fa-times" aria-hidden="true"></i>
                                        Discard
                                    </button>
                                </td>
                            </tr>
                        </table>
                        <div class="row no-gutters" style="padding-bottom: 10px">
                            <div id="accord" class="col pb-0">
                                <h3>Shortcuts</h3>
                                <div style="overflow: visible;">
                                    <!--					 <img id="norm" src="data/images/f248.png" /> <&lt;DRSPELLING>>&nbsp;&nbsp; <img id="norm" src="data/images/f348.png" /> <&lt;PATSPELLING>>&nbsp;&nbsp; -->
                                    <!--					&nbsp;&nbsp; <img id="norm" src="data/images/at.png" /> <php //echo $atShortcut ?>-->

                                    <legend id="tip"><img id="norm" src="data/images/f248.png"/>
                                        <&lt;INAUDIBLE>> &nbsp;&nbsp; <img id="norm"
                                                                           src="data/images/slash.png"/> <?php echo $slashShortcut ?>
                                    </legend>
                                </div>
                            </div>
                            <div class="col-auto">
                                <button type="button" id="searchEngine" class="btn btn-primary btn-sm h-auto"
                                        style="margin-top: 2px;" hidden>TEXT SEARCH
                                    <i class="fas fa-search-plus"
                                       style="font-size: large"></i>
                                </button>
                            </div>
                            <!--<div class="col-auto mt-auto mb-auto mr-auto pr-4 pl-1" id="searchEngine" hidden>
                                <i class="hover-expand fab fa-searchengin" style="font-size: x-large; color: var(--vtex-blue)"></i>
                            </div>-->
                        </div>
                    </fieldset>
                </form>
                <div id="divv" class="form-row">
                    <div class="col">
                        <textarea id="report" name="report" placeholder="" rows="25" class="area"></textarea>
                    </div>
                    <!--                    <div class="col-auto justify-content-end align-items-end"><input type="text" id="captionsSearch"/></div>-->
                </div>


            </div>

        </div>
    </div>
</div>


<!-- The Modal -->
<div id="modal" class="vtex-modal">

    <!-- Modal content -->
    <div class="vtex-modal-content">
        <h2>Job Picker</h2>
        <!--            <p><i>Filtering Jobs with Status of: Awaiting Transcription, In Progress and Suspended.</i></p>-->

        <div style="overflow-x: hidden" class="vspt-table-div">
            <table id="jobs-tbl" class="table vspt-table hover compact"></table>
        </div>
    </div>

</div>

<!-- The Modal -->
<div id="modalLoading" class="vtex-modal">

    <!-- Modal content -->
    <div class="vtex-modal-content" id="loadingContent">
        <h2>Please wait..</h2>
        <p><i></i></p>


        <div style="text-align: right">
            <button class="mdc-button mdc-button--unelevated confirm-button" id="loadingConfirm">
                <div class="mdc-button__ripple"></div>
                <i class="material-icons mdc-button__icon" aria-hidden="true">done_all</i>
                <span class="mdc-button__label">OK</span>
            </button>
        </div>
    </div>

</div>

<div id="modalSearchCaptions" class="vtex-modal" >

    <!-- Modal content -->
    <div class="vtex-modal-content" id="loadingContent" style="width: fit-content!important;">

        <div class="row">
            <div class="col"><h2>Text Search</h2></div>
            <div class="col-auto justify-content-end align-items-end pr-0 pb-0 mt-auto">
                <input type="text" id="captionsSearch"/>
            </div>
            <div class="col-auto vtex-help-icon hover-expand pb-0 mt-auto pl-2 mr-1" id="searchBtn">
                <i class="fas fa-search" style="font-size: x-large"></i>
            </div>
        </div>

<!--        <div id="captionResult">-->
            <table id="captionsTbl" class="display" style="width: 100% !important"></table>
<!--        </div>-->

        <div class="row">
            <div style="text-align: right" class="mt-2 col justify-content-end align-items-end pr-0">
                <button class="mdc-button mdc-button--unelevated suspend-button" id="capSrcClear">
                    <div class="mdc-button__ripple"></div>
                    <!--                <i class="material-icons mdc-button__icon" aria-hidden="true">done_all</i>-->
                    <span class="mdc-button__label">clear</span>
                </button>
            </div>
            <div style="text-align: right" class="mt-2 col-auto justify-content-end align-items-end">
                <button class="mdc-button mdc-button--unelevated confirm-button" id="capSrcClose">
                    <div class="mdc-button__ripple"></div>
                    <!--                <i class="material-icons mdc-button__icon" aria-hidden="true">done_all</i>-->
                    <span class="mdc-button__label">close</span>
                </button>
            </div>
        </div>
    </div>

</div>


<div class="overlay" id="overlay">
    <div class="loading-overlay-text" id="loadingText">Loading Transcribe..</div>
    <div class="spinner">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
        <div class="bounce3"></div>
    </div>
</div>

<?php include_once "data/parts/footer.php"?>
</body>

</html>
