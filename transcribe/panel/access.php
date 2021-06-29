<?php
//include('../data/parts/head.php');
require '../../api/vendor/autoload.php';
use Src\Enums\INTERNAL_PAGES;
$vtex_page = INTERNAL_PAGES::MANAGE_USER_ACCESS;

include('../data/parts/head.php');

//redirect to main
if (!isset($_SESSION['role']) || $_SESSION['role'] != "1") {
//access is a System Administrator ONLY
    ob_start();
    header('Location: ' . "../index.php");
    ob_end_flush();
    die();
}

if( !isset($_POST["uid-access"]) || $_POST["uid-access"] <= 0) {
    header('Location: ' . "users.php");
    exit();
}

$uid = $_POST["uid-access"];
?>

<html lang="en">

<head>
    <title>vScription Manage User Access</title>
    <link rel="shortcut icon" type="image/png" href="../data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="../data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="../data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="../data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>
    <script src="../data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>
    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>

    <!--	Scroll Bar Dependencies    -->
    <script src="../data/scrollbar/jquery.nicescroll.js"></script>
    <!--	///// End of scrollbar   /////-->

    <!--  Data table Jquery helping libs  -->
<!--    <link rel="stylesheet" type="text/css" href="../data/libs/DataTables/datatables.css"/>-->
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/4.0.0/material-components-web.min.css"/>
<!--    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.material.min.css"/>-->
<!--    <script type="text/javascript" src="../data/libs/DataTables/datatables.min.js"></script>-->
<!--    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.material.min.js"></script>-->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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


    <script src="../data/scripts/access.min.js"></script>
    <link href="../data/css/access.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid h-100 vspt-container-fluid">
        <!--        <div class="w-100 h-100 d-flex flex-nowrap vspt-container-fluid-row">-->
        <div class="vspt-container-fluid-row d-flex">

        <?php include_once "../data/parts/nav.php"?>

        <div class="vspt-page-container">

            <div class="row">
                <div class="col">
                    <a class="logbar" href="users.php"><i class="fas fa-arrow-left"></i> Go back to Users</a>
                </div>


            </div>

            <div class="row vspt-title-row no-gutters">
                <div class="col align-items-end d-flex">
                    <legend class="page-title mt-auto">
                        <i class="fas fa-shield-alt"></i>
                        Manage Access for UID <?php echo $uid?>
                    </legend>
                </div>
                <div class="col-auto">
                    <img src="../data/images/Logo_vScription_Transcribe.png" width="300px"/>
                </div>
            </div>

            <div class="vtex-card contents">

                <div class="vtex-top-bar">
                    <h2 class="users-tbl-title">Access Table</h2>
                    <button class="mdc-button mdc-button--unelevated refresh-button" id="refresh_btn">
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">refresh</i>
                        <span class="mdc-button__label">Refresh</span>
                    </button>
                </div>

                <div style="overflow-x: hidden" class="vspt-table-div">
                    <table id="access-tbl" class="access-tbl table vspt-table hover compact"></table>
                </div>
            </div>

        </div>
    </div>
</div>


<!-- The Modal -->
<div id="modal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <h2 style="color: #1e79be" id="modalHeaderTitle" class="mt-3">
            <i class="fas fa-key"></i>&nbsp;Add Permission
        </h2>


        <input id="uidIn" name="uid" value="<?php echo $uid?>" style="display: none">

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
                <button class="mdc-button mdc-button--unelevated blue-btn" id="updateAccBtn" type="button" disabled>
                    <div class="mdc-button__ripple"></div>
                    <i class="fas fa-user-edit"></i>
                    <span class="mdc-button__label">&nbsp; Update</span>
                </button>

                <button class="mdc-button mdc-button--unelevated green-btn" id="createAccBtn" type="button" disabled>
                    <div class="mdc-button__ripple"></div>
                    <i class="fas fa-key"></i>
                    <span class="mdc-button__label">&nbsp; Add</span>
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
