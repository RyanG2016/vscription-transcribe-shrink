<?php
//include('../data/parts/head.php');
$vtex_page = 3;

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
    <title>vScription Admin Panel</title>
    <link rel="shortcut icon" type="image/png" href="../data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="../data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="../data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="../data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>
    <script src="../data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>
    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <link href="../data/css/admin_panel.css" rel="stylesheet">
</head>

<body>
<?php include_once "../data/parts/nav.php" ?>

<div id="container" style="width: 100%">
    <div class="form-style-5">

        <table id="header-tbl">
            <tr>
                <td id="navbtn" align="left" colspan="1">

                    <a class="logout" href="../landing.php"><i class="fas fa-arrow-left"></i> Go back to landing page</a>
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
                    <legend class="page-title">Admin Panel</legend>
                </td>
                <!--<td align="right" rowspan="2" id="fix-td">

                    </td>-->

                <td width="300px">
                    <img src="../data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
                </td>
            </tr>


        </table>

        <div class="root">
            <div class="nav-bar">

                <div class="vtex-card nav-header first">
                    QUICK TOOLS
                </div>
                <div class="nav-btns-div">
                    <button class="mdc-button mdc-button--outlined tools-button" onclick="location.href='admin_tools.php'">
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">vpn_key</i>
                        <span class="mdc-button__label">Admin Tools</span>
                    </button>

                    <button class="mdc-button mdc-button--outlined tools-button" onclick="location.href='links.php'" disabled>
                        <div class="mdc-button__ripple"></div>
                        <i class="fas fa-link"></i>
                        <span class="mdc-button__label">Useful Links</span>
                    </button>

                    <div class="vtex-card nav-header">
                        REPORTS
                    </div>

                    <button class="mdc-button mdc-button--outlined tools-button" onclick="location.href='billing_report.php'">
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">attach_money</i>
                        <span class="mdc-button__label">Billing Reports</span>
                    </button>
                    <button class="mdc-button mdc-button--outlined tools-button" onclick="location.href='typist_report.php'">
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">text_fields</i>
                        <span class="mdc-button__label">Typist Reports</span>
                    </button>

                    <div class="vtex-card nav-header">
                        MANAGEMENT
                    </div>
                    <button class="mdc-button mdc-button--outlined tools-button" onclick="location.href='accounts.php'">
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">admin_panel_settings</i>
                        <span class="mdc-button__label">Manage Orgs</span>
                    </button>
                    <button class="mdc-button mdc-button--outlined tools-button" onclick="location.href='users.php'">
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">account_circle</i>
                        <span class="mdc-button__label">Manage Users</span>
                    </button>
                </div>

            </div>
            <div class="vtex-card contents first">

                <!--        CONTENTS GOES HERE        -->

                <table class="welcome">
                    <tr>
                        <td rowspan="2">
                            <i class="material-icons mdc-button__icon welcome-icon" aria-hidden="true">format_quote</i>
                        </td>
                        <td rowspan="1" style="font-size: 1.6rem;">
                            <span style="vertical-align: top"> Welcome back, <?php echo $_SESSION["fname"]?>!</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 1rem; font-style: italic; color: dimgrey">
                            <span style="vertical-align: bottom">Here you can find various tools to help you manage the website.</span>
                        </td>
                    </tr>
                </table>

            </div>
        </div>


    </div>
</div>

</body>

</html>
