<?php
//include('../data/parts/head.php');

include('data/parts/session_settings.php');

require('data/parts/ping.php');

if(!isset($_SESSION['loggedIn']))
{
    header('location:logout.php');
    exit();
}
if(isset($_SESSION['counter']))
{
    unset($_SESSION['counter']);
}

// admin panel main

//redirect to main
/*if ($_SESSION['role'] != "1" && $_SESSION['role'] != "3") {
    ob_start();
    header('Location: '."accessdenied.php");
    ob_end_flush();
    die();
}*/
?>

<html lang="en">

<head>
    <title>vScription Pro Transcribe Downloads</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link href="data/css/controller.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>
    <script src="data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>
    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>

</head>

<body>


<div id="container" style="width: 100%">
    <div class="form-style-5">

        <table id="header-tbl">
            <tr>



                <td id="navbtn" align="left" colspan="1">
                    <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
                </td>



            </tr>
            <tr class="spacer"></tr>
            <tr style="margin-top: 50px">
                <td class="title" align="left" width="450px">
                    <legend class="page-title">vScription Pro Transcribe Downloads</legend>
                </td>
                <!--<td align="right" rowspan="2" id="fix-td">

                    </td>-->

                <td width="300px">
                    <img src="data/images/Logo_vScription_Transcribe.png" style="float:right;" width="300px"/>
                </td>
            </tr>


        </table>

        <div class="grid-wrapper">
            <!--<h2>Description</h2>
            vScription Transcribe Controller made specially to vScription Typists, It allows to control the transcribe player using a foot pedal.

            <h2>Compatibility</h2>-->

            <div class="item1 grid-headers"><h3>Downloads</h3></div>
            <div class="item2-header grid-headers"><h3>Description</h3></div>
            <div class="item2">
                <p>
                    Here you will find the vScription Apps used to connect local hardware devices (foot control, digital recorders, microphones etc) to various vScription modules as well as a compatibility matrix. If you don't see your digital recorder in the list you can still upload your files through the Job Upload page.
                </p>
            </div>
            <div class="item3">

                <table class="controller-downloads-tbl">
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
                           <strong>vScription Controller Windows Installer</strong></i><br>For USB Foot Control Support
                            Windows Installer <i>(JDK 14 included)</i>
                        </td>
                        <td>
                            164 MB
                        </td>
                        <td>
                            <a href="/controller_app/windows/vScriptionControllerWin_v1.8.exe">vScriptionControllerWin_v1.8.exe</a>
                        </td>
                    </tr>                    
                    <tr>
                        <td>
                            <strong>vScription Controller Cross Platform Jar</strong><i><br>(Requires <a href='www.java.com' target='blank'>Java</a> 8 installed)</i><br>For USB Foot Control Support
                        </td>
                        <td>
                            3.88 MB
                        </td>
                        <td>
                            <a href="/controller_app/jars/controller_v1.8.jar">vScriptionControllerCrossPlatform_v1.8.jar</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>vScription Upload Cross Platform Jar </strong><i><br>(Requires <a href='https://www.oracle.com/java/technologies/javase-jdk15-downloads.html' target='blank'>OpenJDK</a> > 14 installed)</i><br>For Digital Portable Automatic Uploads and 3rd Party Integrations
                        </td>
                        <td>
                            164 MB
                        </td>
                        <td>
                            <a href="/controller_app/jars/vScriptionUpload-v1.1.jar">vScriptionUpload-v1.1.jar</a>
                        </td>
                    </tr>

                    <!--         OLDER VERSIONS           -->
                    <tr>
                        <td colspan="3" style="text-align: center; background:#6c6c6c; color: white; font-size: 1rem">
                            <i>Older Versions</i>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <div class="item4-5-header grid-headers"><h3>Compatibility</h3></div>
            <div class="item4">
                <table class="compat-tbl">
                    <tr>
                        <th colspan="2" style="text-align: center; background: #1e79be; color: white">
                            <b>Browsers</b>
                        </th>
                    </tr>
                    <tr>
                        <td class="bold">
                            <i class="fab fa-chrome"></i> Chrome
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
                    </tr>
                    <tr>
                        <td class="bold">
                            <i class="fab fa-edge"></i> Edge Chromium
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
                    </tr>
                    <tr>
                        <td class="bold">
                            <i class="fab fa-safari"></i> Safari
                        </td>
                        <td class="check">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="item5">

                <table class="compat-tbl">
                    <tr>
                        <th colspan="2" style="text-align: center; background: #1e79be; color: white">
                            <b>Operating Systems</b>
                        </th>
                    </tr>
                    <tr>
                        <td class="bold">
                            <i class="fab fa-windows"></i> Windows
                        </td>
                        <td class="check">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">
                            <i class="fab fa-apple"></i> macOS
                        </td>
                        <td class="check">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">
                            <i class="fab fa-ubuntu"></i> Linux
                        </td>
                        <td class="check">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </td>
                    </tr>
                </table>

            </div>

            <div class="item6">

                <table class="compat-tbl">
                    <tr>
                        <th colspan="2" style="text-align: center; background: #1e79be; color: white">
                            <b>USB Foot Controls</b>
                        </th>
                    </tr>
                    <tr>
                        <td class="bold">
                            VEC IN-USB
                        </td>
                        <td class="check">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">
                            VEC IN-USB2
                        </td>
                        <td class="check">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">
                            VEC IN-USB3
                        </td>
                        <td class="check">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">
                            Philips ACC2330
                        </td>
                        <td class="check">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">
                            Philips ACC2320
                        </td>
                        <td class="check">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </td>
                    </tr>
                </table>

            </div>
            <div class="item7">
            <table class="compat-tbl">
                <tr>
                    <th colspan="2" style="text-align: center; background: #1e79be; color: white">
                        <b>Digital Recorders</b>
                    </th>
                </tr>
                <tr>
                    <td class="bold">
                        Philips DPM8000/8100
                    </td>
                    <td class="check">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </td>
                </tr>
                <tr>
                    <td class="bold">
                        Philips LFH9500/9600
                    </td>
                    <td class="check">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </td>
                </tr>
                <tr>
                    <td class="bold">
                        Olympus DS-9000/9500
                    </td>
                    <td class="check">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </td>
                </tr>
                <tr>
                    <td class="bold">
                        Olympus DS-3500/7000
                    </td>
                    <td class="check">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </td>
                </tr>
                <tr>
                    <td class="bold">
                        Olympus DS-5000/5500
                    </td>
                    <td class="check">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </td>
                </tr>
            </table>

            </div>
        </div>


    </div>
</div>

</body>

</html>
