<?php
//include('../data/parts/head.php');
$vtex_page = 4;
include('data/parts/session_settings.php');

require('data/parts/ping.php');

if (!isset($_SESSION['loggedIn'])) {
    header('location:../logout.php');
    exit();
}
if (isset($_SESSION['counter'])) {
    unset($_SESSION['counter']);
}

//redirect to main
if (!isset($_SESSION['role']) || $_SESSION['role'] != "2") {
//access is a System Administrator ONLY
    ob_start();
    header('Location: ' . "../accessdenied.php");
    ob_end_flush();
    die();
}

/*if( !isset($_POST["uid-access"]) || $_POST["uid-access"] <= 0) {
    header('Location: ' . "accesss.php");
    exit();
}

$uid = $_POST["uid-access"];*/
?>

<html lang="en">

<head>
    <title>vScription Manage Typists</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>
    <script src="data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>
    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>

    <!--	Scroll Bar Dependencies    -->
    <script src="data/scrollbar/jquery.nicescroll.js"></script>
    <!--	///// End of scrollbar   /////-->

    <!--  Data table Jquery helping libs  -->
    <link rel="stylesheet" type="text/css" href="data/libs/DataTables/datatables.css"/>
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/4.0.0/material-components-web.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.material.min.css"/>
    <script type="text/javascript" src="data/libs/DataTables/datatables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.material.min.js"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">
    <script src="data/dialogues/jquery-confirm.min.js"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.ui.position.js"></script>

    <!-- BOOTSTRAP -->
    <!-- BOOTSTRAP -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"
            integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg=="
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="data/css/custom-bootstrap-select.css" />

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>


    <script src="data/scripts/manage_typists.min.js"></script>
    <link href="data/css/manage_typists.css" rel="stylesheet">
</head>

<body>
<?php include_once "data/parts/nav.php" ?>

<div id="container" style="width: 100%">
    <div class="form-style-5">

        <table id="header-tbl">
            <tr>
                <td id="navbtn" align="left" colspan="1">
                    <a class="logout" href="main.php"><i class="fas fa-arrow-left"></i> Go back to Job Lister</a>
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

                    <legend class="page-title">
                        <i class="fas fa-keyboard"></i>
                        Manage Typists
                    </legend>
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
                    ACTIONS
                </div>
                <div class="nav-btns-div actions-btns">
                    <button class="mdc-button mdc-button--outlined tools-button" id="createAcc">
                        <div class="mdc-button__ripple"></div>
                        <i class="fas fa-envelope"></i>
                        <span class="mdc-button__label">&nbsp;Invite Typist</span>
                    </button>

                    <!--<div class="vtex-card nav-header">
                        Header 2
                    </div>

                    <button class="mdc-button mdc-button--outlined tools-button" >
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">attach_money</i>
                        <span class="mdc-button__label">Button 2</span>
                    </button>-->


                </div>

            </div>
            <div class="vtex-card contents first">

                <div class="vtex-top-bar">
                    <h2 class="accesss-tbl-title">Current Typists</h2>
                    <button class="mdc-button mdc-button--unelevated refresh-button" id="refresh_btn">
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">refresh</i>
                        <span class="mdc-button__label">Refresh</span>
                    </button>
                </div>


                <!--        CONTENTS GOES HERE        -->
                <table id="access-tbl" class="access-tbl w100" style="width:100%">
                    <thead>
                    <tr>
<!--                        <th>ID</th>-->
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>


    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="color: #1e79be" id="modalHeaderTitle">
                    <i class="fas fa-envelope-square"></i>&nbsp;Invite
                </h3>
                <!--                <h5 class="modal-title">Modal title</h5>-->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><span aria-hidden="true"><i class="fas fa-times"></i></span></span>
                </button>
            </div>
            <div class="modal-body pb-0">
                <!--<label>Typist Email</label>
                <input type="text" class="form-control" id="accNameTxt" aria-describedby="acc_name_help" placeholder="Choose a name for your account">
                <small id="acc_name_help" class="form-text text-muted">
                    Name must be less than 254 characters with no special characters.
                </small>
                <div class="valid-feedback">
                    Looks good!
                </div>
                <div class="invalid-feedback">
                    Please enter a valid name.
                </div>-->
<!--                <form method="post" id="createAccForm" class="createAccForm" target="_self" style="margin-block-end: unset;">-->

                    <!--<label for="email" class="vtex-form_lbl">
                        Email
                        <input class="email vtex-input" id="email" name="email" type="email">
                    </label>-->

                    <div class="account">
                        <label>Typist Email</label>
<!--                        <select id="accountBox" name="acc_id" class="account_select" data-width="250px">-->
<!--                        </select>-->
                        <select class="form-control show-tick" id="accountBox" data-container="body" data-dropup-auto="false" name="email">
                            <option selected>Loading...</option>
                        </select>
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                        <!--<div class="invalid-feedback">
                            Please enter a valid name.
                        </div>-->
                    </div>
                    <br>

                    <!--===================================================-->
                    <!--<div class="role">
                        <label id="role">Role<br>
                            <select id="roleBox" name="acc_role" data-width="250px">
                            </select>
                        </label>
                    </div>-->

                    <!--===================================================-->

                    <div class="modal-footer pr-0">

                        <button class="mdc-button mdc-button--unelevated cancel-acc-button" id="closeAccModal" type="button">
                            <div class="mdc-button__ripple"></div>
                            <i class="fas fa-times"></i>
                            <span class="mdc-button__label">&nbsp; Cancel</span>
                        </button>

                        <button class="mdc-button mdc-button--unelevated green-btn" id="sendInviteBtn" type="button">
                            <div class="mdc-button__ripple"></div>
                            <i class="fas fa-envelope"></i>
                            <span class="mdc-button__label">&nbsp; Send</span>
                        </button>
                    </div>

<!--                </form>-->
            </div>
<!--            <div class="modal-footer">-->
<!--                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>-->
<!--                <button type="button" class="btn btn-primary" id="createAdminAccBtn"><i class="fas fa-plus"></i> &nbsp;Create</button>-->
<!--            </div>-->
        </div>
    </div>
</div>

<div class="overlay" id="overlay" style="display: none">
    <div class="loading-overlay-text" id="loadingText">Please wait..</div>
    <div class="spinner">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
        <div class="bounce3"></div>
    </div>
</div>

</body>

</html>