<?php
//include('../data/parts/head.php');
require '../../api/vendor/autoload.php';
use Src\Enums\INTERNAL_PAGES;

$vtex_page = INTERNAL_PAGES::ADMIN_PANEL_INDEX;

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

<div class="container-fluid d-flex h-auto vspt-container-fluid">
    <div class="row w-100 h-100 vspt-container-fluid-row no-gutters" style="white-space: nowrap">

        <?php include_once "../data/parts/nav.php"?>

        <div class="vspt-page-container vspt-col-auto-fix">

            <div class="row">
                <div class="col">
                    <a class="logbar" href="../landing.php"><i class="fas fa-arrow-left"></i> Go back to landing page</a>
                </div>

                <div class="col-auto logbar">
                    Logged in as: <?php echo $_SESSION['uEmail'] ?> |
                    <!--                    </div>-->
                    <a class="logout" href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </div>

            <div class="row vspt-title-row no-gutters">
                <div class="col align-items-end d-flex">
                    <legend class="page-title mt-auto">
                        <span class="fas fa-user-shield fa-fw mr-3"></span>
                        Admin Panel
                    </legend>
                </div>
                <div class="col-auto">
                    <img src="../data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
                </div>
            </div>

            <div class="vtex-card contents">

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


<?php include_once "../data/parts/footer.php"?>
</body>

</html>
