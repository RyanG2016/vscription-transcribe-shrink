<?php
//include('../data/parts/head.php');
$vtex_page = 12;
require __DIR__.'/../api/bootstrap.php';
include('data/parts/session_settings.php');

require('data/parts/ping.php');

if (!isset($_SESSION['loggedIn'])) {
    header('location:../logout.php');
    exit();
}
if (isset($_SESSION['counter'])) {
    unset($_SESSION['counter']);
}

use Src\Models\Package;
use Src\Models\SR;
use Src\Payment\PaymentProcessor;
// User Setting
?>

<html lang="en">

<head>
    <title>Processing payment...</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>-->
<!--    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">-->
<!--    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">-->
<!--    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>-->
<!--    <script src="data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>-->
<!--    <script src="data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>-->
<!--    <script src="data/libs/node_modules/@material/switch/dist/mdc.switch.js"></script>-->
    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>

    <!--    Jquery confirm  -->
<!--    <link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">-->
<!--    <script src="data/dialogues/jquery-confirm.min.js"></script>-->

    <!-- BOOTSTRAP -->
<!---->
<!--    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"-->
<!--            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"-->
<!--            crossorigin="anonymous"></script>-->

<!--    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">-->
<!--    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>-->


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
//                ob_end_flush();
//                echo "hello world2";
//                ob_implicit_flush(true);
//                ob_start();
//                echo "Processing.. ";
//                ob_flush();
//                flush();

            // Get package data
            $pkg = Package::withID($_POST["package"], $dbConnection);
            // Process
            $processor = new PaymentProcessor(
                    $_POST['fname'], $_POST['lname'],
                    $_POST['address'],
                    $_POST['city'],
                    $_POST['state'],
                    $_POST['country'],
                    $_POST['zipcode'],
                    $_POST['name_on_card'],
                    $_POST['card_number'],
                    $_POST['cvv'],
                    $_POST['expiry_date'],
                    $pkg->getSrpPrice(),
                    $pkg,
                    $dbConnection
            );
            $processor->saveUserAddress();
            $error = $processor->chargeCreditCardNow();
                if(!$error)
                {
                    echo 'Payment Success! <i class="far fa-laugh-beam"></i> redirecting..';

                    // add minutes to user account
                    $sr = SR::withAccID($_SESSION["accID"], $dbConnection);
                    $sr->addToMinutesRemaining($pkg->getSrpMins());
                    $sr->save();

                }else{
                    echo 'Payment Failed! <i class="far fa-frown"></i> redirecting..';
                }
//                ob_flush();
//                flush();
//                sleep(2);
                header("HTTP/1.1 303 See Other");
                header("Location: payment-details.php");
//                ob_end_flush();

            ?>
        </small>
    </div>
</body>
</html>