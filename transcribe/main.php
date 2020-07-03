<!DOCTYPE html>
<html lang="en">

<?php
include('data/parts/head.php');
include('rtf3/src/HtmlToRtf.php');
include('data/parts/constants.php');
include_once("gaTrackingCode.php");

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] != "2" && $_SESSION['role'] != "1") {
        header('location:accessdenied.php');
    }
}
else {
        header('location:accessdenied.php');
}

?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <noscript>
        <meta http-equiv="refresh" content="0;url=noscript.php">
    </noscript>
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!--  MDC Components  -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>



    <link rel="stylesheet" href="data/css/main.css">
    <script src="data/scripts/main.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;1,300&display=swap"
          rel="stylesheet">
    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>
<!--    <link rel="stylesheet" href="data/css/vs-style.css">-->

    <title>vScription Transcribe Pro Dictation Upload</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
    <link rel="stylesheet" href="data/css/job_lister_form_5.css">

    <link rel="stylesheet" href="data/main/jquery-ui.css">
    <script src="data/main/jquery.js"></script>
    <script src="data/main/garlic.js"></script>
    <script src="data/main/jquery-ui.js"></script>

    <!--	Scroll Bar Dependencies    -->

    <script src="data/scrollbar/jquery.nicescroll.js"></script>
    <!--	///// End of scrollbar depdns   /////-->


    <link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">
    <script src="data/dialogues/jquery-confirm.min.js"></script>


    <!--    <link rel="stylesheet" href="data/thirdparty/scripts/css/tablesort.css">-->
    <link rel="stylesheet" href="data/thirdparty/scripts/css/styles.css">
    <!--    <script src="data/thirdparty/scripts/tablesort.js"></script>-->

    <!--  Data table Jquery helping libs  -->
    <link rel="stylesheet" type="text/css" href="data/libs/DataTables/datatables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/4.0.0/material-components-web.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.material.min.css"/>
    <script type="text/javascript" src="data/libs/DataTables/datatables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.material.min.js"></script>

    <!--	Tooltip 	-->
    <link rel="stylesheet" type="text/css" href="data/tooltipster/css/tooltipster.bundle.min.css" />
    <link rel="stylesheet" type="text/css" href="data/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-punk.min.css" />
    <script type="text/javascript" src="data/tooltipster/js/tooltipster.bundle.min.js"></script>

</head>

<body>

<div id="container" style="width: 100%">
    <div class="form-style-5">

        <table id="header-tbl">
            <tr>
                <td id="logbar" align="right" colspan="2">
                    Logged in as: <?php echo $_SESSION['uEmail'] ?> |

                    <a class="logout" href="logout.php">
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
                    <img src="data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
                </td>
            </tr>
        </table>


        <table class="data-tbl">
            <tr>
                <td colspan="1">
                    <h3 class="getList job_list_tbl_title">Jobs List</h3>
                </td>
                <td colspan="3" align="right">
                    <span class="top-links" id="help">
                        <a href="https://vscriptionpro.helpdocsonline.com/" target="_blank" title="">Need help</a>
                        <i class="far fa-question-circle"></i>
                    </span>
                    <button class="mdc-button mdc-button--unelevated foo-button" id="newupload_btn">
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">cloud_upload</i>
                        <span class="mdc-button__label">Upload Jobs</span>
                    </button>
                    <button class="mdc-button mdc-button--unelevated foo-button" id="refresh_btn"
                            >
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true"
                        >refresh</i
                        >
                        <span class="mdc-button__label">Refresh</span>
                    </button>
                </td>
            </tr>
            <tr>
                <td colspan="4">

                    <table id="jobs-tbl" class="display" style="width:100%">
                        <thead>
                        <tr>
                            <th>Job #</th>
                            <th>Author</th>
                            <th>Job Type</th>
                            <th>Date Dictated</th>
                            <th>Date Uploaded</th>
                            <th>Job Length</th>
                            <th>Job Status</th>
                            <th>Job Transcribed</th>
                            <th>Initial Download</th>
                            <th>Download</th>
                        </tr>
                        </thead>
                    </table>
                </td>
            </tr>
        </table>


    </div>
</div>

</body>

</html>
