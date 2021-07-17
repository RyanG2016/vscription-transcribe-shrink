<?php
//include('../data/parts/head.php');
require __DIR__ . '/../../api/bootstrap.php';
use Src\Enums\INTERNAL_PAGES;

$vtex_page = INTERNAL_PAGES::ADMIN_PANEL_INDEX;

include('../data/parts/head.php');

// admin panel main

//redirect to main
if (!isset($_SESSION['role']) || $_SESSION['role'] != "1") {
//User is a System Administrator ONLY
    ob_start();
    header('Location: '."../index.php");
    ob_end_flush();
    die();
}
?>

<html lang="en">

<head>
    <title>vScription Admin Panel</title>
    <link rel="shortcut icon" type="image/png" href="../data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="../data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="../data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="../data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>
    <script src="../data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>
    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <link href="../data/css/admin_panel.css" rel="stylesheet">
    <script src="../data/scripts/admin_panel.min.js" type="text/javascript"></script>
<!--    <script src="../data/scripts/admin_panel.min.js" type="text/javascript"></script>-->

</head>

<body>

<div class="container-fluid h-100 vspt-container-fluid">
        <!--        <div class="w-100 h-100 d-flex flex-nowrap vspt-container-fluid-row">-->
        <div class="vspt-container-fluid-row d-flex">


            <?php include_once "../data/parts/nav.php"?>

        <div class="vspt-page-container">

           <!-- <div class="row">
                <div class="col">
                    <a class="logbar" href="../landing.php"><i class="fas fa-arrow-left"></i> Go back to landing page</a>
                </div>


            </div>-->

            <div class="row vspt-title-row no-gutters">
                <div class="col align-items-end d-flex">
                    <legend class="page-title mt-auto">
                        <span class="fas fa-user-shield fa-fw mr-3"></span>
                        Admin Panel
                    </legend>
                </div>
                <div class="col-auto">
                    <img src="../data/images/Logo_vScription_Transcribe.png" width="300px"/>
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

                <h2 class="mdc-typography--headline4">Charts</h2>
                <div class="row no-gutters">
                    <div class="pie-container">
                        <h3 class="text-center">Files</h3>
                        <canvas id="filesChart"></canvas>
                    </div>

                    <div class="pie-container">
                        <h3 class="text-center">SR Queue</h3>
                        <canvas id="srqChart"></canvas>
                    </div>

                    <div class="col"></div>

                    <div class="border-left col-auto pl-3 pr-3"> 

                        <?php

//                        echo 'res: '.   getenv('REVAI_ACCESS_TOKEN')

                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://api.rev.ai/speechtotext/v1/account',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                            CURLOPT_HTTPHEADER => array(
                                'Authorization: Bearer ' .  getenv('REVAI_ACCESS_TOKEN')
//                                'Authorization: Bearer 02hoK6ac6Ar7wJHXuEY8K88sXH8e5879E3sAHeA4dmeejnaEAW9WfPtUEDtk4BCX4FjHhAr6OXWepMPnR2a_yt7UqLHvE'

                            ),
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);
//                        echo $response;
                        $revaiData = json_decode($response, true);
//                        echo json_decode($response, true)["balance_seconds"] . " mins";
                        ?>

                        <div class="row">
                            <div class="col-auto">
                                <img src="../data/images/revai64.png" width="44"/>
                            </div>
                            <div class="col">
                                <div class="row">
                                    <b> <?php echo $revaiData["email"] ?> </b>
                                </div>

                                <div class="row align-bottom">
                                    <?php echo number_format($revaiData["balance_seconds"]) . "&nbsp;<b> mins</b>" ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>



            </div>

        </div>
    </div>
</div>


<?php include_once "../data/parts/footer.php"?>
</body>

</html>
