<?php
//include('../data/parts/head.php');

include('../data/parts/session_settings.php');

require('../data/parts/ping.php');

if(!isset($_SESSION['loggedIn']))
{
    header('location:../logout.php');
    exit();
}
if(isset($_SESSION['counter']))
{
    unset($_SESSION['counter']);
}

// admin panel main

//redirect to main
if (!isset($_SESSION['role']) || $_SESSION['role'] != "1") {
//User is a System Administrator ONLY
    ob_start();
    header('Location: '."../accessdenied.php");
    ob_end_flush();
    die();
}
?>

<html>

<head>
    <title>vScription Billing Reports</title>
    <link rel="shortcut icon" type="image/png" href="../data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <link href="../data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link href="../data/css/billing_rep.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="../data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="../data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>
    <script src="../data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>
<!--    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>-->
    <script src="../data/scripts/billing_report.min.js"></script>
    <script src="../data/thirdparty/scripts/html2pdf.bundle.min.js"></script>
    <link href="https://printjs-4de6.kxcdn.com/print.min.css" rel="stylesheet">
    <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>

</head>

<body>


<div id="container" style="width: 100%">
    <div class="form-style-5">

        <table id="header-tbl">
            <tr>



                <td id="navbtn" align="left" colspan="1">
                    <!--                        Logged in as: --><?php //echo $_SESSION['uEmail']?><!-- |-->
                    <!--                    </div>-->

                    <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Admin Panel</a>
                </td>

                <td id="logbar" align="right" colspan="1">
                    Logged in as: <?php echo $_SESSION['uEmail'] ?> |
                    <!--                    </div>-->
                    <a class="logout" href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </td>

            </tr>
            <tr class="spacer"></tr>
            <tr style="margin-top: 50px">
                <td class="title" align="left" width="450px">
                    <legend class="page-title"><i class="fas fa-file-invoice-dollar"></i> Client Billing Reports</legend>
                </td>
                <!--<td align="right" rowspan="2" id="fix-td">

                    </td>-->

                <td width="300px">
                    <img src="../data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
                </td>
            </tr>


        </table>

        <div class="grid-wrapper">
            <div class="start-date-item">
                <label for="startDate">Start Date</label><input id="startDate" type="text" contenteditable="false"/>
            </div>
            <div class="end-date-item">
                <label for="endDate">End Date</label><input id="endDate" type="text" contenteditable="false"/>
            </div>
            <div class="retrieve-item">
                <button class="mdc-button mdc-button--unelevated tools-button" id="getReport">
                    <div class="mdc-button__ripple"></div>
                    <i class="material-icons mdc-button__icon" aria-hidden="true">text_fields</i>
                    <span class="mdc-button__label">Retrieve Report</span>
                </button>
            </div>
            <div class="pdf-item">
                <button class="mdc-button mdc-button--unelevated tools-button" id="getPDF" disabled>
                    <div class="mdc-button__ripple"></div>
                    <i class="fas fa-file-pdf"></i>
                    <span class="mdc-button__label">&nbsp;PDF</span>
                </button>
            </div>
            <div class="print-item">
                <button class="mdc-button mdc-button--unelevated tools-button" id="getPrint" disabled>
                    <div class="mdc-button__ripple"></div>
                    <i class="fas fa-print"></i>
                    <span class="mdc-button__label">&nbsp;text</span>
                </button>
            </div>
            <div class="report-grid billing-report-container" id="printableReport">
<!--                <div class="billing-report-container"></div>-->
            </div>
        </div>


    </div>
</div>

</body>

</html>
