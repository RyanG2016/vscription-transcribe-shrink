<?php

require '../../api/vendor/autoload.php';
use Src\Enums\INTERNAL_PAGES;


include('../data/parts/head.php');


//redirect to main
if (!isset($_SESSION['role']) || $_SESSION['role'] != "1") {
//User is a System or Client Administrator
    ob_start();
    header('Location: '."../index.php");
    ob_end_flush();
    die();
}
$vtex_page = INTERNAL_PAGES::ADMIN_TOOLS;

?>

<html lang="en">

<head>
    <title>vScription Admin Tools</title>
    <link rel="shortcut icon" type="image/png" href="../data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="../data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="../data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="../data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>
<!--    <script src="../data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>-->
    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>

    <!-- BOOTSTRAP -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
            integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV"
            crossorigin="anonymous"></script>

    <!--  css  -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">


    <script src="../data/scripts/admin_tools.min.js"></script>
    <link href="../data/css/admin_tools.css" rel="stylesheet">

</head>

<body>


<div class="container-fluid h-100 vspt-container-fluid">
        <!--        <div class="w-100 h-100 d-flex flex-nowrap vspt-container-fluid-row">-->
        <div class="vspt-container-fluid-row d-flex">

        <?php include_once "../data/parts/nav.php"?>

        <div class="vspt-page-container">

            <div class="row">
                <div class="col">
                    <a class="logbar" href="index.php"><i class="fas fa-arrow-left"></i> Go back to Admin Panel</a>
                </div>


            </div>

            <div class="row vspt-title-row no-gutters">
                <div class="col align-items-end d-flex">
                    <legend class="page-title mt-auto">
                        <i class="fas fa-tools"></i>
                        Admin Tools
                    </legend>
                </div>
                <div class="col-auto">
                    <img src="../data/images/Logo_vScription_Transcribe.png" width="300px"/>
                </div>
            </div>

            <div class="vtex-card contents">

                <label class="mdc-text-field mdc-text-field--outlined" id="pwd">
                    <input type="text" class="mdc-text-field__input" aria-labelledby="my-label-id">
                    <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                <span class="mdc-floating-label" id="my-label-id">Password</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
                </span>
                </label>

                <button class="mdc-button mdc-button--raised" id="hashPwd">
                    <div class="mdc-button__ripple"></div>
                    <i class="material-icons mdc-button__icon" aria-hidden="true"
                    >vpn_key</i
                    >
                    <span class="mdc-button__label">Hash Password</span>
                </button>

            </div>

        </div>
    </div>
</div>


<?php include_once "../data/parts/footer.php"?>
</body>

</html>
