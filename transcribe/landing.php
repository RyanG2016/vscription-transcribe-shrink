<?php
//include('../data/parts/head.php');

include('data/parts/session_settings.php');

require('data/parts/ping.php');

if (!isset($_SESSION['loggedIn'])) {
    header('location:../logout.php');
    exit();
}
if (isset($_SESSION['counter'])) {
    unset($_SESSION['counter']);
}

// User Setting
?>

<html>

<head>
    <title>vScription User Settings</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>
    <script src="data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>
    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>

    <!--    Jquery confirm  -->
    <link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">
    <script src="data/dialogues/jquery-confirm.min.js"></script>

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="data/custom-bootstrap-select/css/bootstrap.css">
    <script src="data/custom-bootstrap-select/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

    <?php
    ;
    ?>

    <script type="text/javascript">
        var roleIsset = <?php echo (!isset($_SESSION['role']) && !isset($_SESSION['accID']))?0:true ?>;
    </script>

    <link href="data/css/landing.css?v=2" rel="stylesheet">
    <script src="data/scripts/landing.min.js" type="text/javascript"></script>

</head>

<body>


<div id="container" style="width: 100%">
    <div class="form-style-5">

        <table id="header-tbl">
            <tr>
                <td id="navbtn" align="left" colspan="1">
                    <?php
                    if (isset($_SESSION['role'])) {
                        switch ($_SESSION['role']) {
                            case 1:
                                echo "<a class=\"logout\" href=\"panel/\"><i class=\"fas fa-arrow-left\"></i> Go to admin panel</a>";
                                break;

                            case 2:
                                echo "<a class=\"logout\" href=\"main.php\"><i class=\"fas fa-arrow-left\"></i> Go to job list</a>";
                                break;

                            case 3:
                                echo "<a class=\"logout\" href=\"transcribe.php\"><i class=\"fas fa-arrow-left\"></i> Go to transcribe</a>";
                                break;
                        }
                    }
                    ?>
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
                    <legend class="page-title"><i class="fas fa-user-cog"></i> User Settings</legend>
                </td>
                <!--<td align="right" rowspan="2" id="fix-td">

                    </td>-->

                <td width="300px">
                    <img src="data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
                </td>
            </tr>


        </table>

        <div class="root">
            <div class="nav-bar">

                <div class="vtex-card nav-header first">
                    Current Role
                </div>
                <div class="nav-btns-div">
                    <!--                    <button class="mdc-button mdc-button--outlined tools-button">-->
                    <!--                        <div class="mdc-button__ripple"></div>-->

                    <table>
                        <tr>
                            <td style="vertical-align: top;">
                                <i class="fas fa-keyboard tools-label" style="color: white;"></i>
                            </td>
                            <td style="vertical-align: top">
                                <span class="mdc-button tools-label" id="currentRole">
                                    <?php echo isset($_SESSION["role_desc"]) ? $_SESSION["role_desc"] : "None" ?>
                                </span>
                            </td>
                        </tr>
                    </table>

                    <!--                    </button>-->

                    <!--                    <button class="mdc-button mdc-button--outlined tools-button">-->
                    <!--                        <div class="mdc-button__ripple"></div>-->

                    <table>
                        <tr>
                            <td style="vertical-align: top">
                                <i class="fas fa-user-alt tools-label" style="color: white;"></i>
                            </td>
                            <td style="vertical-align: top">
                                <span class="mdc-button tools-label" id="currentAccountName">
                                    <?php echo isset($_SESSION["acc_name"]) ?
        //                                strlen($_SESSION["acc_name"]) > 21 ?
        //                                    substr($_SESSION["acc_name"], 0, 20) . ".." :
                                        $_SESSION["acc_name"]
                                        : "None" ?>
                                </span>
                            </td>
                        </tr>
                    </table>


                    <!--                    </button>-->

                </div>

                <?php
                if (isset($_SESSION["role"])) {
                    $rl = $_SESSION["role"];
                    if ($rl == 3) {
                        echo "<div class=\"vtex-card nav-header first\">
                                Navigation
                            </div>
                            <div class=\"nav-btns-div\">
                                <button class=\"mdc-button mdc-button--outlined tools-button\" onclick=\"location.href='transcribe.php'\">
                                    <div class=\"mdc-button__ripple\"></div>
                                    <i class=\"fas fa-angle-double-right\"></i>
                                        Go To Transcribe
                                    </span>
                                </button>
                            </div>";
                    } else if ($rl == 2) {
                        echo "<div class=\"vtex-card nav-header first\">
                                Navigation
                            </div>
                            <div class=\"nav-btns-div\">
                                <button class=\"mdc-button mdc-button--outlined tools-button\" onclick=\"location.href='main.php'\">
                                    <div class=\"mdc-button__ripple\"></div>
                                    <i class=\"fas fa-angle-double-right\"></i>
                                        Go To Job Lister
                                    </span>
                                </button>
                            </div>";
                    } else if ($rl == 1) {
                        echo "<div class=\"vtex-card nav-header first\">
                                Navigation
                            </div>
                            <div class=\"nav-btns-div\">
                                <button class=\"mdc-button mdc-button--outlined tools-button\" onclick=\"location.href='panel/'\">
                                    <div class=\"mdc-button__ripple\"></div>
                                    <i class=\"fas fa-angle-double-right\"></i>
                                        Go To Admin Panel
                                    </span>
                                </button>
                                
                                <button class=\"mdc-button mdc-button--outlined tools-button\" onclick=\"location.href='main.php'\">
                                    <div class=\"mdc-button__ripple\"></div>
                                    <i class=\"fas fa-angle-double-right\"></i>
                                        Go To Job Lister
                                    </span>
                                </button>
                                
                                <button class=\"mdc-button mdc-button--outlined tools-button\" onclick=\"location.href='transcribe.php'\">
                                    <div class=\"mdc-button__ripple\"></div>
                                    <i class=\"fas fa-angle-double-right\"></i>
                                        Go To Transcribe
                                    </span>
                                </button>
                                
                                
                            </div>";
                    }
                }
                ?>

                <div class="vtex-card nav-header first">Role Settings</div>
                <div class="nav-btns-div">
                    <button class="mdc-button mdc-button--outlined tools-button" id="changeRoleBtn">
                        <div class="mdc-button__ripple"></div>
                        <i class="fas fa-wrench"></i>
                        <span class="mdc-button__label">SWITCH ACCOUNT/ROLE</span>
                    </button>

                    <button class="mdc-button mdc-button--outlined tools-button" id="setDefaultRoleBtn">
                        <div class="mdc-button__ripple"></div>
                        <i class="fas fa-wrench"></i>
                        Set Default
                        </span>
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
                            <span style="vertical-align: top"> Welcome back, <?php echo $_SESSION["fname"] ?>!</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 1rem; font-style: italic; color: dimgrey">
                            <span style="vertical-align: bottom">Here you can choose your next job start by clicking change role from the sidebar.</span>
                            <!--                            <span style="vertical-align: bottom">Here you can find all your assigned work and data.</span>-->
                        </td>
                    </tr>
                </table>

            </div>
        </div>


    </div>
</div>

<!-- The Modal -->
<div id="modal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <h2 style="color: #1e79be" id="modalHeaderTitle">
            <i class="fas fa-wrench"></i>&nbsp;Change Role
        </h2>


        <input id="uidIn" name="uid" value="<?php echo $_SESSION['uid'] ?>" style="display: none">

        <form method="post" id="createAccForm" class="createAccForm" target="_self">

            <!--<label for="email" class="vtex-form_lbl">
                Email
                <input class="email vtex-input" id="email" name="email" type="email">
            </label>-->

            <div class="account">
                <label>Account</label><br>
                <select id="accountBox" name="acc_id" class="account_select" data-width="250px">
                </select>
            </div>
            <br>

            <!--===================================================-->
            <div class="role">
                <label id="role">Role<br>
                    <select id="roleBox" name="acc_role" data-width="250px">
                    </select>
                </label>
            </div>
            <!--===================================================-->

            <div class="modal-footer">
                <button class="mdc-button mdc-button--unelevated blue-btn" id="updateRoleBtn" type="button" disabled>
                    <div class="mdc-button__ripple"></div>
                    <i class="fas fa-user-edit"></i>
                    <span class="mdc-button__label">&nbsp; SET</span>
                </button>

                <button class="mdc-button mdc-button--unelevated cancel-acc-button" id="closeModalBtn" type="button">
                    <div class="mdc-button__ripple"></div>
                    <i class="fas fa-times"></i>
                    <span class="mdc-button__label">&nbsp; Cancel</span>
                </button>
            </div>

        </form>


    </div>
</div>

<div class="overlay" id="overlay">
    <div class="spinner">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
        <div class="bounce3"></div>
    </div>
</div>

</body>

</html>
