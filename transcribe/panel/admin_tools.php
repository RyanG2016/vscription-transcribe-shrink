<?php
include('../data/parts/head.php');


//redirect to main
if (!isset($_SESSION['role']) || $_SESSION['role'] != "1") {
//User is a System or Client Administrator
    ob_start();
    header('Location: '."../accessdenied.php");
    ob_end_flush();
    die();
}


?>

<html>

<head>
    <title>vScription Admin Tools</title>
    <link rel="shortcut icon" type="image/png" href="../data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="../data/scripts/admin_tools.min.js"></script>
    <link href="../data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link href="../data/css/admin_tools.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="../data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="../data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>
    <script src="../data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>
    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>

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
                    <legend class="page-title">Admin Tools</legend>
                </td>
                <!--<td align="right" rowspan="2" id="fix-td">

                    </td>-->

                <td width="300px">
                    <img src="../data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
                </td>
            </tr>


        </table>

        <div class="grid-wrapper">

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

</body>

</html>
