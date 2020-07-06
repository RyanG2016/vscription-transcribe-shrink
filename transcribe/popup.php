<?php

/*if(!isset($_POST) || !isset($_POST['src']))
{
    header("location: index.php");
    exit();
}*/

?>

<!DOCTYPE html>

<?php
include('data/parts/head.php');
include ('data/parts/constants.php');

// TODO RE ENABLE
/*if(!isset($_POST) || !isset($_POST['src']))
{
    header("location: index.php");
    exit();
}*/

?>

<html lang="en">

<head>

    <?php include_once("gaTrackingCode.php");?>
    <!--  $this related  -->
    <script src="data/scripts/parts/constants.js" type="text/javascript"></script>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
    <title>vScription Compact View</title>

    <!--	jQuery 	-->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>


    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="data/thirdparty/scripts/moment.js"></script>

    <!--	Tooltip 	-->
    <link rel="stylesheet" type="text/css" href="data/tooltipster/css/tooltipster.bundle.min.css" />
    <link rel="stylesheet" type="text/css" href="data/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-punk.min.css" />
    <script type="text/javascript" src="data/tooltipster/js/tooltipster.bundle.min.js"></script>

    <!--	Scroll Bar Dependencies    -->

    <script src="data/scrollbar/jquery.nicescroll.js"></script>
    <!--	///// End of scrollbar depdns   /////-->

    <!--  $this related  -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <link href='data/css/popup.css' type='text/css' rel='stylesheet' />
    <script src="data/scripts/popup.min.js?v=1" type="text/javascript"></script>


    <!--	Able Player dependencies   -->
    <!--    CSS     -->
    <link href='ableplayer/styles/ableplayer.css' type='text/css' rel='stylesheet' />
    <!-- JavaScript -->
    <script src="ableplayer/thirdparty/js.cookie.js"></script>
    <script src="ableplayer/build/ableplayer.js"></script>
    <!--	///// End of Able Player deps   /////-->

    <link rel="stylesheet" href="data/main/jquery-ui.css">
    <script src="data/main/jquery-ui.js"></script>
    <link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">
    <script src="data/dialogues/jquery-confirm.min.js"></script>

</head>

<body>
<div id="updated_version_bar">There is a newer version (v<span></span>) of the vScription Transcribe Controller available -> <a href="" target="_blank">download</a></div>

<div class="container">
    <div style="text-align: right">

    </div>
    <div class="title" id="title">

        <table>
            <tr>
                <td rowspan="2"><img class="logo" src="data/images/Logo_only.png" alt="vScription Logo"></td>
                <td class="controller-status-holder">
                    <span class="controller-status" id="statusTxt">
                        <i>connecting to controller please wait...</i>
                    </span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="title-text-holder">
                        <span class="jobNo" id="jobNo"></span>
                         -
                        <span id="author" class="author"></span>
                         -
                        <span id="jobType" class="jobType"></span>
                    </span>
                    <span class="title-default-text">
                        Transcribe compact view
                    </span>
                </td>
            </tr>

        </table>

    </div>
    <div class="toolbar">
    <span class="loaded">
        <button class="mdc-button mdc-button--unelevated save-button" id="saveBtn" type="button" name="saveBtn">
            <div class="mdc-button__ripple"></div>
            <i class="material-icons mdc-button__icon" aria-hidden="true">save</i>
            <span class="mdc-button__label">Complete</span>
        </button>

        <button class="mdc-button mdc-button--unelevated suspend-button" id="suspendBtn" type="button" name="suspendBtn">
            <div class="mdc-button__ripple"></div>
            <i class="material-icons mdc-button__icon" aria-hidden="true">pause_circle_outline</i>
            <span class="mdc-button__label">Suspend</span>
        </button>

        <button class="mdc-button mdc-button--unelevated discard-button" id="discardBtn" name="discardBtn" type="button">
            <div class="mdc-button__ripple"></div>
            <i class="material-icons mdc-button__icon" aria-hidden="true">clear</i>
            <span class="mdc-button__label">Discard</span>
        </button>
    </span>
        <span class="not-loaded">
        <button class="mdc-button mdc-button--unelevated switch-back-button" id="switchBackBtn" name="switchBackBtn" type="button">
            <div class="mdc-button__ripple"></div>
            <i class="material-icons mdc-button__icon" aria-hidden="true">arrow_back</i>
            <span class="mdc-button__label">back to full view</span>
        </button>
        <button class="mdc-button mdc-button--unelevated load-button" id="loadBtn" name="loadBtn" type="button">
            <div class="mdc-button__ripple"></div>
            <i class="material-icons mdc-button__icon" aria-hidden="true">backup</i>
            <span class="mdc-button__label">Load</span>
        </button>
    </span>
    </div>
    <div id="audio-td" style="width: 450px">
        <audio id="audio1" width="450" data-able-player preload="auto" data-seek-interval="2" >
        </audio>
    </div>



    <div class="overlay">
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>
<!--    <form class="validate-form" method="post" name="form" data-persist="garlic" id="form" enctype="multipart/form-data">-->
<!--    </form>-->
</div>

<!-- The Modal -->
<div id="modalLoading" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
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

</body>


</html>
