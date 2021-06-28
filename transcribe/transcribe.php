<!DOCTYPE html>
<html lang="en">

<?php
require '../api/vendor/autoload.php';

use Src\Enums\INTERNAL_PAGES;

$vtex_page = INTERNAL_PAGES::TRANSCRIBE;
require '../api/bootstrap.php';

use Src\TableGateways\AccountGateway;

include('data/parts/head.php');
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

    <!--  MDC Components  -->
    <!--    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">-->
    <!--    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>-->

    <!-- Context Menu -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.ui.position.js"></script>

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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css"
          crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="data/libs/semantic/dist/semantic.min.css">
    <script src="data/libs/semantic/dist/semantic.min.js"></script>


    <script src='tinymce/tinymce.min.js?v=<?php echo $version_control ?>'></script>

    <script src='data/scripts/tinymce.min.js?v=3'></script>
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

    <link href='data/css/transcribe.css?v=<?php echo $version_control ?>' type='text/css' rel='stylesheet'/>

    <!-- Enjoyhint library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/kineticjs/5.2.0/kinetic.js"></script>
    <link href="data/thirdparty/enjoyhint/enjoyhint.css" rel="stylesheet">
    <script src="data/thirdparty/enjoyhint/enjoyhint.min.js"></script>

    <?php $tuts = (isset($_SESSION['tutorials'])) ? $_SESSION['tutorials'] : '{}'; ?>
    <script type="text/javascript">
        var tutorials = '<?php echo $tuts;?>';
    </script>

</head>

<body>

<script src="data/scripts/parts/constants.js" type="text/javascript"></script>
<script src="data/scripts/transcribe.min.js?v=3"></script>

<div id="updated_version_bar">There is a newer version (v<span></span>) of the vScription Transcribe Controller
    available -> <a href="" target="_blank">download</a></div>

<div class="container-fluid h-100 vspt-container-fluid">
    <div class="w-100 h-100 d-flex flex-nowrap vspt-container-fluid-row">

        <?php include_once "data/parts/nav.php" ?>

        <div class="vspt-page-container">

            <div class="vtex-card contents m-0">

                <div class="row ">
                    <div class="col report-col">
                        <legend id="leg"><span class="number">1</span> Report Body

                            <span class="transcribe-shortcuts">
                                <img src="data/images/f1_48.png"/> <i>Insert last used word</i> &nbsp;&nbsp;

                                <img src="data/images/f2_48.png"/> <i><-INAUDIBLE-></i> &nbsp;&nbsp;

                                <img src="data/images/slash48.png"/> <i>Expand Word</i> &nbsp;&nbsp;

                                <button type="button" class="btn btn-primary btn-sm pop-btn" id="pop">
                                    <i class="fas fa-external-link-alt"></i>
                                </button>
                                <button class="ui right labeled icon grey basic button toggle-demo-bar" id="toggleDemoBar">
                                  <i class="right left arrow icon"></i>
                                  Sidebar
                                </button>
                            </span>

                        </legend>


                        <div id="divv" class="form-row report-container">
                            <div class="col">
                                <textarea id="report" name="report" placeholder="" rows="25" class="area"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto player-demo-sidebar" id="demoSidebar">
                        <span class="controller-status" id="statusTxt">
                            <i>connecting to controller please wait...</i>
                        </span>
                        <div class="col audio-col">
                            <audio id="audio1"  data-able-player preload="auto"
                                   data-skin="2020"
                                   class="mr-auto ml-auto"
                                   data-seek-interval="2" data-transcript-src="transcript">
                            </audio>
                        </div>

                        <div class="demographics-div ui equal width form" id="demoDiv">
                            <form class="validate-form" method="post" name="form" id="form" enctype="multipart/form-data">
                                <fieldset>
                                    <div class="row mr-0" style="padding-bottom: 10px">
                                        <div class="col-auto p-1">
                                            <button class="btn btn-primary btn-sm h-auto" id="searchEngine" type="button" hidden>
                                                TEXT SEARCH
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>

                                        <div class="col p-1 text-right">
                                            <button class="btn btn-sm save-button mb-1" id="saveBtn" name="saveBtn"
                                                    type="submit" disabled>
                                                <i class="fas fa-save"></i> Save and Complete
                                            </button>

                                            <button class="btn btn-sm suspend-button mb-1" id="suspendBtn" type="submit"
                                                    name="suspendBtn" disabled>
                                                <i class="fas fa-pause-circle"></i> Suspend
                                            </button>

                                            <button class="btn btn-sm discard-button mb-1" id="discardBtn" name="discardBtn"
                                                    type="button" disabled>
                                                <i class="fas fa-times" aria-hidden="true"></i>
                                                Discard
                                            </button>
                                        </div>
                                    </div>
                                </fieldset>

                                <legend id="leg" class="mt-1"><span class="number">2</span> Demographics</legend>
                                <!-- <div class="form-row">-->
                                <div class="col-xl col-lg-12 mb-3 demographics-col">

                                        <div class="field">
                                            <label>Job/File ID</label>

                                            <input type="text" class="form-control" id="jobNo"
                                                   placeholder="Job/File ID"
                                                   name="jobNo" <?php if ($set == 1 && !empty($n1)) {
                                                echo 'value="' . $n1 . "\"";
                                            } ?> readonly>
                                        </div>

                                        <div class="field">
                                            <label>Author</label>
                                            <div class="ui left icon input">
                                                <input type="text" id="authorName" class="form-control"
                                                      name="authorName"
                                                      title="Author Name" <?php if ($set == 1 && !empty($n2)) {
                                                    echo 'value="' . $n2 . "\"";
                                                } ?> readonly>
                                                <i class="user icon"></i>
                                            </div>
                                        </div>

                                        <div class="field">
                                            <label for="jobType">Job Type</label>
                                            <select class="ui search dropdown" id="jobType" name="jobType" disabled>
                                                <?php
                                                foreach ($workTypes as $type) {
                                                    $type = trim($type);
                                                    echo '<option value="' . strtolower($type) . '">' . $type . '</option>';
                                                }
                                                ?>
                                            </select>

                                        </div>

                                        <div class="field">
                                            <label for="date">Dictated Date</label>
                                            <div class="ui left icon input">

                                                <input type="text" name="DateDic" class="form-control" id="date"
                                                       title="Date Dictated" <?php if ($set == 1 && !empty($dateD)) {
                                                    echo 'value="' . $dateD . "\"";
                                                } ?> readonly>
                                                <i class="icon fas fa-calendar-alt"></i>
                                            </div>
                                        </div>

                                        <div class="field">
                                            <label for="dateT">Transcribed Date</label>
                                            <div class="ui left icon input">
                                                <input type="text" name="DateTra" id="dateT"
                                                       class="form-control"
                                                       title="Date Transcribed" <?php if ($set == 1 && !empty($dateT)) {
                                                    echo 'value="' . $dateT . "\"";
                                                } else {
                                                    echo date("d-M-Y");
                                                } ?> readonly>
                                                <i class="icon fas fa-calendar-alt"></i>
                                            </div>
                                        </div>

                                        <div class="field">
                                            <label for="user_field_1">User Field 1</label>
                                            <input type="text" id="user_field_1" name="user_field_1"
                                                   class="form-control"
                                                   title="User Field 1" <?php if ($set == 1 && !empty($uf1)) {
                                                echo 'value="' . $uf1 . "\"";
                                            } ?> readonly>
                                        </div>

                                        <div class="field">
                                            <label for="user_field_2">User Field 2</label>
                                            <input type="text" id="user_field_2" name="user_field_2"
                                                   class="form-control"
                                                   title="User Field 2" <?php if ($set == 1 && !empty($uf2)) {
                                                echo 'value="' . $uf2 . "\"";
                                            } ?> readonly>
                                        </div>

                                        <div class="field">
                                            <label for="user_field_3">User Field 3</label>
                                            <input type="text" id="user_field_3" name="user_field_3"
                                                   class="form-control"
                                                   title="User Field 3" <?php if ($set == 1 && !empty($uf3)) {
                                                echo 'value="' . $uf3 . "\"";
                                            } ?> readonly>
                                        </div>

                                        <div class="field">
                                            <label for="comments">Typist comment</label>
                                            <div class="ui left icon input">
                                                <textarea type="text" id="comments" name="comments"
                                                        class="form-control comments-text-area"
                                                        rows="2"
                                                        title="Typist Comments"
                                                        disabled>
                                                    <?php if ($set == 1 && !empty($ph)) {
                                                        echo 'value="' . $ph . "\"";
                                                    } ?>
                                                </textarea>
                                                <i class="comment dots icon"></i>

                                            </div>
                                        </div>

                                        <div class="field">
                                            <label for="file_comment">Job Comment</label>
                                            <div class="ui left icon input">
                                                <textarea type="text" id="file_comment" name="file_comment"
                                                       class="form-control comments-text-area"
                                                       rows="2"
                                                       title="File Comments" readonly>
                                                    <?php if ($set == 1 && !empty($ph)) {
                                                        echo 'value="' . $ph . "\"";
                                                    } ?>
                                                </textarea>
                                                <i class="comment dots icon"></i>
                                            </div>
                                        </div>
                                </div>

                                <!-- </div>-->


                            </form>
                        </div>
                    </div>
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
        <div class="text-right">
            <button class="btn btn-sm btn-light mb-1 mt-2"
                    id="showCompBtn"
                    data-toggle="button"
                    type="button">
                <i class="far fa-eye"></i> View Completed
            </button>
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
            <button class="btn save-button" id="loadingConfirm">
                <i class="fas fa-check"></i>
                OK
            </button>
        </div>
    </div>

</div>

<div id="modalSearchCaptions" class="vtex-modal">

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

        <div style="overflow-x: hidden" class="vspt-table-div">
            <table id="captionsTbl" class="table vspt-table hover compact"></table>
        </div>

        <!--        <div id="captionResult">-->
        <!--            <table id="captionsTbl" class="display" style="width: 100% !important"></table>-->
        <!--        </div>-->

        <div style="text-align: right" class="mt-2 col-auto justify-content-end align-items-end pr-0">
            <button class="btn btn-sm btn-info" id="capSrcClear">
                Clear
            </button>


            <button class="btn btn-sm btn-secondary" id="capSrcClose">
                Close
            </button>
        </div>
    </div>

</div>

<div id="shortcutsModal" class="vtex-modal">

    <!-- Modal content -->
    <div class="vtex-modal-content">
        <h2><i class="fas fa-star" style="color: #f2b01e"></i> User Shortcuts</h2>
        <p><i>Your shortcuts are saved to your user account.</i></p>

        <div style="overflow-x: hidden" class="vspt-table-div">
            <table id="shortcuts-tbl" class="table vspt-table hover compact"></table>
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

<?php include_once "data/parts/footer.php" ?>
</body>

</html>
