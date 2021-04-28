<?php
//include('../data/parts/head.php');
require '../api/vendor/autoload.php';

use Src\Enums\INTERNAL_PAGES;

$vtex_page = INTERNAL_PAGES::VS_DOWNLOADS;
include('data/parts/head.php');

?>

<html lang="en">

<head>
    <title>vScription Pro Transcribe Downloads</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
    <!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>-->
    <!--    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">-->
    <!--    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">-->
    <!--    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>-->
    <!--    <script src="data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>-->
    <!--    <script src="data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>-->

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.0/moment.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
            integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV"
            crossorigin="anonymous"></script>

    <!--    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>-->
    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>


    <!--  Datatables  -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>


    <!--  css  -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css"
          crossorigin="anonymous">

    <!--  MDC Components  -->
    <!--    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">-->
    <!--    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">-->
    <!--    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>-->
    <!--    <script src="data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>-->

    <link href="data/css/downloads-controller.css" rel="stylesheet">

</head>

<body>
<div class="container-fluid d-flex h-auto vspt-container-fluid">
    <div class="row w-100 h-100 vspt-container-fluid-row no-gutters">

        <?php include_once "data/parts/nav.php" ?>

        <div class="vspt-page-container vspt-col-auto-fix">

            <div class="row">
                <div class="col">
                    <a class="logbar" href="/"><i class="fas fa-arrow-left"></i> home</a>
                </div>
            </div>

            <div class="row vspt-title-row no-gutters">
                <div class="col align-items-end d-flex">
                    <legend class="page-title mt-auto">
                        <i class="fad fa-download"></i> vScription Transcribe Downloads
                    </legend>
                </div>
                <div class="col-auto">
                    <img src="data/images/Logo_vScription_Transcribe.png" width="300px"/>
                </div>
            </div>

            <div class="vtex-card contents">

                <!--        CONTENTS GOES HERE        -->

                <p class="text-muted">Here you will find the vScription Apps used to connect local hardware devices
                    (foot control, digital recorders, microphones etc) to various vScription modules as well as a
                    compatibility matrix. If you don't see your digital recorder in the list you can still upload your
                    files through the Job Upload page.</p>
                <hr>
                <div class="form-row justify-content-center">
                    <div class="col vspt-table-div mb-4">
                        <h5>Downloads</h5>
                        <table id="downloadsTbl" class="table vspt-table hover compact">
<!--                        <table id="downloadsTbl" class="table compact table-hover table-bordered">-->
                            <thead>
                                <tr>
                                    <th>
                                        Product / File Description
                                    </th>
                                    <th>
                                        File Size
                                    </th>
                                    <th>
                                        Download
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <strong>vScription Controller Windows Installer</strong></i><br><span
                                            style='font-size:.7rem' ;>For USB Foot Control Support
                            Windows Installer <i>(JDK 14 included)</span>
                                </td>
                                <td>
                                    164 MB
                                </td>
                                <td>
                                    <a href="/controller_app/controller/windows/vScriptionControllerWin_v1.8.exe">vScriptionControllerWin_v1.8.exe</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>vScription Controller Cross Platform Jar</strong><i><br><span
                                                style='font-size:.8rem' ;>(Requires <a href='https://java.com'
                                                                                       target='blank'>Java</a> 8 installed)</i><br></span>
                                    <span style='font-size:.7rem'>For USB Foot Control Support</span>
                                </td>
                                <td>
                                    3.88 MB
                                </td>
                                <td>
                                    <a href="/controller_app/controller/cross_platform/vScriptionControllerCrossPlatform_v1.8.jar">vScriptionControllerCrossPlatform_v1.8.jar</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>vScription Upload Windows Installer </strong><i><br><span
                                            style='font-size:.7rem' ;>For Digital Portable Automatic Uploads and 3rd Party Integrations</span>
                                </td>
                                <td>
                                    174 MB
                                </td>
                                <td>
                                    <a href="/controller_app/upload_app/windows/vScription_upload_setup_v1.1.exe">vScription_upload_setup_v1.1.exe</a>
                                </td>
                            </tr>                            
                            <tr>
                                <td>
                                    <strong>vScription Upload Cross Platform Jar </strong><i><br><span
                                                style='font-size:.8rem' ;>(Requires <a
                                                    href='https://www.oracle.com/java/technologies/javase-jdk15-downloads.html'
                                                    target='blank'>OpenJDK</a> > 14 installed)</i><br></span><span
                                            style='font-size:.7rem' ;>For Digital Portable Automatic Uploads and 3rd Party Integrations</span>
                                </td>
                                <td>
                                    5 MB
                                </td>
                                <td>
                                    <a href="/controller_app/upload_app/cross_platform/vScriptionUpload.zip">vScriptionUpload-v1.1.jar</a>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                    <div class="col">
                        <h5>Compatibility</h5>
                        <table class="table vspt-table hover compact" id="compatTbl">
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align: center; background: #1e79be; color: white">
                                        <b>Browsers</b>
                                    </th>
                                    <th colspan="2" style="text-align: center; background: #1e79be; color: white">
                                        <b>Operating Systems</b>
                                    </th>
                                    <th colspan="2" style="text-align: center; background: #1e79be; color: white">
                                        <b>USB Foot Controls</b>
                                    </th>
                                    <th colspan="2" style="text-align: center; background: #1e79be; color: white">
                                        <b>Digital Recorders</b>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="bold">
                                        <i class="fab fa-chrome"></i> Chrome
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>
                                    <!---->
                                    <td class="bold">
                                        <i class="fab fa-windows"></i> Windows
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>

<!--                                    -->
                                    <td class="bold">
                                        VEC IN-USB
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>
<!--                                    -->

                                    <td class="bold">
                                        Philips DPM8000/8100
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="bold">
                                        <i class="fab fa-firefox-browser"></i> Firefox
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>
                                    <!---->
                                    <td class="bold">
                                        <i class="fab fa-apple"></i> macOS
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>
<!--                                    -->
                                    <td class="bold">
                                        VEC IN-USB2
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>
<!--                                    -->
                                    <td class="bold">
                                        Philips LFH9500/9600
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="bold">
                                        <i class="fab fa-edge"></i> Edge Chromium
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>

<!--                                    -->

                                    <td class="bold">
                                        <i class="fab fa-ubuntu"></i> Linux
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>
<!--                                    -->

                                    <td class="bold">
                                        VEC IN-USB3
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>
<!--                                    -->
                                    <td class="bold">
                                        Olympus DS-9000/9500
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="bold">
                                        <i class="fab fa-edge-legacy"></i> Edge Legacy
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </td>
<!--                                    -->
                                    <td colspan="4"></td>
                                    <td class="bold">
                                        Philips ACC2330
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bold">
                                        <i class="fab fa-safari"></i> Safari
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </td>
<!--
-->

<!--                                    -->
                                    <td colspan="4"></td>
                                    <td class="bold">
                                        Olympus DS-3500/7000
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>
                                </tr>
                            <tr>
                                <td colspan="6"></td>
                                <td class="bold">
                                    Olympus DS-5000/5500
                                </td>
                                <td class="check">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </td>
                            </tr>

                                <tr>
                                <td colspan="6"></td>

                                    <td class="bold">
                                        Philips ACC2320
                                    </td>
                                    <td class="check">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>
                            </tr>
                            </tbody>

                        </table>

                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<?php include_once "data/parts/footer.php" ?>

</body>

</html>
