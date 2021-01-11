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

// admin panel main

//redirect to main
if (!isset($_SESSION['role']) || $_SESSION['role'] != "1") {
//User is a System Administrator ONLY
    ob_start();
    header('Location: ' . "../index.php");
    ob_end_flush();
    die();
}
$vtex_page = INTERNAL_PAGES::ACCOUNTS;
?>

<html lang="en">

<head>
    <title>vScription Manage Organizations</title>
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
    <!--    <link rel="stylesheet" type="text/css" href="../data/libs/DataTables/datatables.css"/>-->
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/4.0.0/material-components-web.min.css"/>

    <!--    <script type="text/javascript" src="../data/libs/DataTables/datatables.min.js"></script>-->

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
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
            integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV"
            crossorigin="anonymous"></script>

    <!--    <script src="../data/custom-bootstrap-select/js/bootstrap.min.js"></script>-->

    <!-- BOOTSTRAP SELECT -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

    <!--  Datatables  -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>


    <!--  css  -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css"
          crossorigin="anonymous">

    <script src="../data/scripts/accounts.min.js?v=2"></script>
    <link href="../data/css/manage_accounts.css" rel="stylesheet">
</head>

<body>
<div class="container-fluid d-flex h-auto vspt-container-fluid">
    <div class="row w-100 h-100 vspt-container-fluid-row no-gutters" style="white-space: nowrap">

        <?php include_once "../data/parts/nav.php"?>

        <div class="vspt-page-container col">

            <div class="row">
                <div class="col">
                    <a class="logbar" href="index.php"><i class="fas fa-arrow-left"></i> Go back to Admin Panel</a>
                </div>


            </div>

            <div class="row vspt-title-row no-gutters">
                <div class="col align-items-end d-flex">
                    <legend class="page-title mt-auto">
                        <i class="material-icons mdc-button__icon" aria-hidden="true">admin_panel_settings</i>
                        Organization Management
                    </legend>
                </div>
                <div class="col-auto">
                    <img src="../data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
                </div>
            </div>

            <div class="vtex-card contents">

                <div class="vtex-top-bar">
                    <h2 class="users-tbl-title">Organization List</h2>
                    <button class="mdc-button mdc-button--unelevated refresh-button" id="refresh_btn">
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">refresh</i>
                        <span class="mdc-button__label">Refresh</span>
                    </button>
                </div>

                <div style="overflow-x: hidden" class="vspt-table-div">
                    <table id="accounts-tbl" class="accounts-tbl vspt-table table row-border hover compact"></table>
                </div>
            </div>

        </div>
    </div>
</div>



<!-- The Modal -->
<div id="modal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <form method="post" id="createAccForm" class="createAccForm" target="_self">
            <div class="modal-header">
                <div>
                    <h3 style="color: #1e79be" id="modalHeaderTitle"><i class="fas fa-user-plus"></i>&nbsp;Create New
                        Org</h3>
                </div>
                <div style="text-align: right">
                    <fieldset class="vtex-fieldset enabled-radios" style="display: inline;text-align: center;">
                        <legend>Enabled</legend>
                        <label class="vtex-jq-lbl-check" for="enabled-t"><i class="fas fa-check"></i></label>
                        <input class="radio-no-icon" type="radio" name="enabled" id="enabled-t" value="1">

                        <label class="vtex-jq-lbl-cross" for="enabled-f"><i class="fas fa-times"></i></label>
                        <input class="radio-no-icon" type="radio" name="enabled" id="enabled-f" value="0">
                    </fieldset>
                    <fieldset class="vtex-fieldset billable-radios" style="display: inline;text-align: center;">
                        <legend>Billable</legend>
                        <label class="vtex-jq-lbl-check" for="billable-t"><i class="fas fa-check"></i></label>
                        <input class="radio-no-icon" type="radio" name="billable" id="billable-t" value="1">

                        <label class="vtex-jq-lbl-cross" for="billable-f"><i class="fas fa-times"></i></label>
                        <input class="radio-no-icon" type="radio" name="billable" id="billable-f" value="0">
                    </fieldset>
                </div>
            </div>

            <div class="modal-body m-t-8">

                <!--            <label for="enabled" class="vtex-form_lbl">Enabled</label>-->
                <!--            <input class="enabled vtex-input" type="text">-->


                <!--                <label for="acc_name" class="vtex-form_lbl">-->
                <!--                    Account Name-->
                <!--                    <input class="acc_name vtex-input" id="accName" name="acc_name" type="text">-->
                <!--                </label>-->

                <div class="form-row">
                    <div class="input-group col w-100 mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Org Name</span>
                        </div>
                        <input type="text" class="form-control" id="accName" placeholder="" name="acc_name">
                    </div>
                </div>


                <div class="form-row mb-2">
                    <div class="input-group col">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Org Retention Time</span>
                        </div>
                        <input type="number" class="form-control" id="acc_retention_time" placeholder=""
                               name="acc_retention_time" min="0"
                               onkeyup="if(this.value<0){this.value= this.value * -1}">
                    </div>

                    <div class="input-group col">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Act-Log Retention Time</span>
                        </div>
                        <input type="number" class="form-control" id="act_log_retention_time" placeholder=""
                               name="act_log_retention_time" min="0"
                               onkeyup="if(this.value<0){this.value= this.value * -1}">
                    </div>

                </div>

                <fieldset class="vtex-fieldset-light">
                    <legend>&nbsp;Billing 1&nbsp;</legend>

                    <div class="form-row mb-2">

                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rate ($)</span>
                            </div>
                            <input type="number" class="form-control" id="bill_rate1" placeholder="" name="bill_rate1"
                                   min="0" onkeyup="if(this.value<0){this.value= this.value * -1}">
                        </div>

                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Typing/Minute</span>
                            </div>
                            <input type="number" class="form-control" id="bill_rate1_min_pay" placeholder=""
                                   name="bill_rate1_min_pay" min="0"
                                   onkeyup="if(this.value<0){this.value= this.value * -1}">
                        </div>

                    </div>


                    <div class="form-row mb-2">
                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Turn Around Time (days)</span>
                            </div>
                            <input type="number" class="form-control" id="bill_rate1_TAT" placeholder=""
                                   name="bill_rate1_TAT" min="0"
                                   onkeyup="if(this.value<0){this.value= this.value * -1}">
                        </div>

                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Type</span>
                            </div>
                            <select class="form-control" id="br1box" name="bill_rate1_type"> </select>
                        </div>
                    </div>

                    <div class="form-row mb-2">
                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Description</span>
                            </div>
                            <input type="text" class="form-control" id="bill_rate1_desc" placeholder=""
                                   name="bill_rate1_desc">
                        </div>
                    </div>

                </fieldset>


                <fieldset class="vtex-fieldset-light">
                    <legend>&nbsp;Billing 2&nbsp;</legend>

                    <div class="form-row mb-2">

                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rate ($)</span>
                            </div>
                            <input type="number" class="form-control" id="bill_rate2" placeholder="" name="bill_rate2"
                                   min="0" onkeyup="if(this.value<0){this.value= this.value * -1}">
                        </div>

                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Typing/Minute</span>
                            </div>
                            <input type="number" class="form-control" id="bill_rate2_min_pay" placeholder=""
                                   name="bill_rate2_min_pay" min="0"
                                   onkeyup="if(this.value<0){this.value= this.value * -1}">
                        </div>

                    </div>


                    <div class="form-row mb-2">
                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Turn Around Time (days)</span>
                            </div>
                            <input type="number" class="form-control" id="bill_rate2_TAT" placeholder=""
                                   name="bill_rate2_TAT" min="0"
                                   onkeyup="if(this.value<0){this.value= this.value * -1}">
                        </div>

                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Type</span>
                            </div>
                            <select class="form-control" id="br2box" name="bill_rate2_type"> </select>
                        </div>
                    </div>

                    <div class="form-row mb-2">
                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Description</span>
                            </div>
                            <input type="text" class="form-control" id="bill_rate2_desc" placeholder=""
                                   name="bill_rate2_desc">
                        </div>
                    </div>

                </fieldset>


                <fieldset class="vtex-fieldset-light">
                    <legend>&nbsp;Billing 3&nbsp;</legend>

                    <div class="form-row mb-2">

                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rate ($)</span>
                            </div>
                            <input type="number" class="form-control" id="bill_rate3" placeholder="" name="bill_rate3"
                                   min="0" onkeyup="if(this.value<0){this.value= this.value * -1}">
                        </div>

                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Typing/Minute</span>
                            </div>
                            <input type="number" class="form-control" id="bill_rate3_min_pay" placeholder=""
                                   name="bill_rate3_min_pay" min="0"
                                   onkeyup="if(this.value<0){this.value= this.value * -1}">
                        </div>

                    </div>


                    <div class="form-row mb-2">
                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Turn Around Time (days)</span>
                            </div>
                            <input type="number" class="form-control" id="bill_rate3_TAT" placeholder=""
                                   name="bill_rate3_TAT" min="0"
                                   onkeyup="if(this.value<0){this.value= this.value * -1}">
                        </div>

                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Type</span>
                            </div>
                            <select class="form-control" id="br3box" name="bill_rate3_type"> </select>
                        </div>
                    </div>

                    <div class="form-row mb-2">
                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Description</span>
                            </div>
                            <input type="text" class="form-control" id="bill_rate3_desc" placeholder=""
                                   name="bill_rate3_desc">
                        </div>
                    </div>

                </fieldset>


                <fieldset class="vtex-fieldset-light">
                    <legend>&nbsp;Billing 4&nbsp;</legend>

                    <div class="form-row mb-2">

                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rate ($)</span>
                            </div>
                            <input type="number" class="form-control" id="bill_rate4" placeholder="" name="bill_rate4"
                                   min="0" onkeyup="if(this.value<0){this.value= this.value * -1}">
                        </div>

                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Typing/Minute</span>
                            </div>
                            <input type="number" class="form-control" id="bill_rate4_min_pay" placeholder=""
                                   name="bill_rate4_min_pay" min="0"
                                   onkeyup="if(this.value<0){this.value= this.value * -1}">
                        </div>

                    </div>


                    <div class="form-row mb-2">
                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Turn Around Time (days)</span>
                            </div>
                            <input type="number" class="form-control" id="bill_rate4_TAT" placeholder=""
                                   name="bill_rate4_TAT" min="0"
                                   onkeyup="if(this.value<0){this.value= this.value * -1}">
                        </div>

                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Type</span>
                            </div>
                            <select class="form-control" id="br4box" name="bill_rate4_type"> </select>
                        </div>
                    </div>

                    <div class="form-row mb-2">
                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Description</span>
                            </div>
                            <input type="text" class="form-control" id="bill_rate4_desc" placeholder=""
                                   name="bill_rate4_desc">
                        </div>
                    </div>

                </fieldset>


                <fieldset class="vtex-fieldset-light">
                    <legend>&nbsp;Billing 5&nbsp;</legend>

                    <div class="form-row mb-2">

                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rate ($)</span>
                            </div>
                            <input type="number" class="form-control" id="bill_rate5" placeholder="" name="bill_rate5"
                                   min="0" onkeyup="if(this.value<0){this.value= this.value * -1}">
                        </div>

                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Typing/Minute</span>
                            </div>
                            <input type="number" class="form-control" id="bill_rate5_min_pay" placeholder=""
                                   name="bill_rate5_min_pay" min="0"
                                   onkeyup="if(this.value<0){this.value= this.value * -1}">
                        </div>

                    </div>


                    <div class="form-row mb-2">
                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Turn Around Time (days)</span>
                            </div>
                            <input type="number" class="form-control" id="bill_rate5_TAT" placeholder=""
                                   name="bill_rate5_TAT" min="0"
                                   onkeyup="if(this.value<0){this.value= this.value * -1}">
                        </div>

                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Type</span>
                            </div>
                            <select class="form-control" id="br5box" name="bill_rate5_type"> </select>
                        </div>
                    </div>

                    <div class="form-row mb-2">
                        <div class="input-group col">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Description</span>
                            </div>
                            <input type="text" class="form-control" id="bill_rate5_desc" placeholder=""
                                   name="bill_rate5_desc">
                        </div>
                    </div>

                </fieldset>


            </div>

            <div class="modal-footer pb-0">
                <button class="mdc-button mdc-button--unelevated cancel-acc-button" id="closeAccModal" type="button">
                    <div class="mdc-button__ripple"></div>
                    <i class="fas fa-times"></i>
                    <span class="mdc-button__label">&nbsp; Cancel</span>
                </button>
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
            </div>

        </form>


    </div>
</div>

<?php include_once "../data/parts/footer.php"?>
</body>

</html>
