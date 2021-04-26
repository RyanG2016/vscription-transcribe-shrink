<?php
//include('../data/parts/head.php');
require '../api/vendor/autoload.php';
use Src\Enums\INTERNAL_PAGES;
use Src\Enums\CUSTOM_FIELD_ERRORS;

$vtex_page = INTERNAL_PAGES::SETTINGS;
include('data/parts/head.php');

?>

<html lang="en">

<head>
    <title>vScription Settings</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>
    <script src="data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>
    <script src="data/libs/node_modules/@material/switch/dist/mdc.switch.js"></script>
    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>

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
            $hasOwnOrg = (isset($_SESSION["userData"]["account"]) && $_SESSION["userData"]["account"] != 0);
            $ownMatchesCurrent = false;
            if($roleIsSet && $hasOwnOrg && ($_SESSION["accID"] == $_SESSION["userData"]["account"]))
            {
                $ownMatchesCurrent = true;
            }
        ?>
        var roleIsset = <?php echo $roleIsSet ?>;
        var redirectID = <?php echo $roleIsSet? $_SESSION['role']:"0" ?>;
        var hasOwnOrg = <?php echo $hasOwnOrg?"1":"0" ?>;
        var ownMatchesCurrent = <?php echo $ownMatchesCurrent?"1":"0" ?>;
    </script>

    <!-- Enjoyhint library -->
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/kineticjs/5.2.0/kinetic.js"></script>-->
<!--    <script src="data/thirdparty/enjoyhint/enjoyhint.min.js"></script>-->

    <?php $tuts=(isset($_SESSION['tutorials']))?$_SESSION['tutorials']:'{}'; ?>
    <script type="text/javascript">
        var tutorials='<?php echo $tuts;?>';
    </script>

    <link href="data/thirdparty/typeahead/typehead.css" rel="stylesheet">
    <script src="data/thirdparty/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
    <script src="data/thirdparty/parsley/parsley.min.js" type="text/javascript"></script>
    <link href="data/css/parts/parsleyjs.css" rel="stylesheet">

    <link href="data/css/settings.css?v=2" rel="stylesheet">
    <script src="data/scripts/settings.min.js?v=5" type="text/javascript"></script>

</head>

<body>

<div class="container-fluid h-100 vspt-container-fluid">
        <div class="w-100 h-100 d-flex flex-nowrap vspt-container-fluid-row">

        <?php include_once "data/parts/nav.php"?>

        <div class="vspt-page-container">

            <div class="row vspt-title-row no-gutters">
                <div class="col align-items-end d-flex">
                    <legend class="page-title mt-auto">
                        <i class="fad fa-user-cog"></i> Settings
                    </legend>
                </div>
                <div class="col-auto">
                    <img src="data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
                </div>
            </div>

            <div class="vtex-card contents">

                <!--        CONTENTS GOES HERE        -->

                <div class="row settings-content">
                    <div id="userCard" class="col users-card">
                        <h5 class="mb-3"><i class="fas fa-user"></i> Basic</h5>

                        <form id="userForm">
                            <div class="row">
                                <div class="col">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Email</span>
                                        </div>
                                        <input type="text" class="form-control" id="email" name="email" placeholder=""
                                               data-parsley-type="email"
                                               aria-describedby="inputGroupPrepend"
                                               value="<?php echo $_SESSION['userData']['email'] ?>" required>
                                    </div>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">First Name</span>
                                        </div>
                                        <input type="text" class="form-control" id="fname" name="first_name" placeholder=""
                                               aria-describedby="inputGroupPrepend"
                                               data-parsley-pattern="/^[a-z ]{2,50}$/i"
                                               data-parsley-error-message="<?php echo CUSTOM_FIELD_ERRORS::NAME ?>"
                                               value="<?php echo $_SESSION['userData']['first_name'] ?>" required>
                                    </div>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Last Name</span>
                                        </div>
                                        <input type="text" class="form-control" id="lname" name="last_name" placeholder=""
                                               aria-describedby="inputGroupPrepend"
                                               data-parsley-pattern="/^[a-z]{1}[a-z0-9_ ]{2,50}$/i"
                                               data-parsley-error-message="<?php echo CUSTOM_FIELD_ERRORS::NAME ?>"
                                               value="<?php echo $_SESSION['userData']['last_name'] ?>" required>
                                    </div>

                                    <div class="input-group">
                                        <div class="row w-100 no-gutters">
                                            <div class="col">
                                                <span class="bs-text">Newsletter</span>
                                                &emsp;<button id="newsletter" type="button"
                                                              class="btn btn-primary newsletter-button
                                                              <?php echo (isset($_SESSION['userData']['newsletter']) && $_SESSION['userData']['newsletter'] == 1)? 'active':'' ?> "
                                                              data-toggle="button" aria-pressed="<?php echo (isset($_SESSION['userData']['newsletter']) && $_SESSION['userData']['newsletter'] == 1)? 'true':'false'  ?>">
                                                    <span class="vspt-check-toggle-icon"></span>
                                                </button>
                                            </div>

                                            <div class="col text-right">
                                                <span class="bs-text">Receive Job Notifications</span>
                                                &emsp;<button id="emailTranscript" type="button"
                                                              class="btn btn-primary newsletter-button <?php echo (isset($_SESSION['userData']['email_notification']) && $_SESSION['userData']['email_notification'] == 1)? 'active':''  ?>"
                                                              data-toggle="button" aria-pressed="<?php echo (isset($_SESSION['userData']['email_notification']) && $_SESSION['userData']['email_notification'] == 1)?'true':'false' ?>">
                                                    <span class="vspt-check-toggle-icon"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="btn btn-primary vspt-small-btn float-right" type="submit"
                                            id="updateUser">
                                        <i class="fas fa-save"></i> Save
                                    </button>
                                </div>


                                <!--                                                -->
                                <div class="col">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Street</span>
                                        </div>
                                        <input type="text" class="form-control" id="address" name="address" placeholder=""
                                               data-parsley-pattern="/^[a-z0-9_ .]{5,100}$/i"
                                               aria-describedby="inputGroupPrepend"
                                               data-parsley-error-message="<?php echo CUSTOM_FIELD_ERRORS::STREET ?>"
                                               value="<?php echo $_SESSION['userData']['address'] ?>">
                                    </div>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">City</span>
                                        </div>
                                        <input type="text" class="form-control" id="city" name="city" placeholder=""
                                               aria-describedby="inputGroupPrepend"
                                               data-parsley-pattern="/^[a-z ]{2,100}$/i"
                                               data-parsley-error-message="<?php echo CUSTOM_FIELD_ERRORS::CITY ?>"
                                               value="<?php echo $_SESSION['userData']['city'] ?>">
                                    </div>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">State/Prov</span>
                                        </div>
                                        <input type="text" class="form-control" id="state" name="state" placeholder=""
                                               aria-describedby="inputGroupPrepend"
                                               data-parsley-pattern="/^[a-z ]{2,100}$/i"
                                               data-parsley-error-message="<?php echo CUSTOM_FIELD_ERRORS::STATE ?>"
                                               value="<?php echo $_SESSION['userData']['state'] ?>">
                                    </div>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Postal/Zip</span>
                                        </div>
                                        <input type="text" class="form-control" id="zip" name="zip" placeholder=""
                                               data-parsley-length="[0, 20]"
                                               aria-describedby="inputGroupPrepend"
                                               data-parsley-error-message="<?php echo CUSTOM_FIELD_ERRORS::ZIP ?>"
                                               value="<?php echo $_SESSION['userData']['zipcode'] ?>">
                                    </div>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Country</span>
                                        </div>
                                        <input type="text" class="form-control" id="country" name="country" placeholder=""
                                               aria-describedby="inputGroupPrepend"
                                               value="<?php echo $_SESSION['userData']['country'] ?>" required>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>

                    <div class="w-100">
                        <hr>
                    </div>

                    <?php

                    if($roleIsSet && $_SESSION["role"] != 3)
                    {
                    ?>

                        <div id="orgCard" class="col">

                            <?php
                            if(($hasOwnOrg && $ownMatchesCurrent) || ($hasOwnOrg && !$roleIsSet))
                            {
                                echo '<h5 class="mb-3"><i class="fas fa-laptop-house"></i> My Organization</h5>';
                            }else{
                                echo '<h5 class="mb-3"><i class="fas fa-building"></i> Current Organization</h5>';
                            }
                            ?>

                            <form id="orgForm">
                                <div class="row">
                                    <div class="col">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Organization Name</span>
                                            </div>
                                            <input type="text" class="form-control" id="orgName"
                                                   name="organization_name"
                                                   data-parsley-pattern="/^[a-z]{1}[a-z0-9_ ]{2,255}$/i"
                                                   data-parsley-error-message="<?php echo CUSTOM_FIELD_ERRORS::ORG ?>"
                                                   placeholder="" aria-describedby="inputGroupPrepend" value="<?php echo $_SESSION['acc_name'] ?>" required>
                                        </div>

                                        <div class="row no-gutters w-100 ret">
                                            <div class="col">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Organization Retention Time</span>
                                                    </div>
                                                    <input type="number" class="form-control" id="orgRetTime"
                                                           placeholder=""
                                                           name="retention_time"
                                                           aria-describedby="inputGroupPrepend"
                                                           value="<?php echo $_SESSION['acc_retention_time'] ?>"
                                                           data-parsley-error-message="<?php echo CUSTOM_FIELD_ERRORS::RETENTION_TIME ?>"
                                                           max="180"
                                                           min="1"
                                                           required>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Activity Log Retention Time</span>
                                                    </div>

                                                    <input type="number" class="form-control" id="orgLogTime"
                                                           placeholder=""
                                                           max="180"
                                                           min="1"
                                                           name="act_log_ret_time"
                                                           aria-describedby="inputGroupPrepend"
                                                           data-parsley-error-message="<?php echo CUSTOM_FIELD_ERRORS::LOG_RETENTION_TIME ?>"
                                                           value="<?php echo $_SESSION['act_log_retention_time'] ?>"
                                                           required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="input-group">

                                        </div>
                                        <div class="input-group">
                                            <div class="row w-100 no-gutters">
                                                <div class="col" style="align-self: center">
                                                    <div class="form-row">
                                                        <div class="col">
                                                            <em class="bs-text">Auto Refresh Job List  <span  class="vtex-jr-help-icon">(?)</span></em>
                                                        </div>
                                                        <div class="col text-right">
                                                            <div class="mdc-switch mdc-switch--disabled ml-auto mt-auto mb-auto" id="jlSwitch">
                                                                <div class="mdc-switch__track"></div>
                                                                <div class="mdc-switch__thumb-underlay">
                                                                    <div class="mdc-switch__thumb"></div>
                                                                    <input type="checkbox" id="jlSwitchCheckbox" class="mdc-switch__native-control" role="switch" aria-checked="false" disabled>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                <div class="input-group input-group-jl">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Job List Refresh Interval</span>
                                                    </div>

                                                    <input type="number" class="form-control" id="orgJobListRefreshInterval"
                                                           placeholder=""
                                                           max="300"
                                                           min="30"
                                                           name="auto_list_ref_interval"
                                                           aria-describedby="inputGroupPrepend"
                                                           data-parsley-error-message="<?php echo CUSTOM_FIELD_ERRORS::LIST_REFRESH_INTERVAL ?>"
                                                           value="<?php echo $_SESSION['auto_list_refresh_interval'] ?>"
                                                           required>
                                                </div>
                                            </div>
                                            </div>
                                        </div>

                                        <div class="input-group">
                                            <div class="row w-100 no-gutters">
                                                <div class="col" style="align-self: center">
                                                    <div class="form-row">
                                                        <div class="col">
                                                            <em class="bs-text">Enable Speech To Text  <span  class="vtex-help-icon">(?)</span></em>
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
                                                </div>

                                                <div class="col font-italic sr-balance-div"><span class="bs-text">Speech-to-text minutes:</span> <span class="col bs-text p-0 text-left"><span id="srMinutes">
                                                            <span class="spinner">
                                                                <div class="bounce1"></div>
                                                                <div class="bounce2"></div>
                                                                <div class="bounce3"></div>
                                                            </span>
                                                    </span></span>
                                                    <button class="btn btn-primary add-mins-btn" type="button" onclick="window.open('/packages.php', '_blank')">
                                                        <i class="fas fa-plus-circle" ></i> ADD MINS
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary vspt-small-btn" type="button" onclick="window.open('/manage_users.php', '_blank')">
                                            <i class="fas fa-users" ></i> Manage Users
                                        </button>

                                        <button class="btn btn-primary vspt-small-btn float-right" type="submit" id="updateCurrentOrg">
                                            <i class="fas fa-save"></i> Save
                                        </button>

                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="w-100">
                            <hr>
                        </div>


                    <?php
                    }
                    if (!$_SESSION["userData"]["account"]) {

                    ?>

                    <div id="ownOrgCard" class="border-left col">
                        <h5 class="mb-3"><i class="fas fa-laptop-house"></i> My Organization</h5>

                        <div class="alert alert-info" role="alert">
                            <em>You didn't create an organization profile, <u class="vtex-cursor-pointer" data-toggle="modal" data-target="#createAccModal" >create one?</u></em>
                        </div>

                        <hr>
                    </div>

                    <?php
                    }
                    else if(!$ownMatchesCurrent){

                    ?>

                    <div id="ownOrgCard" class="border-left col">
                        <h5 class="mb-3"><i class="fas fa-user"></i> My Organization</h5>

                        <form id="ownOrgForm">
                            <div class="row">
                                <div class="col">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Organization Name</span>
                                        </div>
                                        <input type="text" class="form-control" id="ownOrgName"
                                               name="organization_name"
                                               data-parsley-pattern="/^[a-z]{1}[a-z0-9_ ]{2,255}$/i"
                                               data-parsley-error-message="<?php echo CUSTOM_FIELD_ERRORS::ORG ?>"
                                               placeholder="" aria-describedby="inputGroupPrepend" value="<?php echo $_SESSION['adminAccountName'] ?>" required>
                                    </div>

                                    <div class="row no-gutters w-100 ret">
                                        <div class="col">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Organization Retention Time</span>
                                                </div>
                                                <input type="number" class="form-control" id="ownOrgRetTime"
                                                       placeholder=""
                                                       name="retention_time"
                                                       aria-describedby="inputGroupPrepend"
                                                       value="<?php echo $_SESSION['adminAccRetTime'] ?>"
                                                       max="180"
                                                       min="1"
                                                       data-parsley-error-message="<?php echo CUSTOM_FIELD_ERRORS::RETENTION_TIME ?>"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Activity Log Retention Time</span>
                                                </div>

                                                <input type="number" class="form-control" id="ownOrgLogTime"
                                                       placeholder=""
                                                       max="180"
                                                       data-parsley-error-message="<?php echo CUSTOM_FIELD_ERRORS::LOG_RETENTION_TIME ?>"
                                                       min="1"
                                                       name="act_log_ret_time"
                                                       aria-describedby="inputGroupPrepend"
                                                       value="<?php echo $_SESSION['adminAccLogRetTime'] ?>"
                                                       required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="input-group">

                                    </div>
                                    <div class="input-group">
                                            <div class="row w-100 no-gutters">
                                                <div class="col" style="align-self: center">
                                                    <div class="form-row">
                                                        <div class="col">
                                                            <em class="bs-text">Auto Refresh Job List  <span  class="vtex-jr-help-icon">(?)</span></em>
                                                        </div>
                                                        <div class="col text-right">
                                                            <div class="mdc-switch mdc-switch--disabled ml-auto mt-auto mb-auto" id="jlOwnSwitch">
                                                                <div class="mdc-switch__track"></div>
                                                                <div class="mdc-switch__thumb-underlay">
                                                                    <div class="mdc-switch__thumb"></div>
                                                                    <input type="checkbox" id="jlOwnSwitchCheckbox" class="mdc-switch__native-control" role="switch" aria-checked="false" disabled>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                <div class="input-group input-group-jl">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Job List Refresh Interval</span>
                                                    </div>

                                                    <input type="number" class="form-control" id="ownOrgJobListRefreshInterval"
                                                           placeholder=""
                                                           max="300"
                                                           min="30"
                                                           name="auto_list_ref_interval"
                                                           aria-describedby="inputGroupPrepend"
                                                           data-parsley-error-message="<?php echo CUSTOM_FIELD_ERRORS::LIST_REFRESH_INTERVAL ?>"
                                                           value="<?php echo $_SESSION['adminAccJobRefreshInterval'] ?>"
                                                           required>
                                                </div>
                                            </div>
                                            </div>
                                        </div>


                                    <div class="input-group">
                                        <div class="row w-100 no-gutters">
                                            <div class="col" style="align-self: center">
                                                <div class="form-row">
                                                    <div class="col">
                                                        <em class="bs-text">Enable Speech To Text  <span  class="vtex-help-icon">(?)</span></em>
                                                    </div>
                                                    <div class="col text-right">
                                                        <div class="mdc-switch mdc-switch--disabled ml-auto mt-auto mb-auto" id="srOwnSwitch">
                                                            <div class="mdc-switch__track"></div>
                                                            <div class="mdc-switch__thumb-underlay">
                                                                <div class="mdc-switch__thumb"></div>
                                                                <input type="checkbox" id="ownSrSwitchCheckbox" class="mdc-switch__native-control" role="switch" aria-checked="false" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col font-italic sr-balance-div"><span class="bs-text">Speech-to-text minutes:</span> <span class="col bs-text p-0 text-left"><span id="srOwnMinutes">
                                                                <span class="spinner">
                                                                    <div class="bounce1"></div>
                                                                    <div class="bounce2"></div>
                                                                    <div class="bounce3"></div>
                                                                </span>
                                                    </span></span>
                                                <button class="btn btn-primary add-mins-btn" type="button" onclick="window.open('/packages.php?self', '_blank')">
                                                    <i class="fas fa-plus-circle" ></i> ADD MINS
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="btn btn-primary vspt-small-btn float-right" type="submit" id="updateOwnOrg">
                                        <i class="fas fa-save"></i> Save
                                    </button>

                                </div>
                            </div>
                        </form>

                        <hr>
                    </div>

                    <?php } ?>

                    <div class="w-100"></div>

                    <div id="typistCard" class="col">
                        <h5><i class="fad fa-typewriter"></i> Typist</h5>
<!--                        <div class="alert alert-info" role="alert" id="alertT0">
                            <em>No access found.</em>
                        </div>
                        <div class="alert alert-success" role="alert" id="alertT1">
                            <em>You have access as a typist for
                                <b id="typistCount">0</b>
                                accounts.
                                <br>
                            </em>
                        </div>
-->
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
<!--
                        <div id="typist1" class="text-muted text-justify">Switch your current role to typist from the side menu to start working.</div>
                        <div id="typist0" class="text-muted">Please wait for a job invitation from an admin.</div>-->

                        <div class="position-fixed bottom-0 toast-container right-0 p-3" style="z-index: 50000; right: 0; bottom: 0;">
                            <div id="sttToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
                                <div class="toast-header">
                                           <img src="data/images/Logo_only.png" height="24px" class="rounded mr-2">
                                    <strong class="mr-auto">Organization Updated</strong>
<!--                                    <small>Just now</small>-->
                                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="toast-body">
                                    Speech To Text has been enabled
                                </div>
                            </div>
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
                    &nbsp;Create Organization
                </h3>
<!--                <h5 class="modal-title">Modal title</h5>-->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><span aria-hidden="true"><i class="fas fa-times"></i></span></span>
                </button>
            </div>
            <div class="modal-body">
                <label for="acc_name">Organization Name</label>
                <input type="text" class="form-control" id="accNameTxt" aria-describedby="acc_name_help" placeholder="Choose a name for your organization">
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
