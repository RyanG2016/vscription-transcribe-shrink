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

            <div class="vtex-card contents w-100">
<!--                <div class="border-right col-auto pl-2 pr-2 stats-col">-->
                <div class=" stats-col">

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

                    <table class="table table-dark stats-tbl table-hover">
                        <tr>
                            <th>
                                <img src="../data/images/revai64.png" width="44"/>
                            </th>
                            <th colspan="2" style="vertical-align: middle">
                                <b> <?php echo $revaiData["email"] ?> </b>
                            </th>
                        </tr>

                        <tr class="vtex-revai-rem">
                            <td></td>
                            <td><?php echo number_format($revaiData["balance_seconds"]/60); ?></td>
                            <td><b> mins</b></td>
                        </tr>
                        <tr class="vtex-revai-rem">
                            <td></td>

                            <td><?php echo $revaiData["balance_seconds"] ?></td>
                            <td><b>secs</b></td>
                        </tr>

                        <tr class="bg-info">
                            <td colspan="3" class="text-center">General</td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                Total Files
                            </td>
                            <td id="totalFiles">
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                Total Orgs (enabled)
                            </td>
                            <td id="totalOrgs">
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                Sys Admin Access
                            </td>
                            <td id="totalSysAccess">
                            </td>
                        </tr>
                    </table>



                </div>

                <div class="panel-data">
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

                    </div>

                </div>



            </div>

        </div>
    </div>
</div>


<?php include_once "../data/parts/footer.php"?>
</body>

</html>
