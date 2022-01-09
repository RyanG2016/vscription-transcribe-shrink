<?php
include_once('data/parts/session_settings.php');
include("data/parts/config.php");
include('data/parts/constants.php');
include('data/parts/ping.php');


if (isset($_SESSION['loggedIn'])) {
//    session_regenerate_id(true);
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

    <!-- <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script> -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <!-- Start Bootstrap 5 -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

    <!-- End Bootstrap 5 -->

    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">

    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>

    <link rel="stylesheet" type="text/css" href="data/login/vendor/animate/animate.css">

    <link rel="stylesheet" type="text/css" href="data/login/css/new_custom.css">

    <link rel="stylesheet" type="text/css" href="data/login/css/util.css?v=<?php echo $version_control ?>">
    <link rel="stylesheet" type="text/css" href="data/css/signup.css?v=<?php echo $version_control ?>">

    <!--	Tooltip 	-->
    <link rel="stylesheet" type="text/css" href="data/tooltipster/css/tooltipster.bundle.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="data/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-borderless.min.css"/>

    <script type="text/javascript" src="data/tooltipster/js/tooltipster.bundle.min.js"></script>

    <!-- Scroll Bar -->
    <script src="data/scrollbar/jquery.nicescroll.js"></script>

    <!--<link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">-->
    <link rel="stylesheet" href="data/dialogues/jquery-confirm.min2.css">
    <script src="data/dialogues/jquery-confirm.min.js"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"
            integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg=="
            crossorigin="anonymous"></script>

    <link rel="stylesheet" href="data/css/custom-bootstrap-select.css"/>

    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>

    <script src="data/scripts/signup.min.js?v=6"></script>

</head>

<body style="padding: 0px !important; padding-right: 0px !important">

<div class="modal main-model-fade-4 fade" tabindex="-1" id="modal" data-backdrop="static" data-keyboard="false">
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

                <div class="serviceChoiceBtn disabled d-none" id="NSTTServices" title="Choose this to use our speech to text engine to convert your meetings to text within minutes.">
                        <button class="mdc-button mdc-button--raised mdc-button--leading" disabled>
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

<div class="container_new_login14">
    <form class="vtex-signup-form needs-validation" id="signupForm" autocomplete="off" novalidate>
      

        <div id="signupCarousel" class="carousel slide" data-bs-interval="false">
            <div class="carousel-inner">

                <div class="carousel-item sign-up-custom_corlor active">

                    <!--<div class="row justify-content-center carousel-page-title">
                        <span class="align-text-bottom"> <i class="fas fa-info-circle fa-lg"></i> </span>
                        <h5 class="fs-22 align-top">&nbsp;Basic</h5>
                    </div>-->

                    <!----------------------Row 1----------------->
            <div class="row_row">

                <div class="colmd6L d-none d-md-block">
                    
                    <span class="login100-form-title p-b-20">
                        <img src="data/images/Logo_vScription_Transcribe_Stacked.png" style="height: 110px" id="left_logo"
                             alt="vScription"/>
                    </span>
                    <span id="copyright">
						Copyright @2022
					</span>
                    <!-- <span id="title" class="login100-form-title p-b-26">
                        Signup
                    </span> -->

                </div>

                <div class="colmd6R">
                <div class="signup-form">

                     <div class="form-row">
                        <div class="col-12">
                        <img src="data/images/Logo_vScription_Transcribe_Stacked.png" alt="" id="login-resp-logo" class="d-md-none">
                            <h3 class="signup_heaing_new_design9879"> Sign Up </h3>
                        </div>
                    </div>

                    <div class="form-row">
                        <!----------------------EMAIL----------------->
   

                        <div class="col-12">
                            <!-- <label for="inputEmail"><i class="fas fa-envelope"></i> Email</label> -->

                            <?php
                            if($hasRef)
                            {
                                ?>
                                <input type="email" class="input_form_control3" id="inputEmail" placeholder="Email" name="email"
                                       value="<?php echo isset($_GET['email'])?$_GET['email']:'' ?>"
                                       required readonly>
                                <?php
                            }

                            else{
                                ?>

                                <input type="email" class="input_form_control3" id="inputEmail" placeholder="Email*" name="email"
                                       value="<?php echo isset($_GET['email'])?$_GET['email']:'' ?>"
                                       required autofocus>

                                <?php
                            }
                            ?>

                            <!-- <i class="far fa-envelope fa-lg signup_field_icons"></i> -->
                            


                            <!--                                <div class="valid-feedback">-->
                            <!--                                    Looks good!-->
                            <!--                                </div>-->
                            <div class="invalid-feedback">
                                Please enter a valid email
                            </div>
                        </div>
                    
                    </div>


                    <div class="form-row">

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <!----------------------PASSWORD-------------->
                            <!-- <label for="inputPassword"><i class="fas fa-key"></i> Password</label> -->
                            <input type="password" class="input_form_control3" id="inputPassword" placeholder="Password*"
                                   name="password"
                                   title="Password Requirements"
                                   data-trigger="click"
                                   required>
                            <!-- <i class="far fa-lock fa-lg signup_field_icons"></i> -->
                            <div class="invalid-feedback">
                                Please enter a valid password
                            </div>

                        </div>                   

                    </div>

                    <div class="form-row">
                        
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <!----------------------Confirm Password-------------->
                            <!-- <label for="inputConfirmPassword"><i class="fas fa-key"></i> Confirm </label> -->
                            <input type="password" class="input_form_control3" id="inputConfirmPassword"
                                   placeholder="Confirm Password*"
                                   title="Confirm Password"
                                   data-trigger="focus"
                                   required>
                                   
                            <!-- <i class="far fa-lock fa-lg signup_field_icons"></i> -->
                            <div class="invalid-feedback">
                                Passwords don't match.
                            </div>
                        </div>

                    </div>

                    <!----------------------Organization----------------->

                    <div class="form-row">

                        <div class="col-12">
                           <!--  <label for="inputAccName"><i class="fas fa-sitemap"></i> Organization Name</label> -->

                            <?php
                            if($hasRef)
                            {
                                ?>
                                <input type="text" class="input_form_control3" id="inputAccName" placeholder="Organization Name*" value="<?php echo isset($_GET['org'])?$_GET['org']:'invitation' ?>"
                                       disabled>
                                <?php
                            }

                            else{
                            ?>

                                <input type="text" class="input_form_control3" id="inputAccName" placeholder="Organization Name*" name="accname"
                                       required>

                           <?php
                           }
                            ?>
                        <!-- <i class="far fa-sitemap fa-lg signup_field_icons"></i> -->
                        </div>

                        <!----------------------Line Break----------------->
                        <div class="w-100 m-t-2"></div>

                    </div>

                    <!----------------------NAME----------------->
                    <div class="form-row">

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <!-- <label for="inputfName"><i class="fas fa-font"></i> First Name</label> -->
                            <input type="text" class="input_form_control3" id="inputfName" placeholder="First Name*" name="fname"
                                   required autofocus>
                            <!--<div class="valid-feedback">
                                Looks good!
                            </div>-->
                            <div class="invalid-feedback">
                                Please enter your name.
                            </div>
                        <!-- <i class="far fa-user fa-lg signup_field_icons"></i> -->
                        </div>
                        
                    </div>

                    <div class="form-row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <!-- <label for="inputlName"><i class="fas fa-bold"></i> Last Name</label> -->
                            <input type="text" class="input_form_control3" id="inputlName" placeholder="Last Name*" name="lname"
                                   required>
                        <!-- <i class="far fa-user fa-lg signup_field_icons"></i> -->
                        </div>
                    </div>

                    <div class="form-row">

                        <div class="col">
                           <!--  <label for="countryBox"><i class="fas fa-globe-americas"></i> Country</label> -->
                            <div class="spinner" id="countrySpin">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                            <select class="show-tick country-box" id="countryBox" data-container="body"
                                    data-dropup-auto="false" name="country">
                                <option selected>Country...</option>
                            </select>
                        <!-- <i class="fal fa-globe-americas fa-lg signup_field_icons"></i> -->
                        </div>

                    </div>

                    <div class="form-row">

                        <div class="col">
                            <!--            <hr/>-->
                            <div class="checkbox justify-content-center" id="tosDiv">
                                <div class="form-inline45884554">
                                    <label>
                                        <input type="checkbox" id="tos"/>
                                        <small class="text-sm-right font-normal fs-14" style="color: #616a71"> &nbsp; I have read and agreed to the <span><a
                                                        class="fs-14" id="tooltipLink" href="./terms.php" target="_blank" style="color: #f17f57; font-weight: 700;">Terms and Conditions</a> </span></small>
                                    </label>

                                </div>
                            </div>


                        </div>
                    </div>


                    <!----------------------Signup---------------->
                    <div class="container-login100-form-btn pt-0">
                        <div class="row w-100">
                            <!--<div class="col-auto arrows prev-btn-div">
                                <button type="button" class="btn btn-primary btn-lg" id="prevBtn" ><</button>
                            </div>-->
                            <div class="col-md-5" style="padding: 0">
                                <button type="button" class="btn btn-primary btn-lg" id="signupBtn" disabled>Sign Up</button>
                            </div>
                            <!--<div class="col-auto arrows next-btn-div">
                                <button type="button" class="btn btn-primary btn-lg w-auto" id="nextBtn" >></button>
                            </div>-->
                        </div>
                    </div>

                    <div class="row w-100 m-0">

                        <div class="col-md-5" style="padding: 0">
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


                    <div class="row p-t-10 m-0 " style="width: 100%">
                        <div class="col has_account">
                            <div class="text-left" style="font-weight: 500">
                                Already have an account? <a class="txt2" href="./index.php" id="loginHyperLink" style="font-weight: bold; text-decoration: none;">
                                    Log in
                                </a>
                            </div>
                        </div>
                      <!--   <div class="col">
                            <div class="text-right">
                                <a class="txt2" href="./policy.php" target="_blank">
                                    Privacy Policy
                                </a>
                            </div>
                        </div> -->
                    </div>

                    </div>
                </div>

            </div> <!-- row sign up -->


                </div>  <!-- Frist Section Part Sign Up -->


                <div class="carousel-item">

                    <!-------------Verification Page----------------->

                    <div class="verifaction_carousel_item_areasection"> 

                        <div class="form-row">
                            <div class="form-group col justify-content-center text-center">
                                 <img src="data/images/Logo_vScription_Transcribe_Stacked.png" style="height: 110px"
                             alt="vScription"/>
                             <h5 class="verify_your_acoount_set45"> Verify Your Account </h5>
                                <label for="code" class="justify-content-center text-center mb-3 verification_code_544574">
                                    Enter the verification code sent to your email
                                </label>
                                <input type="text" class="form-control text-lg-center fs-25 col-sm-5 ml-auto mr-auto maxlength_placeholder_code78568"
                                       id="code" maxlength="6" placeholder="Code">
                                <!--<div class="valid-feedback">
                                    Looks good!
                                </div>-->
                                <button type="button" class="button_submit_class_45464" id="verifyBtn"> Verify  </button>
                                <div class="invalid-feedback">
                                    Please check your entry
                                </div>
                            </div>
                        </div>

                    </div>


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



    </form>



</div>

</body>
<noscript>
    For full functionality of this site, it is necessary to enable JavaScript.
    You can find a step-by-step instruction at <a href="https://www.enable-javascript.net">enable-javascript.net</a> to
    enable JavaScript in your web browser.
</noscript>

</html>
