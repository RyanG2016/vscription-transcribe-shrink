<?php
include('data/parts/session_settings.php');
include("data/parts/config.php");
include('data/parts/constants.php');
include('data/parts/ping.php');


if (isset($_SESSION['loggedIn'])) {
    unset($_SESSION['counter']);
    session_regenerate_id(true);
    //redirect to main
    if (isset($_SESSION['role'])) {

        if ($_SESSION['role'] == "1") {
            // User is System Admin
            redirect("panel/");
        } else if ($_SESSION['role'] == "2") {
            // User is Client Administrator
            redirect("main.php");
        } else if ($_SESSION['role'] == "3") {
            //User is a Transcriptionist
            redirect("transcribe.php");
        } else {
            redirect("settings.php");
        }

    } else {
        redirect("settings.php");
    }

}

isset($_SESSION['uEmail']) ? $uEmail = $_SESSION['uEmail'] : $uEmail = "";
$hasRef = isset($_GET['ref']) && !empty($_GET['ref']);
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>

    <title>Signup</title>
    <!-- Redirect to another page (for no-js support) (place it in your <head>) -->
    <noscript>
        <meta http-equiv="refresh" content="0;url=noscript.php">
    </noscript>

    <script type="text/javascript">
        let ref = <?php echo $hasRef ? "'" . $_GET['ref'] . "'" : 0 ?>;
    </script>

    <script src="data/login/vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
            integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV"
            crossorigin="anonymous"></script>

    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>

    <link rel="stylesheet" type="text/css" href="data/login/vendor/animate/animate.css">

    <link rel="stylesheet" type="text/css" href="data/login/css/util.css?v=<?php echo $version_control ?>">
    <link rel="stylesheet" type="text/css" href="data/css/signup.css?v=<?php echo $version_control ?>">

    <!--	Tooltip 	-->
    <link rel="stylesheet" type="text/css" href="data/tooltipster/css/tooltipster.bundle.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="data/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-borderless.min.css"/>
    <script type="text/javascript" src="data/tooltipster/js/tooltipster.bundle.min.js"></script>

    <!--	Scroll bar  	-->
    <script src="data/scrollbar/jquery.nicescroll.js"></script>


    <!--<link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">-->
    <link rel="stylesheet" href="data/dialogues/jquery-confirm.min2.css">
    <script src="data/dialogues/jquery-confirm.min.js"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"
            integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg=="
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="data/css/custom-bootstrap-select.css"/>

    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>

    <script src="data/scripts/signup.min.js"></script>

</head>

<body>

<div class="modal fade" tabindex="-1" id="modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="color: #343a40" id="modalHeaderTitle">
                    <i class="fas fa-concierge-bell"></i>&nbsp;Choose A Service Type
                </h3>
                <!--                <h5 class="modal-title">Modal title</h5>-->
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><span aria-hidden="true"><i class="fas fa-times"></i></span></span>
                </button> -->
            </div>
            <div class="modal-body pb-0 justify-content-between">
                <div class="serviceChoiceBtn" id="platformServices" title="Choose this to use vScription Transcribe as your own dictation system. You provide your own typists">
                        <button class="mdc-button mdc-button--raised">
                            <span class="mdc-button__ripple"></span>
                            <i class="fas fa-microphone"></i>
                            <span class="mdc-button__label">PLATFORM SERVICES</span>
                        </button>
                </div>
                <div class="serviceChoiceBtn" id="transcriptionServices" title="Choose this to let us do all of your typing for you. All you have to do is send us your files">
                        <button class="mdc-button mdc-button--raised mdc-button--leading">
                            <span class="mdc-button__ripple"></span>
                            <i class="fas fa-typewriter"></i>
                            <span class="mdc-button__label">TRANSCRIPTION SERVICES</span>
                        </button>
                </div>
                <div class="serviceChoiceBtn" id="NSTTServices" title="Choose this to use our speech to text engine to convert your meetings to text within minutes.">
                        <button class="mdc-button mdc-button--raised mdc-button--leading">
                            <span class="mdc-button__ripple"></span>
                            <i class="fas fa-comment-alt-lines"></i>
                            <span class="mdc-button__label">NARRATIVE SPEECH TO TEXT SERVICES</span>
                        </button>
                </div>
            </div>
                    <!-- <div class="mdc-radio mdc-radio--touch">
                        <div class="mdc-radio">
                            <label id="lblPlatformServices">
                                <input type="radio" class="mdc-radio__native-control" name="radio" id="platformServices" value="1" checked >
                                Platform Services 
                            </label>
                        </div>
                       <div class="mdc-radio">
                            <label id="lblTranscriptionServices">
                            <input type="radio" class="mdc-radio__native-control" name="radio" id="transcriptionServices" value="2" >
                            Transcription Services
                            </label>
                        </div>
                        <div class="mdc-radio">                             
                            <label id="lblNSTTServices">
                            <input type="radio" class="mdc-radio__native-control" name="radio" id="NSTTServices" value="3" >
                            Narrative Speech To Text Services
                            </label> 
                        </div> 
                    </div>   -->
                <!-- <br> -->

                <div class="modal-footer pr-0">
                    <!-- <button class="mdc-button mdc-button--unelevated green-btn" id="saveSTBtn" type="button">
                        <div class="mdc-button__ripple"></div>
                        <i class="fas fa-check"></i>
                        <span class="mdc-button__label">&nbsp; Ok</span> -->
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="vtex-signup-container">
    <form class="vtex-signup-form needs-validation" id="signupForm" autocomplete="off" novalidate>

        <span class="login100-form-title p-b-20">
            <img src="data/images/Logo_vScription_Transcribe_Stacked.png" style="height: 110px"
                 alt="vScription"/>
        </span>
        <span id="title" class="login100-form-title p-b-26">
            Signup
        </span>

        <div id="signupCarousel" class="carousel slide" data-interval="false">
            <div class="carousel-inner">
                <div class="carousel-item active">

                    <!--<div class="row justify-content-center carousel-page-title">
                        <span class="align-text-bottom"> <i class="fas fa-info-circle fa-lg"></i> </span>
                        <h5 class="fs-22 align-top">&nbsp;Basic</h5>
                    </div>-->

                    <!----------------------Row 1----------------->

                    <div class="form-row">
                        <!----------------------EMAIL----------------->
                        <div class="col-12">
                            <label for="inputEmail"><i class="fas fa-envelope"></i> Email</label>

                            <?php
                            if($hasRef)
                            {
                                ?>
                                <input type="email" class="form-control" id="inputEmail" placeholder="Email" name="email"
                                       value="<?php echo isset($_GET['email'])?$_GET['email']:'' ?>"
                                       required readonly>
                                <?php
                            }

                            else{
                                ?>

                                <input type="email" class="form-control" id="inputEmail" placeholder="Email" name="email"
                                       value="<?php echo isset($_GET['email'])?$_GET['email']:'' ?>"
                                       required autofocus>

                                <?php
                            }
                            ?>


                            <!--                                <div class="valid-feedback">-->
                            <!--                                    Looks good!-->
                            <!--                                </div>-->
                            <div class="invalid-feedback">
                                Please enter a valid email
                            </div>
                        </div>
                    </div>


                    <div class="form-row m-t-16">

                        <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                            <!----------------------PASSWORD-------------->
                            <label for="inputPassword"><i class="fas fa-key"></i> Password</label>
                            <input type="password" class="form-control" id="inputPassword" placeholder="Password"
                                   name="password"
                                   title="Password Requirements"
                                   data-trigger="click"
                                   required>
                            <!--<div class="valid-feedback">
                                Looks good!
                            </div>-->
                            <div class="invalid-feedback">
                                Please enter a valid password
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                            <!----------------------Confirm Password-------------->
                            <label for="inputConfirmPassword"><i class="fas fa-key"></i> Confirm </label>
                            <input type="password" class="form-control" id="inputConfirmPassword"
                                   placeholder="Confirm Password"
                                   title="Confirm Password"
                                   data-trigger="focus"
                                   required>
                            <!--<div class="valid-feedback">
                                Looks good!
                            </div>-->
                            <div class="invalid-feedback">
                                Passwords doesn't match.
                            </div>
                        </div>

                    </div>


                    <!----------------------Organization----------------->

                    <div class="form-row m-t-16">
                        <div class="col-12">
                            <label for="inputAccName"><i class="fas fa-sitemap"></i> Organization Name</label>

                            <?php
                            if($hasRef)
                            {
                                ?>
                                <input type="text" class="form-control" id="inputAccName" placeholder="" value="<?php echo isset($_GET['org'])?$_GET['org']:'invitation' ?>"
                                       disabled>
                                <?php
                            }

                            else{
                            ?>

                                <input type="text" class="form-control" id="inputAccName" placeholder="" name="accname"
                                       required>

                           <?php
                           }
                            ?>
                        </div>

                        <!----------------------Line Break----------------->
                        <div class="w-100 m-t-2"></div>

                    </div>

                    <!----------------------NAME----------------->
                    <div class="form-row m-t-16">
                        <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                            <label for="inputfName"><i class="fas fa-font"></i> First Name</label>
                            <input type="text" class="form-control" id="inputfName" placeholder="" name="fname"
                                   required autofocus>
                            <!--<div class="valid-feedback">
                                Looks good!
                            </div>-->
                            <div class="invalid-feedback">
                                Please enter your name.
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                            <label for="inputlName"><i class="fas fa-bold"></i> Last Name</label>
                            <input type="text" class="form-control" id="inputlName" placeholder="" name="lname"
                                   required>
                            <!--<div class="valid-feedback">
                                Looks good!
                            </div>-->
                        </div>
                    </div>

                    <div class="form-row m-t-16">

                        <div class="col">
                            <label for="countryBox"><i class="fas fa-globe-americas"></i> Country</label>
                            <div class="spinner" id="countrySpin">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                            <select class="form-control show-tick country-box" id="countryBox" data-container="body"
                                    data-dropup-auto="false" name="country">
                                <option selected>Loading...</option>
                            </select>
                        </div>

                    </div>


                </div>
                <div class="carousel-item">

                    <!--<div class="row justify-content-center carousel-page-title mb-3">
                        <span class="align-text-bottom"> <i class="fas fa-user-check fa-lg"></i> </span>
                        <h5 class="fs-22 align-top">&nbsp;Verification</h5>
                    </div>-->

                    <!-------------Verification Page----------------->

                    <div class="form-row">
                        <div class="form-group col justify-content-center text-center">
                            <label for="code" class="justify-content-center text-center">
                                Enter the verification code sent to your email
                            </label>
                            <input type="text" class="form-control text-lg-center fs-25 col-sm-5 ml-auto mr-auto"
                                   id="code" maxlength="6" placeholder="Code">
                            <!--<div class="valid-feedback">
                                Looks good!
                            </div>-->
                            <div class="invalid-feedback">
                                Please check your entry
                            </div>
                        </div>
                    </div>

                    <!-- <div class="verify-btn-container">
                         <div class=" row w-100">
                             <div class="col">
                                 <button type="button" class="btn btn-primary btn-lg w-100" id="verifyBtn">Verify</button>
                             </div>
                         </div>
                     </div>
-->

                </div>

                <div class="carousel-item">

                    <div class="row justify-content-center carousel-page-title mb-3">
                        <span class="align-text-bottom"> <i class="fas fa-sign-in-alt fa-lg"></i> </span>
                        <h5 class="fs-22 align-top">&nbsp;Logging in</h5>
                    </div>

                    <!-------------ACCOUNT-NAME----------------->

                    <h6 class="text-white text-center">Setting up your account, please wait..<br>
                        <!--<h6 class="text-sm-center font-italic font-weight-light text-muted">
                            <small>You can verify later by visiting the link sent to your email</small>
                        </h6>-->
                    </h6>

                    <!-- <div class="verify-btn-container">
                         <div class=" row w-100">
                             <div class="col">
                                 <button type="button" class="btn btn-primary btn-lg w-100" id="verifyBtn">Verify</button>
                             </div>
                         </div>
                     </div>
-->

                </div>


            </div>
        </div>

        <!--            <hr/>-->
        <div class="checkbox justify-content-center m-t-10" id="tosDiv">
            <div class="form-inline justify-content-center">
                <label>
                    <input type="checkbox" id="tos"/>
                    <small class="text-sm-right font-italic fs-17"> &nbsp; I have read and agreed to the <span><a
                                    class="fs-17" href="./terms.php" target="_blank">Terms and Conditions</a> </span></small>
                </label>

            </div>
        </div>


        <!----------------------Signup---------------->
        <div class="container-login100-form-btn pt-0">
            <div class="row w-100 justify-content-center">
                <!--<div class="col-auto arrows prev-btn-div">
                    <button type="button" class="btn btn-primary btn-lg" id="prevBtn" ><</button>
                </div>-->
                <div class="col-8">
                    <button type="button" class="btn btn-primary btn-lg" id="signupBtn" disabled>Signup</button>
                </div>
                <!--<div class="col-auto arrows next-btn-div">
                    <button type="button" class="btn btn-primary btn-lg w-auto" id="nextBtn" >></button>
                </div>-->
            </div>
        </div>

        <div class="row w-100 m-0 justify-content-center">

            <div class="col-8">
                <div class="progress" id="formProgressDiv">
                    <div class="progress-bar" id="formProgressBar"
                         role="progressbar" style="width: 0;" aria-valuenow="0" aria-valuemin="0"
                         aria-valuemax="100"></div>
                </div>
            </div>

            <div class="w-100"></div>

            <div class="col-8">
                <div class="progress" id="loginProgressDiv" style="display: none">
                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                         id="loginProgressBar"
                         role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                         style="width: 100%"></div>
                </div>
            </div>

        </div>

    </form>


    <div class="row p-t-20 m-0 " style="width: 100%">
        <div class="col">
            <div class="text-left">
                <a class="txt2" href="./index.php" id="loginHyperLink">
                    Back to Login
                </a>
            </div>
        </div>
        <div class="col">
            <div class="text-right">
                <a class="txt2" href="./policy.php" target="_blank">
                    Privacy Policy
                </a>
            </div>
        </div>
    </div>
</div>

</body>
<noscript>
    For full functionality of this site, it is necessary to enable JavaScript.
    You can find a step-by-step instruction at <a href="https://www.enable-javascript.net">enable-javascript.net</a> to
    enable JavaScript in your web browser.
</noscript>

</html>
