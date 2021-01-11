<?php
//include('../data/parts/head.php');
require '../api/vendor/autoload.php';
use Src\Enums\INTERNAL_PAGES;

$vtex_page = INTERNAL_PAGES::LANDING;
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

<html lang="en">

<head>
    <title>vScription</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>
    <script src="data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>
    <script src="data/libs/node_modules/@material/switch/dist/mdc.switch.js"></script>
    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>

    <!--    Jquery confirm  -->
    <link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">
    <script src="data/dialogues/jquery-confirm.min.js"></script>

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

    <script type="text/javascript">
        <?php
            $roleIsSet = (!isset($_SESSION['role']) && !isset($_SESSION['accID']))?0:true;
        ?>
        var roleIsset = <?php echo $roleIsSet ?>;
        var redirectID = <?php echo $roleIsSet? $_SESSION['role']:0 ?>;
    </script>

    <!-- Enjoyhint library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/kineticjs/5.2.0/kinetic.js"></script>
    <link href="data/thirdparty/enjoyhint/enjoyhint.css" rel="stylesheet">
<!--    <script src="data/thirdparty/enjoyhint/enjoyhint.min.js"></script>-->
    <script src="data/thirdparty/enjoyhint/enjoyhint.min.js"></script>

    <?php $tuts=(isset($_SESSION['tutorials']))?$_SESSION['tutorials']:'{}'; ?>
    <script type="text/javascript">
        var tutorials='<?php echo $tuts;?>';
    </script>
    <link href="data/css/landing.css?v=2" rel="stylesheet">
    <script src="data/scripts/landing.min.js?v=3" type="text/javascript"></script>

</head>

<body>

<div class="container-fluid d-flex h-auto vspt-container-fluid">
    <div class="row w-100 h-100 vspt-container-fluid-row no-gutters" style="white-space: nowrap">

        <?php include_once "data/parts/nav.php"?>

        <div class="vspt-page-container col">

            <div class="row">
                <div class="col">
                    <?php
                    if (isset($_SESSION['role'])) {
                        switch ($_SESSION['role']) {
                            case 1:
                                echo "<a class=\"logbar\" href=\"panel/\"><i class=\"fas fa-arrow-left\"></i> Go to admin panel</a>";
                                break;

                            case 2:
                                echo "<a class=\"logbar\" href=\"main.php\"><i class=\"fas fa-arrow-left\"></i> Go to job list</a>";
                                break;

                            case 3:
                                echo "<a class=\"logbar\" href=\"transcribe.php\"><i class=\"fas fa-arrow-left\"></i> Go to transcribe</a>";
                                break;
                        }
                    }
                    ?>
                </div>


            </div>

            <div class="row vspt-title-row no-gutters">
                <div class="col align-items-end d-flex">
                    <legend class="page-title mt-auto">
                        <i class="fas fa-home"></i> Home
                    </legend>
                </div>
                <div class="col-auto">
                    <img src="data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
                </div>
            </div>

            <div class="vtex-card contents">

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
                            <span style="vertical-align: bottom">Here you can choose your next job start by clicking <b>switch account/role</b> from the sidebar.</span>
                            <!--                            <span style="vertical-align: bottom">Here you can find all your assigned work and data.</span>-->
                        </td>
                    </tr>
                </table>

                <hr>

                <div class="row">
                    <div class="col-3">
                        <div class="list-group" id="list-tab" role="tablist">
                            <a class="list-group-item list-group-item-action active" id="list-current-list" data-toggle="list" href="#list-curr" role="tab" aria-controls="home"><i class="fas fa-building"></i> Current Organization</a>
                            <a class="list-group-item list-group-item-action" id="list-my-org-list" data-toggle="list" href="#list-my-org" role="tab" aria-controls="profile"><i class="fas fa-house-user"></i> My Organization</a>
                            <a class="list-group-item list-group-item-action" id="list-typist-list" data-toggle="list" href="#list-typist" role="tab" aria-controls="messages"><i class="fas fa-keyboard"></i> My Typist Profile</a>
                            <!--                            <a class="list-group-item list-group-item-action" id="list-settings-list" data-toggle="list" href="#list-settings" role="tab" aria-controls="settings">Settings</a>-->
                        </div>
                    </div>
                    <div class="col-9">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="list-curr" role="tabpanel" aria-labelledby="list-current-list">


                                <div id="adminCard" class="col">

                                    <?php
                                    if(!isset($_SESSION["accID"]) || $_SESSION["accID"] == 0)
                                    {
                                        echo '<i class="justify-content-center">Please switch role first</i>';
                                    }
                                    else {
                                        echo '<h3 class="col text-center">'.$_SESSION["acc_name"].'</h3>';
                                        echo "<div class=\"alert alert-info\" role=\"alert\">
                                            Role: <b>" . $_SESSION["role_desc"]. "</b>
                                        </div>";
                                        if($_SESSION["role"] == 1 || $_SESSION["role"] == 2 )
                                        {
                                            echo '<div class="alert alert-light" role="alert" ';

                                            echo'>
                                                    <div class="form-row">
                                                        <div class="col">
                                                            <em>Enable Speech To Text  <span id="srSwitchHelp" class="vtex-help-icon">(?)</span></em>
                                                        </div>
                                                        <div class="col text-right">
                                                        <div class="mdc-switch mdc-switch--disabled ml-auto mt-auto mb-auto" id="srSwitch">
                                                            <div class="mdc-switch__track"></div>
                                                            <div class="mdc-switch__thumb-underlay">
                                                                <div class="mdc-switch__thumb"></div>
                                                                <input type="checkbox" id="srSwitchCheckbox" class="mdc-switch__native-control" role="switch" aria-checked="false" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="form-row m-t-25">
                                                        <div class="col"><em>Speech-to-text minutes remaining: <span class="col p-0 text-left"><span id="srMinutes"></span></span> &nbsp; <i class="fas fa-plus-circle top-up" onclick="window.open(\'/packages.php\', \'_blank\')"></i></em> </div> 
                                                        
                                                    </div>
                                                </div>';
                                        }
                                        echo '<div class="text-muted text-justify">Organization allows you to manage your jobs,
                                                    invite typists, download completed jobs.
                                                </div>';

                                    }
                                    ?>

                                </div>

                            </div>



                            <!--                            ================= my org ===============-->
                            <div class="tab-pane fade" id="list-my-org" role="tabpanel"aria-labelledby="list-my-org-list">

                                <div id="adminCard" class="col">

                                    <?php
                                    if (!$_SESSION["adminAccountName"] && !$_SESSION["adminAccount"]) {
                                        echo '<h3 class="col text-center">Organization</h3>';
                                        echo "<div class=\"alert alert-info\" role=\"alert\">
                                            <em>You didn't create an organization profile, <u class=\"vtex-cursor-pointer\" data-toggle=\"modal\" data-target=\"#createAccModal\" >create one?</u></em>
                                        </div>";
                                    }

                                    else {
                                        echo '<h3 class="col text-center">'.$_SESSION["adminAccountName"].'</h3>';
                                        echo "<div class=\"alert alert-success\" role=\"alert\">
                                            You are admin of <b>" . $_SESSION["adminAccountName"] . "</b>.
                                        </div>";

                                    }
                                    ?>
                                    <div class="text-muted text-justify">Organization allows you to manage your jobs,
                                        invite typists, download completed jobs.
                                    </div>
                                </div>

                            </div>
                            <!--                            // Typist-->
                            <div class="tab-pane fade" id="list-typist" role="tabpanel" aria-labelledby="list-typist-list">

                                <div class="col">
                                    <div class="row">
                                        <div id="typistCard" class="col">
                                            <h3 class="text-center" id="typistCardHead">Typist Profile</h3>

                                            <div class="alert alert-info" role="alert" id="alertT0">
                                                <em>No access found.</em>
                                            </div>
                                            <div class="alert alert-success" role="alert" id="alertT1">
                                                <em>You have access as a typist for
                                                    <b id="typistCount">0</b>
                                                    accounts.
                                                    <br>
                                                </em>
                                            </div>

                                            <div class="alert alert-light" role="alert" id="alertT2">
                                                <div class="form-row">
                                                    <em>Open for work invitations  <span id="typistWorkHelp" class="vtex-help-icon">(?)</span></em>

                                                    <div class="mdc-switch mdc-switch--disabled ml-auto mt-auto mb-auto" id="typist_av_switch">
                                                        <div class="mdc-switch__track"></div>
                                                        <div class="mdc-switch__thumb-underlay">
                                                            <div class="mdc-switch__thumb"></div>
                                                            <input type="checkbox" id="basic-switch" class="mdc-switch__native-control" role="switch" aria-checked="false" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="typist1" class="text-muted text-justify">Switch your current role to typist from the side menu to start working.</div>
                                            <div id="typist0" class="text-muted">Please wait for a job invitation from an admin.</div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!--                            <div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list">...</div>-->
                        </div>
                    </div>
                </div>

            </div>

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

<div class="modal" tabindex="-1" id="createAccModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="color: #1e79be" id="modalHeaderTitle">
                    <i class="fas fa-user-circle"></i>
                    &nbsp;Create Account
                </h3>
<!--                <h5 class="modal-title">Modal title</h5>-->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><span aria-hidden="true"><i class="fas fa-times"></i></span></span>
                </button>
            </div>
            <div class="modal-body">
                <label for="acc_name">Account Name</label>
                <input type="text" class="form-control" id="accNameTxt" aria-describedby="acc_name_help" placeholder="Choose a name for your account">
                <small id="acc_name_help" class="form-text text-muted">
                    Name must be less than 254 characters with no special characters.
                </small>
                <div class="valid-feedback">
                    Looks good!
                </div>
                <div class="invalid-feedback">
                    Please enter a valid name.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="createAdminAccBtn"><i class="fas fa-plus"></i> &nbsp;Create</button>
            </div>
        </div>
    </div>
</div>

<?php include_once "data/parts/footer.php"?>
</body>

</html>
