<?php
//include('../data/parts/head.php');

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
?>

<html>

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

    <!--  Data table Jquery helping libs  -->
    <link rel="stylesheet" type="text/css" href="../data/libs/DataTables/datatables.css"/>
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/4.0.0/material-components-web.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.material.min.css"/>
    <script type="text/javascript" src="../data/libs/DataTables/datatables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.material.min.js"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="../data/dialogues/jquery-confirm.min.css">
    <script src="../data/dialogues/jquery-confirm.min.js"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.ui.position.js"></script>

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="../data/custom-bootstrap-select/css/bootstrap.css">
    <script src="../data/custom-bootstrap-select/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>


    <script src="../data/scripts/users.min.js"></script>
    <link href="../data/css/users.css" rel="stylesheet">
</head>

<body>


<div id="container" style="width: 100%">
    <div class="form-style-5">

        <table id="header-tbl">
            <tr>
                <td id="navbtn" align="left" colspan="1">
                    <a class="logout" href="index.php"><i class="fas fa-arrow-left"></i> Go back to Admin Panel</a>
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

                    <legend class="page-title">
                        <i class="material-icons mdc-button__icon" aria-hidden="true">account_circle</i>
                        Users Management
                    </legend>
                </td>
                <!--<td align="right" rowspan="2" id="fix-td">

                    </td>-->

                <td width="300px">
                    <img src="../data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
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
                        <i class="fas fa-user-plus"></i>
                        <span class="mdc-button__label">&nbsp;Add User</span>
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
                    <h2 class="users-tbl-title">Users List</h2>
                    <button class="mdc-button mdc-button--unelevated refresh-button" id="refresh_btn">
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">refresh</i>
                        <span class="mdc-button__label">Refresh</span>
                    </button>
                </div>


                <!--        CONTENTS GOES HERE        -->
                <table id="users-tbl" class="users-tbl" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Country</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Status</th>
                        <th>Enabled</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>


    </div>
</div>

<!-- The Modal -->
<div id="modal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <h2 style="color: #1e79be" id="modalHeaderTitle"><i class="fas fa-user-plus"></i>&nbsp;Create New User</h2>

        <form method="post" id="createAccForm" class="createAccForm" target="_self">
            <!--            <label for="enabled" class="vtex-form_lbl">Enabled</label>-->
            <!--            <input class="enabled vtex-input" type="text">-->
            <div style="text-align: right">
                <fieldset class="vtex-fieldset enabled-radios" style="display: inline;text-align: center;">
                    <legend>Enabled</legend>
                    <label class="vtex-jq-lbl-check" for="enabled-t"><i class="fas fa-check"></i></label>
                    <input class="radio-no-icon" type="radio" name="enabled" id="enabled-t" value="1">

                    <label class="vtex-jq-lbl-cross" for="enabled-f"><i class="fas fa-times"></i></label>
                    <input class="radio-no-icon" type="radio" name="enabled" id="enabled-f" value="0">
                </fieldset>
                <fieldset class="vtex-fieldset newsletter-radios" style="display: inline;text-align: center;">
                    <legend>Newsletter</legend>
                    <label class="vtex-jq-lbl-check" for="newsletter-t"><i class="fas fa-check"></i></label>
                    <input class="radio-no-icon" type="radio" name="newsletter" id="newsletter-t" value="1">

                    <label class="vtex-jq-lbl-cross" for="newsletter-f"><i class="fas fa-times"></i></label>
                    <input class="radio-no-icon" type="radio" name="newsletter" id="newsletter-f" value="0">
                </fieldset>
            </div>

            <fieldset class="vtex-fieldset">
                <!--                <legend>&nbsp;Retention&nbsp;</legend>-->
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

            <label for="email" class="vtex-form_lbl">
                Email
                <input class="email vtex-input" id="email" name="email" type="email">
            </label>

            <div class="country">
                <label>Country</label><br>
                <select id="country" name="country_id" class="country_select" data-width="250px">
                </select>
            </div>
            <br>
            <!--===================================================-->
            <div class="state" id="stateContainer">
                <label id="stateBoxLbl" class="stateLbl">State<br>
                    <select id="stateBox" name="state_id" class="state_select" data-width="250px">
                    </select>
                </label>
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

            <div class="modal-footer">
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
</body>

</html>
