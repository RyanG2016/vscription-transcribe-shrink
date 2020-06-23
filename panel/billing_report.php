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
if ($_SESSION['role'] != "1") {
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

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <link href="../data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link href="../data/css/billing_rep.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="../data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="../data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>
    <script src="../data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>
<!--    <link href='../data/fontawesome/css/all.css' type='text/css' rel='stylesheet'/>-->
    <script src="../data/scripts/billing_report.min.js"></script>

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
                    <a class="logout" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </td>

            </tr>
            <tr class="spacer"></tr>
            <tr style="margin-top: 50px">
                <td class="title" align="left" width="450px">
                    <legend class="page-title">Billing Reports</legend>
                </td>
                <!--<td align="right" rowspan="2" id="fix-td">

                    </td>-->

                <td width="300px">
                    <img src="../data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
                </td>
            </tr>


        </table>

        <div class="grid-wrapper">

            <table class="grid-wrapper-tbl">
                <tr class="pad15">
                    <td class="toolbar">Sidebar</td>
                    <td class="flr">
                        <table>
                            <tr>
                                <td>
                                    <label for="startDate">Start Date</label><input id="startDate" type="text" contenteditable="false"/>
                                </td>
                                <td>
                                    <label for="endDate">End Date</label><input id="endDate" type="text" contenteditable="false"/>
                                </td>
                                <td  style="vertical-align: bottom">
                                    <button class="mdc-button mdc-button--raised tools-button" id="getReport">
                                        <div class="mdc-button__ripple"></div>
                                        <i class="material-icons mdc-button__icon" aria-hidden="true">text_fields</i>
                                        <span class="mdc-button__label">Retrieve Report</span>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="pad15">
                    <td style="vertical-align: top">
                        <button class="mdc-button mdc-button--raised tools-button">
                            <div class="mdc-button__ripple"></div>
                            <i class="material-icons mdc-button__icon" aria-hidden="true">picture_as_pdf</i>
                            <span class="mdc-button__label">PDF</span>
                        </button>
                    </td>
                    <td class="flr"><div class="billing-report-container"></div></td>
                </tr>
            </table>

        </div>


    </div>
</div>

</body>

</html>
