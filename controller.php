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
if ($_SESSION['role'] != "1") {
//User is a System Administrator ONLY
    ob_start();
    header('Location: '."accessdenied.php");
    ob_end_flush();
    die();
}
?>

<html>

<head>
    <title>vScription Controller Downloads</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link href="data/css/controller.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>
    <script src="data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>
    <link href='data/fontawesome/css/all.css' type='text/css' rel='stylesheet'/>

</head>

<body>


<div id="container" style="width: 100%">
    <div class="form-style-5">

        <table id="header-tbl">
            <tr>



                <td id="navbtn" align="left" colspan="1">
                    <!--                        Logged in as: --><?php //echo $_SESSION['uEmail']?><!-- |-->
                    <!--                    </div>-->

                    <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
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
                    <legend class="page-title">Transcribe Controller Downloads</legend>
                </td>
                <!--<td align="right" rowspan="2" id="fix-td">

                    </td>-->

                <td width="300px">
                    <img src="data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
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
                <p>vScription Transcribe Controller specially made for vScription Typists, It allows to control transcribe player using foot control.</p>
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
                            cross platform Jar <i>(Requires Java > 8 installed)</i>
                        </td>
                        <td>
                            2.83 MB
                        </td>
                        <td>
                            <a href="/controller_app/jars/controller_v1.4.jar">controller_v1.4.jar</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Windows Bundle <i>(JRE included)</i>
                        </td>
                        <td>
                            37.4 MB
                        </td>
                        <td>
                            <a href="/controller_app/windows/controller_v1.4.zip">controller_v1.4.zip</a>
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
                            Chrome
                        </td>
                        <td class="check">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">
                            Firefox
                        </td>
                        <td class="check">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">
                            Safari
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
                            Windows
                        </td>
                        <td class="check">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">
                            Mac OS
                        </td>
                        <td class="check">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">
                            Linux
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
                            <b>Hardware</b>
                        </th>
                    </tr>
                    <tr>
                        <td class="bold">
                            VEC Footpedal
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
                </table>

            </div>
        </div>


    </div>
</div>

</body>

</html>