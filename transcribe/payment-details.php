<?php
//include('../data/parts/head.php');
require '../api/vendor/autoload.php';
use Src\Enums\INTERNAL_PAGES;

$vtex_page = INTERNAL_PAGES::PAYMENT_DETAILS;
require '../api/bootstrap.php';
include('data/parts/head.php');

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
    $sr = SR::withAccID($paymentJson["acc_id"], $dbConnection);
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


<div class="container-fluid h-100 vspt-container-fluid">
        <div class="w-100 h-100 d-flex flex-nowrap vspt-container-fluid-row">

        <?php include_once "data/parts/nav.php"?>

        <div class="vspt-page-container">

            <div class="row">
                <div class="col">
                    <a class="logbar" href="/"><i class="fas fa-arrow-left"></i> Home</a>
                </div>


            </div>

            <div class="row vspt-title-row no-gutters">
                <div class="col align-items-end d-flex">
                    <legend class="page-title mt-auto">
                        <i class="fas fa-dollar-sign"></i> Receipt
                    </legend>
                </div>
                <div class="col-auto">
                    <img src="data/images/Logo_vScription_Transcribe.png" width="300px"/>
                </div>
            </div>

            <div class="vtex-card contents">

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
                                        <div class="col text-right">$' . number_format($pkg->getSrpPrice(), 2) . ' CAD</div>
                                    </div>
                                    <hr>
                                    ';

                            foreach ($paymentJson["taxes"] as $tax) {
                                echo '<div class="row">
                                        <div class="col-auto">Taxes ('.$tax["code"].'-' . floatval($tax["tax"] )*100 .'%)</div>
                                        <div class="col text-right">$' . number_format(( $pkg->getSrpPrice() * $tax["tax"] ),2) . ' CAD</div>
                                </div>';
                            }


                            echo '
                                    <div class="row mt-3">
                                        <div class="col-auto">Total (incl tax.)</div>
                                        <div class="col text-right">$' . $payment->getAmount() . ' CAD</div>
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
                                        <div class="col text-right">' . $paymentJson["acc_name"] . '</div>
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


<?php include_once "data/parts/footer.php"?>

</body>

</html>
