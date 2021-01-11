<?php
//include('../data/parts/head.php');

require '../../api/vendor/autoload.php';
use Src\Enums\INTERNAL_PAGES;

include('../data/parts/session_settings.php');

require('../data/parts/ping.php');

if (!isset($_SESSION['loggedIn'])) {
    header('location:../logout.php');
    exit();
}
if (isset($_SESSION['counter'])) {
    unset($_SESSION['counter']);
}

//redirect to main
if (!isset($_SESSION['role']) || $_SESSION['role'] != "1") {
//User is a System Administrator ONLY
    ob_start();
    header('Location: ' . "../accessdenied.php");
    ob_end_flush();
    die();
}
$vtex_page = INTERNAL_PAGES::USERS;
?>

<html lang="en">

<head>
    <title>vScription Manage Users</title>
    <link rel="shortcut icon" type="image/png" href="../data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="../data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="../data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="../data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>
    <script src="../data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>
    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>

    <!--	Scroll Bar Dependencies    -->
    <script src="../data/scrollbar/jquery.nicescroll.js"></script>
    <!--	///// End of scrollbar   /////-->

    <!--  JQUERY  -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="../data/dialogues/jquery-confirm.min.css">
    <script src="../data/dialogues/jquery-confirm.min.js"></script>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.ui.position.js"></script>


    <!-- BOOTSTRAP -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

<!--    <script src="../data/custom-bootstrap-select/js/bootstrap.min.js"></script>-->

    <!-- BOOTSTRAP SELECT -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

    <!--  Datatables  -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>


    <!--  css  -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" crossorigin="anonymous">


<!--    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap.min.css"/>-->

    <script src="../data/scripts/users.min.js"></script>
    <link href="../data/css/users.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid d-flex h-auto vspt-container-fluid">
    <div class="row w-100 h-100 vspt-container-fluid-row no-gutters" style="white-space: nowrap">

        <?php include_once "../data/parts/nav.php"?>

        <div class="vspt-page-container vspt-col-auto-fix">

            <div class="row">
                <div class="col">
                    <a class="logbar" href="index.php"><i class="fas fa-arrow-left"></i> Go back to Admin Panel</a>
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
                        <i class="material-icons mdc-button__icon" aria-hidden="true">account_circle</i>
                        Users Management
                    </legend>
                </div>
                <div class="col-auto">
                    <img src="../data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
                </div>
            </div>

            <div class="vtex-card contents">

                <div class="vtex-top-bar">
                    <h2 class="users-tbl-title">Users List</h2>
                    <button class="mdc-button mdc-button--unelevated refresh-button" id="refresh_btn">
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">refresh</i>
                        <span class="mdc-button__label">Refresh</span>
                    </button>
                </div>

                <div style="overflow-x: hidden" class="vspt-table-div">
                    <table id="users-tbl" class="users-tbl table vspt-table hover compact"></table>
                </div>
            </div>

        </div>
    </div>
</div>


<!-- The Modal -->
<div id="modal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <h3 style="color: #1e79be" id="modalHeaderTitle"><i class="fas fa-user-plus"></i>&nbsp;Create New User</h3>

        <form method="post" id="createAccForm" class="createAccForm" target="_self">
            <div style="text-align: right">
                <fieldset class="vtex-fieldset enabled-radios" style="display: inline;text-align: center;">
                    <legend style="font-size: 18px">Enabled</legend>
                    <label class="vtex-jq-lbl-check" for="enabled-t"><i class="fas fa-check"></i></label>
                    <input class="radio-no-icon" type="radio" name="enabled" id="enabled-t" value="1">

                    <label class="vtex-jq-lbl-cross" for="enabled-f"><i class="fas fa-times"></i></label>
                    <input class="radio-no-icon" type="radio" name="enabled" id="enabled-f" value="0">
                </fieldset>
                <fieldset class="vtex-fieldset newsletter-radios" style="display: inline;text-align: center;">
                    <legend style="font-size: 18px">Newsletter</legend>
                    <label class="vtex-jq-lbl-check" for="newsletter-t"><i class="fas fa-check"></i></label>
                    <input class="radio-no-icon" type="radio" name="newsletter" id="newsletter-t" value="1">

                    <label class="vtex-jq-lbl-cross" for="newsletter-f"><i class="fas fa-times"></i></label>
                    <input class="radio-no-icon" type="radio" name="newsletter" id="newsletter-f" value="0">
                </fieldset>
            </div>

            <fieldset class="vtex-fieldset">
                <div class="retention-grid">
                    <label for="fname" class="vtex-form_lbl">
                        First Name
                        <input class="fname vtex-input" id="fname" name="first_name" type="text">
                    </label>

                    <label for="lname" class="vtex-form_lbl">
                        Last Name
                        <input class="lname vtex-input" id="lname" name="last_name" type="text">
                    </label>
                </div>
            </fieldset>

            <label for="email" class="vtex-form_lbl" style="width: 100%">
                Email
                <input class="email vtex-input" id="email" name="email" type="email" style="width: 100%">
            </label>

            <div class="country">
                <label>Country</label><br>
                <select id="country" name="country" class="country_select" data-width="250px">
                </select>
            </div>
            <br>
            <!--===================================================-->
            <div class="state" id="stateContainer">
                <!------------------------------------------------------>
                <label class="vtex-form_lbl state_input_lbl" id="stateInputLbl">
                    State
                    <input class="state_input vtex-input" id="stateInput" name="state" type="text">
                </label>
            </div>
            <!--===================================================-->
            <br>
            <div id="cityContainer" class="city-container">
                <label for="city" class="vtex-form_lbl city_lbl">
                    City
                    <input class="city-input vtex-input" id="cityInput" name="city" type="text">
                </label>
            </div>
            <!--===================================================-->

            <div class="modal-footer pb-0">
                <button class="mdc-button mdc-button--unelevated blue-btn" id="updateAccBtn" type="button" disabled>
                    <div class="mdc-button__ripple"></div>
                    <i class="fas fa-user-edit"></i>
                    <span class="mdc-button__label">&nbsp; Update</span>
                </button>

                <button class="mdc-button mdc-button--unelevated green-btn" id="createAccBtn" type="button" disabled>
                    <div class="mdc-button__ripple"></div>
                    <i class="fas fa-user-plus"></i>
                    <span class="mdc-button__label">&nbsp; Create</span>
                </button>

                <button class="mdc-button mdc-button--unelevated cancel-acc-button" id="closeAccModal" type="button">
                    <div class="mdc-button__ripple"></div>
                    <i class="fas fa-times"></i>
                    <span class="mdc-button__label">&nbsp; Cancel</span>
                </button>
            </div>

        </form>

    </div>
</div>

<?php include_once "../data/parts/footer.php"?>
</body>

</html>
