<?php
//include('../data/parts/head.php');
$vtex_page = 11;
require '../api/bootstrap.php';
include('data/parts/session_settings.php');

require('data/parts/ping.php');

if (!isset($_SESSION['loggedIn'])) {
    header('location:../logout.php');
    exit();
}
if (isset($_SESSION['counter'])) {
    unset($_SESSION['counter']);
}

use Src\Enums\PAYMENT_STATUS;
use Src\Models\Package;
use Src\Models\Payment;
use Src\Models\SR;
use Src\TableGateways\paymentGateway;

$paymentGateway = new paymentGateway($dbConnection);
$lastPayment = $paymentGateway->getLastPurchaseForCurrentUser();

if($lastPayment != null)
{
    $payment = Payment::withRow($lastPayment);
    $paymentSucceed = $payment->getStatus() == PAYMENT_STATUS::PAID;
    $paymentJson = json_decode($payment->getPaymentJson(),true);
    $pkg = Package::withID($payment->getPkgId(), $dbConnection);
    $sr = SR::withAccID($_SESSION["accID"], $dbConnection);
}
//$pkg = Package::withID($_POST["package"], $dbConnection);
// User Setting
?>

<html lang="en">

<head>
    <title>Payment Result</title>
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
        var redirectID = <?php echo $_SESSION['role'] ?>;
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

<!--    <script src="https://unpkg.com/imask"></script>-->
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/3.4.0/imask.min.js"></script>-->
    <link href="data/css/payment.css?v=2" rel="stylesheet">
<!--    <script src="data/scripts/payment.min.js?v=2" type="text/javascript"></script>-->

</head>

<body>

<?php include_once "data/parts/nav.php" ?>

<div id="container" style="width: 100%">
    <div class="form-style-5">

        <table id="header-tbl">
            <tr>
                <td id="navbtn" align="left" colspan="1">
                    <a class=\"logout\" href="landing.php"><i class="fas fa-arrow-left"></i> Go to landing page</a>
                </td>

                <td id="logbar" align="right" colspan="1">
                    Logged in as: <?php echo $_SESSION['uEmail'] ?> |
                    <!--                    </div>-->
                    <a class="logout" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </td>

            </tr>
            <tr class="spacer"></tr>
            <tr style="margin-top: 50px">
                <td class="title" align="left" width="450px">
                    <legend class="page-title"><i class="fas fa-dollar-sign"></i> Receipt</legend>
                </td>
                <!--<td align="right" rowspan="2" id="fix-td">

                    </td>-->

                <td width="300px" style="text-align: right">
                    <img src="data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
                </td>
            </tr>


        </table>

        <div class="root">
<!--            <div class="nav-bar">-->
<!---->
<!--                --><?php
//                if (isset($_SESSION["role"])) {
//                    $rl = $_SESSION["role"];
//                    if ($rl == 3) {
//                        echo "<div class=\"vtex-card nav-header first\">
//                                Navigation
//                            </div>
//                            <div class=\"nav-btns-div\">
//                                <button class=\"mdc-button mdc-button--outlined tools-button\" onclick=\"location.href='transcribe.php'\">
//                                    <div class=\"mdc-button__ripple\"></div>
//                                    <i class=\"fas fa-angle-double-right\"></i>
//                                        Go To Transcribe
//                                    </span>
//                                </button>
//                            </div>";
//                    } else if ($rl == 2) {
//                        echo "<div class=\"vtex-card nav-header first\">
//                                Navigation
//                            </div>
//                            <div class=\"nav-btns-div\">
//                                <button class=\"mdc-button mdc-button--outlined tools-button\" onclick=\"location.href='main.php'\">
//                                    <div class=\"mdc-button__ripple\"></div>
//                                    <i class=\"fas fa-angle-double-right\"></i>
//                                        Go To Job Lister
//                                    </span>
//                                </button>
//
//                                <button class=\"mdc-button mdc-button--outlined tools-button\" onclick=\"location.href='manage_typists.php'\">
//                                    <div class=\"mdc-button__ripple\"></div>
//                                    <i class=\"fas fa-keyboard\"></i>
//                                        Manage Typists
//                                    </span>
//                                </button>
//                            </div>";
//                    } else if ($rl == 1) {
//                        echo "<div class=\"vtex-card nav-header first\">
//                                Navigation
//                            </div>
//                            <div class=\"nav-btns-div\">
//                                <button class=\"mdc-button mdc-button--outlined tools-button\" onclick=\"location.href='panel/'\">
//                                    <div class=\"mdc-button__ripple\"></div>
//                                    <i class=\"fas fa-angle-double-right\"></i>
//                                        Go To Admin Panel
//                                    </span>
//                                </button>
//
//                                <button class=\"mdc-button mdc-button--outlined tools-button\" onclick=\"location.href='main.php'\">
//                                    <div class=\"mdc-button__ripple\"></div>
//                                    <i class=\"fas fa-angle-double-right\"></i>
//                                        Go To Job Lister
//                                    </span>
//                                </button>
//
//                                <button class=\"mdc-button mdc-button--outlined tools-button\" onclick=\"location.href='transcribe.php'\">
//                                    <div class=\"mdc-button__ripple\"></div>
//                                    <i class=\"fas fa-angle-double-right\"></i>
//                                        Go To Transcribe
//                                    </span>
//                                </button>
//
//
//                            </div>";
//                    }
//                }
//                ?>
<!---->
<!--                <div class="vtex-card nav-header first">Role Settings</div>-->
<!--                <div class="nav-btns-div">-->
<!--                    <button class="mdc-button mdc-button--outlined tools-button" id="changeRoleBtn">-->
<!--                        <div class="mdc-button__ripple"></div>-->
<!--                        <i class="fas fa-wrench"></i>-->
<!--                        <span class="mdc-button__label">SWITCH ACCOUNT/ROLE</span>-->
<!--                    </button>-->
<!---->
<!--                    <button class="mdc-button mdc-button--outlined tools-button" id="setDefaultRoleBtn">-->
<!--                        <div class="mdc-button__ripple"></div>-->
<!--                        <i class="fas fa-wrench"></i>-->
<!--                        <span class="mdc-button__label">-->
<!--                        Set Default-->
<!--                        </span>-->
<!--                    </button>-->
<!---->
<!--                </div>-->
<!---->
<!--            </div>-->
            <div class="vtex-card contents first">

                <!--        CONTENTS GOES HERE        -->

<!--                <span class="payment-title">Details for your order</span>-->

                <?php
                if($lastPayment==null)
                {
                    echo '<h3>No records found</h3>';
                }
                else{
                    echo "<h3>Payment Transaction Result</h3><hr>";

                ?>
                <input type="hidden" name="package" value="<?php echo $pkg->getSrpId()?>" />
                <div class="row">

                    <div class="col-lg-8 col-md-9 col-sm-9 border-right">
                        <div class="alert <?php  echo $paymentSucceed ?'alert-success':'alert-danger'?>" role="alert">
                            <?php echo $paymentJson["msg"] ?>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-3 col-sm-3 ">
                        <h5>Order Summary</h5>
                        <hr>

                        <?php

                        echo '
                                    <div class="row">
                                        <div class="col"><b>Package Details</b></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-auto">Package</div>
                                        <div class="col text-right"> ' . $pkg->getSrpName() . '</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-auto">Minutes</div>
                                        <div class="col text-right">' . $pkg->getSrpMins() . '</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-auto">Price</div>
                                        <div class="col text-right">$' . $pkg->getSrpPrice() . ' CAD</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col"><b>Transaction Details</b></div>
                                    </div>                                    
                                    <div class="row">
                                        <div class="col-auto">Reference ID</div>
                                        <div class="col text-right">' . $payment->getRefId() . '</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-auto">Card Number</div>
                                        <div class="col text-right">' . $paymentJson["card"] . '</div>
                                    </div>
                                    <hr>
                                    
                                    <div class="row">
                                        <div class="col"><b>Organization Details</b></div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-auto">Organization</div>
                                        <div class="col text-right">' . $_SESSION['acc_name'] . '</div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-auto">SR balance</div>
                                        <div class="col text-right">' . $sr->getSrMinutesRemaining() . ' min</div>
                                    </div>
                                    '
                        ;
                        ?>
                    </div>

                </div>

                <?php
                }
                ?>

            </div>
        </div>


    </div>
</div>

<!--<div class="overlay" id="overlay">
    <div class="loading-overlay-text" id="loadingText">Please wait..</div>
    <div class="spinner">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
        <div class="bounce3"></div>
    </div>
</div>-->


</body>

</html>
