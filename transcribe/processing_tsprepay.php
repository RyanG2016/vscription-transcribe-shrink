<?php
require __DIR__.'/../api/bootstrap.php';
// require('../support_files/FirePHPCore/fb.php');

use Src\Enums\INTERNAL_PAGES;
$vtex_page = INTERNAL_PAGES::PROCESSING;

include('data/parts/head.php');

// use Src\Models\Package;
// use Src\Models\SR;
use Src\Models\Account;
use Src\Payment\PrepayPaymentProcessor;

// fb($_SESSION);
// fb($_POST);
// fb('Log message'  ,FirePHP::LOG);
// fb('Info message' ,FirePHP::INFO);
// fb('Warn message' ,FirePHP::WARN);
// fb('Error message',FirePHP::ERROR);

?>

<!-- TO DO: Remove SR references in the page as that isn't used here -->
<html lang="en">

<head>
    <title>Processing payment...</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
<!--    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>-->
<!--    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">-->
<!--    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">-->
<!--    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>-->
<!--    <script src="data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>-->
<!--    <script src="data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>-->
<!--    <script src="data/libs/node_modules/@material/switch/dist/mdc.switch.js"></script>-->
    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>

    <!--    Jquery confirm  -->
<!--    <link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">-->
<!--    <script src="data/dialogues/jquery-confirm.min.js"></script>-->

<!-- BOOTSTRAP -->
<!---->
   <!-- <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" -->
           <!-- integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" -->
           <!-- crossorigin="anonymous"></script> -->

   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
   <!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script> -->


    <link href="data/css/processing.css?v=2" rel="stylesheet">
<!--    <script src="data/scripts/packages.min.js?v=2" type="text/javascript"></script>-->
</head>

<body>
    <div class="overlay" id="overlay">
        <div class="loading-overlay-text" id="loadingText">Processing payment..</div>
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
        <div class="text-muted">please don't refresh or click back button</div>
        <small class="text-muted">

            <?php

            // Get package data
            // $pkg = Package::withID($_POST["package"], $dbConnection);
            // $pkg->setSrpPrice(floatval(round(round(floatval($_SESSION["userData"]["bill_rate1"])*floatval($_POST["total_mins"]),2),2)));
            // $pkg->setSrpName("prepay");
            // $pkg->setSrpMinutes($_POST["total_mins"]);
            // We have different data depending on whether client using saved card or not
            if (isset($_SESSION["userData"]["profile_id"]) && !empty($_SESSION["userData"]["profile_id"])) {
                $processor = new PrepayPaymentProcessor(
                    "", "",
                    $_SESSION["userData"]["zipcode"],
                    "",
                    $_SESSION["userData"]["card_number"],  //Having a masked card doesn't seem to affect charging the profile. This card number is returned from Authorize.net.
                    $_POST['cvv'],
                    "",
                    floatval(round(round(floatval($_SESSION["userData"]["bill_rate1"])*floatval($_POST["total_mins"]),2),2)),
                    $_POST["total_mins"],
                    isset($_POST["self"]),
                    $dbConnection
                );
                $processor->saveUserAddress();
                $error = $processor->chargeSavedCreditCardNow();
            } else {
                $processor = new PrepayPaymentProcessor(
                    $_SESSION['fname'], $_SESSION['lname'],
                    $_POST['zipcode'],
                    $_POST['name_on_card'],
                    $_POST['card_number'],
                    $_POST['cvv'],
                    $_POST['expiry_date'],
                    floatval(round(round(floatval($_SESSION["userData"]["bill_rate1"])*floatval($_POST["total_mins"]),2),2)),
                    // $pkg,
                    $_POST["total_mins"],
                    isset($_POST["self"]),
                    $dbConnection
                );
                $processor->saveUserAddress();
                $error = $processor->chargeCreditCardNow();
        }
            // $processor->saveCCPostalCode();
            if(!$error)
                {
                    // echo 'Payment Success! <i class="far fa-laugh-beam"></i> redirecting..';
                    echo "<script>const loadText = document.querySelector('.text-muted');</script>";
                    echo "<script>loadText.style.display = 'none';</script>";
                    echo '<div class="alert alert-success" role="alert">
                    Payment successful, Uploading files...
                  </div>';
                    // check if minutes for self account
                    $accID = $_SESSION["accID"];
                    if(isset($_POST["self"]))
                    {
                        $accID = $_SESSION["userData"]["account"];
                    }
                    // $sr = SR::withAccID($accID, $dbConnection);
                    // $sr->addToMinutesRemaining($pkg->getSrpMins());
                    // $sr->save();
                    $currentAccount = \Src\Models\Account::withID($accID, $dbConnection);
                    error_log("Current Lifetime Minutes is: " .$currentAccount->getLifetimeMinutes(),0);
                    error_log("Comp mins for job is: " .$currentAccount->getCompMins(),0);
                    error_log("Total mins for this transaction is: " .$_POST["total_mins"]);
                    if ($currentAccount->getLifetimeMinutes() == 0) {
                        $currentAccount->setCompMins(0);
                    } else {
                        if (($currentAccount->getCompMins()-$_POST["total_mins"]) < 0) {
                            $currentAccount->setCompMins(0);  
                        } else {
                            $currentAccount->setCompMins($currentAccount->getCompMins()-$_POST["total_mins"]);
                        }
                    }
                    // Save credit card details to Authorize.net and save the profile and payment ID
                    if(isset($_POST["credit_card_status"]) && 
                        ( empty($_SESSION["userData"]["profile_id"]) || is_null($_SESSION["userData"]["profile_id"]) ) &&
                        ( empty($_SESSION["userData"]["payment_id"]) || is_null($_SESSION["userData"]["payment_id"]) )) {
                        $response = $processor->createCustomerProfile($_SESSION["userData"]["email"]);
                        $currentAccount->setProfileId($response->getCustomerProfileId());
                        $currentAccount->setPaymentId($response->getCustomerPaymentProfileIdList()[0]);
                        $_SESSION["userData"]["profile_id"] = $response->getCustomerProfileId();
                        $_SESSION["userData"]["payment_id"] = $response->getCustomerPaymentProfileIdList()[0];
                        $currentAccount->save();
                    }

                    $_SESSION["userData"]["comp_mins"] = 0.00;
                    echo "<script>localStorage.setItem('prepay_upload',true)</script>";
                    echo "<script>window.close();</script>";
                }else{
                    echo "<script>console.log(`An error occurred processing transaction`)</script>";
                    // echo 'Payment Failed! <i class="far fa-frown"></i> redirecting..';
                    echo "<script>const loadText = document.querySelector('.text-muted');</script>";
                    echo "<script>loadText.style.display = 'none';</script>";
                    echo '<div class="alert alert-danger" role="alert">
                    There was an issue processing your payment, <a href="#" onclick="function cw(){window.close();};cw()">Please click here to retry...</a>
                  </div>';
                    // if(isset($_REQUEST["destination"])){
                        // header("Location: {$_REQUEST["destination"]}");
                    // }else if(isset($_SERVER["HTTP_REFERER"])){
                        // header("Location: {$_SERVER["HTTP_REFERER"]}");
                    // }else{
                         /* some fallback, maybe redirect to index.php */
                    // }
                    // sleep(3);
                    // echo "<script>window.close();</script>";
                }
            ?>
        </small>
    </div>
</body>
</html>
