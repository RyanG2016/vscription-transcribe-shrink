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
            redirect("landing.php");
        }

    } else {
        redirect("landing.php");
    }

}

isset($_SESSION['uEmail']) ? $uEmail = $_SESSION['uEmail'] : $uEmail = "";

?>


<!DOCTYPE html>
<html <?php echo $chtml ?>>

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
        var ref = <?php echo ( isset($_GET['ref']) && !empty($_GET['ref']) )? "'".$_GET['ref']."'" :0 ?>;
    </script>

    <!--    <link rel="stylesheet" type="text/css" href="data/login/vendor/bootstrap/css/bootstrap.min.css">-->

    <script src="data/login/vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <link rel="stylesheet" type="text/css" href="data/login/vendor/animate/animate.css">

    <!--  todo delete select 2  folder -->
    <!--    <link rel="stylesheet" type="text/css" href="data/login/vendor/select2/select2.min.css">-->

    <link rel="stylesheet" type="text/css" href="data/login/css/util.css?v=<?php echo $version_control ?>">
    <link rel="stylesheet" type="text/css" href="data/css/signup.css?v=<?php echo $version_control ?>">

    <!--	Tooltip 	-->
    <link rel="stylesheet" type="text/css" href="data/tooltipster/css/tooltipster.bundle.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="data/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-shadow.min.css"/>
    <script type="text/javascript" src="data/tooltipster/js/tooltipster.bundle.min.js"></script>

    <!--	Scroll bar  	-->
    <script src="data/scrollbar/jquery.nicescroll.js"></script>


    <!--<link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">-->
    <link rel="stylesheet" href="data/dialogues/jquery-confirm.min2.css">
    <script src="data/dialogues/jquery-confirm.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"
            integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg=="
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="data/css/custom-bootstrap-select.css" />

    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>

    <!--    <link rel="stylesheet" href="data/css/parts/bootstrap-override.css" />-->

        <script src="data/scripts/signup.min.js"></script>

</head>

<body>
<!--Header include -->
<div class="vtex-signup-wrap">
    <div class="vtex-signup-container">
        <form class="vtex-signup-form needs-validation" id="signupForm" autocomplete="off" novalidate>

        <span class="login100-form-title p-b-20">
            <img src="data/images/Logo_vScription_Transcribe_Pro_Stacked_White.png" style="height: 110px"/>
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

                        <div class="form-row">
                            <!----------------- 1st column ---------------->
                            <div class="col-4">
                                <!----------------------EMAIL----------------->
                                <label for="inputEmail"><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" class="form-control" id="inputEmail" placeholder="Email" name="email"
                                       required autofocus>
                                <!--                                <div class="valid-feedback">-->
                                <!--                                    Looks good!-->
                                <!--                                </div>-->
                                <div class="invalid-feedback">
                                    Please enter a valid email
                                </div>

                                <!----------------------Line Break----------------->
                                <div class="w-100 m-t-16"></div>


                                <!----------------------PASSWORD-------------->
                                <label for="inputPassword"><i class="fas fa-key"></i> Password</label>
                                <input type="password" class="form-control" id="inputPassword" placeholder="Password"
                                       name="password"
                                       title="Password Requirements"
                                       data-trigger="focus"
                                       required>
                                <!--<div class="valid-feedback">
                                    Looks good!
                                </div>-->
                                <div class="invalid-feedback">
                                    Please enter a valid password
                                </div>

                                <!----------------------Line Break----------------->
                                <div class="w-100 m-t-16"></div>

                                <!----------------------Confirm Password-------------->
                                <label for="inputConfirmPassword"><i class="fas fa-key"></i> Confirm Password</label>
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


                                <!----------------------Line Break----------------->
                                <div class="w-100 m-t-16"></div>


                                <label for="inputAccName"><i class="fas fa-sitemap"></i> Organization Name</label>
                                <input type="text" class="form-control" id="inputAccName" placeholder="" name="accname"
                                       required>

                                <div class="form-row">
                                    <!--<div class="col">
                                        <small class="text-muted">
                                            (optional)
                                        </small>
                                    </div>-->
                                    <div class="col">
                                        <div class="checkbox justify-content-start" id="haveAccDiv">
                                            <div class="form-inline justify-content-start">
                                                <label>
                                                    <input type="checkbox" id="haveAccCB"/>&nbsp;
                                                    <small class="text-muted">Don't create</small>
                                                </label>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!----------------- 2nd column ---------------->
                            <div class="col vtex-left-divider">

                                <div class="form-row m-0">
                                    <!----------------------NAME----------------->
                                    <div class="col">
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
                                    <div class="col">
                                        <label for="inputlName"><i class="fas fa-bold"></i> Last Name</label>
                                        <input type="text" class="form-control" id="inputlName" placeholder="" name="lname"
                                               required>
                                        <!--<div class="valid-feedback">
                                            Looks good!
                                        </div>-->
                                    </div>
                                </div>

                                <div class="w-100 m-t-16"></div>

                                <div class="form-row m-0">
                                    <!----------------------Postal Code----------------->

                                    <div class="col">
                                        <label for="inputZip"><i class="fas fa-atlas"></i> Address lookup <small class="text-muted">US/CA only</small></label>
                                        <input type="text" class="form-control" id="inputZip" placeholder="Zip/Postal Code">
                                    </div>

                                    <!----------------------Country----------------->
                                    <div class="col">
                                        <label for="countryBox"><i class="fas fa-globe-americas"></i> Country</label>
                                        <div class="spinner" id="countrySpin">
                                            <div class="bounce1"></div>
                                            <div class="bounce2"></div>
                                            <div class="bounce3"></div>
                                        </div>
                                        <select class="form-control show-tick country-box" id="countryBox" data-container="body"
                                                data-dropup-auto="false" name="countryID">
                                            <option selected>Loading...</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="w-100 m-t-16"></div>

                                <div class="form-row m-0">
                                    <!----------------------Address Line-------------->
                                    <div class="col">
                                        <label for="inputAddress"><i class="fas fa-map-marker-alt"></i> Address Line</label>
                                        <input type="text" class="form-control" id="inputAddress" placeholder="100 characters max" name="address">
                                    </div>

                                </div>

                                <!----------------------Line Break----------------->
                                <div class="w-100 m-t-16"></div>


                               <div class="form-row m-0">
                                   <!----------------------City----------------->
                                   <div class="col">
                                       <label for="inputCity"><i class="fas fa-city"></i> City</label>
                                       <input type="text" class="form-control" id="inputCity" placeholder="" name="city">
                                   </div>

                                   <!----------------------Province----------------->
                                   <div class="col" id="stateGroup">
                                       <label for="countryBox"><i class="fas fa-flag"></i> Province/State</label>
                                       <div class="spinner" id="stateSpin">
                                           <div class="bounce1"></div>
                                           <div class="bounce2"></div>
                                           <div class="bounce3"></div>
                                       </div>
                                       <select class="form-control show-tick state-box" id="stateBox" data-container="body"
                                               data-dropup-auto="false" name="stateID">
                                           <option selected>Loading...</option>
                                       </select>
                                   </div>
                               </div>


                            </div>
                            <!--  column end ↑   -->
                        </div>

                        <!----------------------Row 1----------------->

                        <div class="form-row">
                            <div class="col-5">

                            </div>










                            <!----------------------Line Break----------------->
                            <div class="w-100 m-t-16"></div>

                            <!----------------------Row 4----------------->





                        </div>

                    </div>

                    <div class="carousel-item">

                        <!--<div class="row justify-content-center carousel-page-title mb-3">
                            <span class="align-text-bottom"> <i class="fas fa-user-check fa-lg"></i> </span>
                            <h5 class="fs-22 align-top">&nbsp;Verification</h5>
                        </div>-->

                        <!-------------ACCOUNT-NAME----------------->

                        <div class="form-row">
                            <div class="form-group col justify-content-center text-center">
                                <label for="inputCode" class="justify-content-center text-center">
                                    Enter the verification code sent to your email
                                </label>
                                <input type="text" class="form-control text-lg-center fs-25 col-sm-5 ml-auto mr-auto" id="code" maxlength="6" placeholder="Code">
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
            <div class="checkbox justify-content-center" id="tosDiv">
                <div class="form-inline justify-content-center">
                    <label>
                        <input type="checkbox" id="tos"/>
                        <small class="text-sm-right font-italic"> &nbsp; I have read and agreed to the Terms and Services</small>
                    </label>

                </div>
            </div>


            <!----------------------Signup---------------->
            <div class="container-login100-form-btn pt-0">
                <div class="row w-100 justify-content-center">
                    <!--<div class="col-auto arrows prev-btn-div">
                        <button type="button" class="btn btn-primary btn-lg" id="prevBtn" ><</button>
                    </div>-->
                    <div class="col-6">
                        <button type="button" class="btn btn-primary btn-lg" id="signupBtn" disabled>Signup</button>
                    </div>
                    <!--<div class="col-auto arrows next-btn-div">
                        <button type="button" class="btn btn-primary btn-lg w-auto" id="nextBtn" >></button>
                    </div>-->
                </div>
            </div>

            <div class="row w-100 m-0 justify-content-center">

                <div class="col-6">
                    <div class="progress" id="formProgressDiv">
                        <div class="progress-bar" id="formProgressBar"
                             role="progressbar" style="width: 0;" aria-valuenow="0" aria-valuemin="0"
                             aria-valuemax="100"></div>
                    </div>
                </div>

                <div class="w-100"></div>

                <div class="col-6">
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
                    <a class="txt2" href="./index.php" target="_blank">
                        Login
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
</div>

<!--<div class="overlay" id="overlay">-->
<!--    <div class="loading-overlay-text" id="loadingText">Please wait..</div>-->
<!--    <div class="spinner">-->
<!--        <div class="bounce1"></div>-->
<!--        <div class="bounce2"></div>-->
<!--        <div class="bounce3"></div>-->
<!--    </div>-->
<!--</div>-->
</body>
<noscript>
    For full functionality of this site, it is necessary to enable JavaScript.
    You can find a step-by-step instruction at <a href="https://www.enable-javascript.net">enable-javascript.net</a> to
    enable JavaScript in your web browser.
</noscript>

</html>
