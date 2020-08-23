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

// admin panel main

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
    <title>vScription Manage Accounts</title>
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
    <script type="text/javascript" src="../data/libs/DataTables/datatables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.material.min.js"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="../data/dialogues/jquery-confirm.min.css">
    <script src="../data/dialogues/jquery-confirm.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.ui.position.js"></script>

    <script src="../data/scripts/accounts.min.js"></script>
    <link href="../data/css/manage_accounts.css" rel="stylesheet">
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
                        <i class="material-icons mdc-button__icon" aria-hidden="true">admin_panel_settings</i>
                        Accounts Management
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
                        <span class="mdc-button__label">&nbsp;Create Account</span>
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
                    <h2 class="accounts-tbl-title">Accounts List</h2>
                    <button class="mdc-button mdc-button--unelevated refresh-button" id="refresh_btn">
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">refresh</i>
                        <span class="mdc-button__label">Refresh</span>
                    </button>
                </div>


                <!--        CONTENTS GOES HERE        -->
                <table id="accounts-tbl" class="accounts-tbl" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Prefix</th>
                        <th>Date Created</th>
                        <th>Ret.</th>
                        <th>Log Ret.</th>
                        <th>Prefix</th>
                        <th>Enabled</th>
                        <th>Billable</th>
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
        <h2 style="color: #1e79be" id="modalHeaderTitle"><i class="fas fa-user-plus"></i>&nbsp;Create New Account</h2>

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
                <fieldset class="vtex-fieldset billable-radios" style="display: inline;text-align: center;">
                    <legend>Billable</legend>
                    <label class="vtex-jq-lbl-check" for="billable-t"><i class="fas fa-check"></i></label>
                    <input class="radio-no-icon" type="radio" name="billable" id="billable-t" value="1">

                    <label class="vtex-jq-lbl-cross" for="billable-f"><i class="fas fa-times"></i></label>
                    <input class="radio-no-icon" type="radio" name="billable" id="billable-f" value="0">
                </fieldset>
            </div>

            <label for="acc_name" class="vtex-form_lbl">
                Account Name
                <input class="acc_name vtex-input" id="accName" name="acc_name" type="text">
            </label>

            <fieldset class="vtex-fieldset">
<!--                <legend>&nbsp;Retention&nbsp;</legend>-->
                <div class="retention-grid">
                    <label for="acc_retention_time" class="vtex-form_lbl rt1">
                        Acc Retention Time
                        <input class="acc_retention_time vtex-input-no-padding vtex-spinner-4-digits" name="acc_retention_time" id="acc_retention_time" type="text">
                    </label>

                    <label for="act_log_retention_time" class="vtex-form_lbl rt2">
                        Act-Log Retention Time
                        <input class="act_log_retention_time vtex-input-no-padding vtex-spinner-4-digits" name="act_log_retention_time" id="act_log_retention_time" type="text">
                    </label>
                </div>
            </fieldset>

            <fieldset class="vtex-fieldset-light">
                <legend>&nbsp;Billing 1&nbsp;</legend>
                <div class="bill1-grid">
                    <div class="br1">
                        <label for="bill_rate1" class="vtex-form_lbl">Rate ($)</label>
                        <br>
                        <input class="bill_rate1 vtex-input-no-padding" name="bill_rate1" id="bill_rate1" type="text">

                    </div>
                    <div class="br1min">
                        <label for="bill_rate1_min_pay" class="vtex-form_lbl">Typing/Minute</label>
                        <br>
                        <input class="bill_rate1_min_pay vtex-input-no-padding" name="bill_rate1_min_pay"
                                   id="bill_rate1_min_pay" type="text">

                    </div>
                    <div class="br1tat">
                        <label for="bill_rate1_TAT" class="vtex-form_lbl">Turn Around Time (days)</label>
                        <br>
                        <input class="bill_rate1_TAT vtex-input-no-padding" name="bill_rate1_TAT" id="bill_rate1_TAT"
                                   type="text">

                    </div>

                    <div class="br1type">
                        <label>Type</label>
                        <select id="br1box" name="bill_rate1_type">
                        </select>
                    </div>


                    <div class="br1desc">
                        <label for="bill_rate1_desc" class="vtex-form_lbl">Description</label>
                        <input class="bill_rate1_desc vtex-input" name="bill_rate1_desc" id="bill_rate1_desc" type="text">
                    </div>
                </div>
            </fieldset>


            <fieldset class="vtex-fieldset-light">
                <legend>&nbsp;Billing 2&nbsp;</legend>
                <div class="bill2-grid">
                    <div class="br2">
                        <label for="bill_rate2" class="vtex-form_lbl">Rate ($)</label>
                        <br>
                        <input class="bill_rate2 vtex-input-no-padding" name="bill_rate2" id="bill_rate2" type="text">

                    </div>
                    <div class="br2min">
                        <label for="bill_rate2_min_pay" class="vtex-form_lbl">Typing/Minute</label>
                        <br>
                        <input class="bill_rate2_min_pay vtex-input-no-padding" name="bill_rate2_min_pay"
                                   id="bill_rate2_min_pay" type="text">

                    </div>
                    <div class="br2tat">
                        <label for="bill_rate2_TAT" class="vtex-form_lbl">Turn Around Time (days)</label>
                        <br>
                        <input class="bill_rate2_TAT vtex-input-no-padding" name="bill_rate2_TAT" id="bill_rate2_TAT"
                                   type="text">

                    </div>

                    <div class="br2type">
                        <label>Type</label>
                        <select id="br2box" name="bill_rate2_type">
                        </select>
                    </div>


                    <div class="br2desc">
                        <label for="bill_rate2_desc" class="vtex-form_lbl">Description</label>
                        <input class="bill_rate2_desc vtex-input" name="bill_rate2_desc" id="bill_rate2_desc" type="text">
                    </div>
                </div>
            </fieldset>

            <fieldset class="vtex-fieldset-light">
                <legend>&nbsp;Billing 3&nbsp;</legend>
                <div class="bill3-grid">
                    <div class="br3">
                        <label for="bill_rate3" class="vtex-form_lbl">Rate ($)</label>
                        <br>
                        <input class="bill_rate3 vtex-input-no-padding" name="bill_rate3" id="bill_rate3" type="text">

                    </div>
                    <div class="br3min">
                        <label for="bill_rate3_min_pay" class="vtex-form_lbl">Typing/Minute</label>
                        <br>
                        <input class="bill_rate3_min_pay vtex-input-no-padding" name="bill_rate3_min_pay"
                                   id="bill_rate3_min_pay" type="text">

                    </div>
                    <div class="br3tat">
                        <label for="bill_rate3_TAT" class="vtex-form_lbl">Turn Around Time (days)</label>
                        <br>
                        <input class="bill_rate3_TAT vtex-input-no-padding" name="bill_rate3_TAT" id="bill_rate3_TAT"
                                   type="text">

                    </div>

                    <div class="br3type">
                        <label>Type</label>
                        <select id="br3box" name="bill_rate3_type">
                        </select>
                    </div>


                    <div class="br3desc">
                        <label for="bill_rate3_desc" class="vtex-form_lbl">Description</label>
                        <input class="bill_rate3_desc vtex-input" name="bill_rate3_desc" id="bill_rate3_desc" type="text">
                    </div>
                </div>
            </fieldset>


            <fieldset class="vtex-fieldset-light">
                <legend>&nbsp;Billing 4&nbsp;</legend>
                <div class="bill4-grid">
                    <div class="br4">
                        <label for="bill_rate4" class="vtex-form_lbl">Rate ($)</label>
                        <br>
                        <input class="bill_rate4 vtex-input-no-padding" name="bill_rate4" id="bill_rate4" type="text">

                    </div>
                    <div class="br4min">
                        <label for="bill_rate4_min_pay" class="vtex-form_lbl">Typing/Minute</label>
                        <br>
                        <input class="bill_rate4_min_pay vtex-input-no-padding" name="bill_rate4_min_pay"
                                   id="bill_rate4_min_pay" type="text">

                    </div>
                    <div class="br4tat">
                        <label for="bill_rate4_TAT" class="vtex-form_lbl">Turn Around Time (days)</label>
                        <br>
                        <input class="bill_rate4_TAT vtex-input-no-padding" name="bill_rate4_TAT" id="bill_rate4_TAT"
                                   type="text">

                    </div>

                    <div class="br4type">
                        <label>Type</label>
                        <select id="br4box" name="bill_rate4_type">
                        </select>
                    </div>


                    <div class="br4desc">
                        <label for="bill_rate4_desc" class="vtex-form_lbl">Description</label>
                        <input class="bill_rate4_desc vtex-input" name="bill_rate4_desc" id="bill_rate4_desc" type="text">
                    </div>
                </div>
            </fieldset>


            <fieldset class="vtex-fieldset-light">
                <legend>&nbsp;Billing 5&nbsp;</legend>
                <div class="bill5-grid">
                    <div class="br5">
                        <label for="bill_rate5" class="vtex-form_lbl">Rate ($)</label>
                        <br>
                        <input class="bill_rate5 vtex-input-no-padding" name="bill_rate5" id="bill_rate5" type="text">

                    </div>
                    <div class="br5min">
                        <label for="bill_rate5_min_pay" class="vtex-form_lbl">Typing/Minute</label>
                        <br>
                        <input class="bill_rate5_min_pay vtex-input-no-padding" name="bill_rate5_min_pay"
                                   id="bill_rate5_min_pay" type="text">

                    </div>
                    <div class="br5tat">
                        <label for="bill_rate5_TAT" class="vtex-form_lbl">Turn Around Time (days)</label>
                        <br>
                        <input class="bill_rate5_TAT vtex-input-no-padding" name="bill_rate5_TAT" id="bill_rate5_TAT"
                                   type="text">

                    </div>

                    <div class="br5type">
                        <label>Type</label>
                        <select id="br5box" name="bill_rate5_type">
                        </select>
                    </div>


                    <div class="br5desc">
                        <label for="bill_rate5_desc" class="vtex-form_lbl">Description</label>
                        <input class="bill_rate5_desc vtex-input" name="bill_rate5_desc" id="bill_rate5_desc" type="text">
                    </div>
                </div>
            </fieldset>

            <div class="modal-footer">
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
</body>

</html>
