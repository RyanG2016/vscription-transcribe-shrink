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

    <link rel="stylesheet" href="data/css/parts/bootstrap-override.css" />

    <!-- todo return to minified version-->
    <script src="data/scripts/signup.js"></script>

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

            <!----------------------NAME----------------->

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputfName">First Name</label>
                    <input type="text" class="form-control" id="inputfName" placeholder="" name="fname" required>
                    <div class="valid-feedback">
                        Looks good!
                    </div>
                    <div class="invalid-feedback">
                        Please enter your name.
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="inputlName">Last Name</label>
                    <input type="text" class="form-control" id="inputlName" placeholder="" name="lname" required>
                    <div class="valid-feedback">
                        Looks good!
                    </div>
                </div>
            </div>

            <!----------------------EMAIL----------------->
            <div class="form-group">
                <label for="inputEmail">Email</label>
                <input type="email" class="form-control" id="inputEmail" placeholder="Email" name="email"
                       required>
                <div class="valid-feedback">
                    Looks good!
                </div>
                <div class="invalid-feedback">
                    Please enter your email.
                </div>
            </div>

            <!----------------------PASSWORD-------------->
            <div class="form-group">
                <label for="inputPassword">Password</label>
                <input type="password" class="form-control" id="inputPassword" placeholder="Password" name="password"
                       title="Password Requirements"
                       data-trigger="focus"
                       required>
                <div class="valid-feedback">
                    Looks good!
                </div>
                <div class="invalid-feedback">
                    Please check your password.
                </div>
            </div>

            <!----------------------Confirm Password-------------->
            <div class="form-group">
                <label for="inputConfirmPassword">Confirm Password</label>
                <input type="password" class="form-control" id="inputConfirmPassword" placeholder="Confirm Password"
                       title="Confirm Password"
                       data-trigger="focus"
                       required>
                <div class="valid-feedback">
                    Looks good!
                </div>
                <div class="invalid-feedback">
                    Passwords doesn't match.
                </div>
            </div>

            <!----------------------Country--------------->
            <div class="form-group">
                <label for="countryBox">Country</label>
                <select class="form-control show-tick" id="countryBox" data-container="body" data-dropup-auto="false" name="countryID">
                    <option selected>Loading...</option>
                </select>
            </div>

            <!----------------------State----------------->

            <div class="form-group" id="stateGroup">
                <label for="countryBox">Province/State</label>
                <select class="form-control show-tick" id="stateBox" data-container="body" data-dropup-auto="false" name="stateID">
                    <option selected>Loading...</option>
                </select>
            </div>

            <!----------------------CITY----------------->
            <div class="form-group">
                <label for="inputCity">City</label>
                <input type="text" class="form-control" id="inputCity" placeholder="" name="city">
                <small id="cityHelpInline" class="text-muted">
                    (optional)
                </small>
            </div>

            <!----------------------Signup---------------->
            <div class="container-login100-form-btn">
                <div class="wrap-login100-form-btn">
                    <div class="login100-form-bgbtn"></div>
                    <button class="login100-form-btn" id="signupBtn">
                        Signup
                    </button>
                </div>
            </div>

            <div class="text-center p-t-15" id="info" hidden>
						<span class="txt1" id="btmtxt1">
							Don’t have an account?
						</span>

                <a class="txt2" href="#" id="btmtxt2" hidden>
                    Sign Up
                </a>
            </div>
        </form>
        <div class="text-right p-t-10" id="policy">
            <a class="txt2" href="./policy.php" id="btmtxt2" target="_blank">
                Privacy Policy
            </a>
        </div>
    </div>
</div>

<div class="overlay" id="overlay">
    <div class="loading-overlay-text" id="loadingText">Please wait..</div>
    <div class="spinner">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
        <div class="bounce3"></div>
    </div>
</div>
</body>
<noscript>
    For full functionality of this site, it is necessary to enable JavaScript.
    You can find a step-by-step instruction at <a href="https://www.enable-javascript.net">enable-javascript.net</a> to
    enable JavaScript in your web browser.
</noscript>

</html>
