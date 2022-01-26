<?php
//include('../data/parts/head.php');

require '../api/vendor/autoload.php';
// require('../support_files/FirePHPCore/fb.php');
use Src\Enums\INTERNAL_PAGES;

$vtex_page = INTERNAL_PAGES::PAYMENT;

require '../api/bootstrap.php';
include('data/parts/head.php');
header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
header("Pragma: no-cache"); //HTTP 1.0
//header("Expires: Sat, 26 Jul 1993 05:00:00 GMT"); // Date in the past
header("Expires: 0"); // Now
use Src\Payment\PrepayPaymentProcessor;
// use Src\Models\Package;
// $pkg = Package::withID($_POST["package"], $dbConnection);
// fb($_SESSION);
// fb($_POST);
// fb('Log message'  ,FirePHP::LOG);
// fb('Info message' ,FirePHP::INFO);
// fb('Warn message' ,FirePHP::WARN);
// fb('Error message',FirePHP::ERROR);
// User Setting
// If the customer has a saved payment profile ID, created PrepayPaymentProcessor with needed data
// I think we may be able to create this object with no data for this call but we'll just leave it for now
if (isset($_SESSION["userData"]["profile_id"]) && !empty($_SESSION["userData"]["profile_id"])){
    $processor = new PrepayPaymentProcessor(
        $_SESSION['fname'], $_SESSION['lname'],
        '',
        '',
        '',
        '',
        '',
        '',
        $_SESSION["userData"]["bill_rate1"],
        $_POST["total_mins"],
        '',
        $dbConnection
    );
    $getPaymentDetails = $processor->getCustomerPaymentProfile($_SESSION["userData"]["profile_id"],$_SESSION["userData"]["payment_id"]);
    $_SESSION["userData"]["zipcode"] = $getPaymentDetails->getPaymentProfile()->getbillTo()->getzip();
    $_SESSION["userData"]["card_number"] = $getPaymentDetails->getPaymentProfile()->getPayment()->getCreditCard()->getCardNumber();
    // $_SESSION["userData"]["expiration_date"] = $getPaymentDetails->getPaymentProfile()->getPayment()->getCreditCard()->getExpirationDate();
    $_SESSION["userData"]["card_type"] = $getPaymentDetails->getPaymentProfile()->getPayment()->getCreditCard()->getCardType();
}
?>

<html lang="en">

<head>
    <title>vScription Transcribe Checkout</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png" />
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>
    <script src="data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>
    <script src="data/libs/node_modules/@material/switch/dist/mdc.switch.js"></script>
    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>

    <!--    Moment + Jquery confirm  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
        integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.33/moment-timezone-with-data-1970-2030.min.js"
        integrity="sha512-FOmgceoy0+6TMqXphk6oiZ6OkbF0yKaapTE6TSFwixidHNPt3yVnR3IRIxJR60+JWHzsx4cSpYutBosZ8iBA1g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <!-- BOOTSTRAP -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"
        integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg=="
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="data/css/custom-bootstrap-select.css" />

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
        integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous">
    </script>

    <script type="text/javascript">
    <?php
            $roleIsSet = (!isset($_SESSION['role']) && !isset($_SESSION['accID']))?0:true;
        ?>
    var roleIsset = <?php echo $roleIsSet ?>;
    // var pkgPrice = php echo $pkg->getSrpPrice() ?>;
    var redirectID = <?php echo $_SESSION['role'] ?>;
    </script>

    <!-- Enjoyhint library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/kineticjs/5.2.0/kinetic.js"></script>
    <link href="data/thirdparty/enjoyhint/enjoyhint.css" rel="stylesheet">
    <!--    <script src="data/thirdparty/enjoyhint/enjoyhint.min.js"></script>-->
    <script src="data/thirdparty/enjoyhint/enjoyhint.min.js"></script>

    <!-- <?php $tuts=(isset($_SESSION['tutorials']))?$_SESSION['tutorials']:'{}'; ?>
    <script type="text/javascript">
    var tutorials = '<?php echo $tuts;?>';
    </script> -->

    <!--    <script src="https://unpkg.com/imask"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/3.4.0/imask.min.js"></script>
    <link href="data/thirdparty/typeahead/typehead.css" rel="stylesheet">
    <script src="data/thirdparty/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>

    <script src="data/scripts/parts/ping.min.js" type="text/javascript"></script>

    <link href="data/css/payment.css?v=3" rel="stylesheet">
    <script src="data/scripts/prepayment.min.js?v=5" type="text/javascript"></script>

</head>

<body>

    <div class="container-fluid h-100 vspt-container-fluid">
        <!--        <div class="w-100 h-100 d-flex flex-nowrap vspt-container-fluid-row">-->
        <div class="vspt-container-fluid-row d-flex">

            <?php include_once "data/parts/nav.php"?>

            <div class="vspt-page-container">

                <div class="row">
                    <div class="col">
                        <!--                    <a class="logbar" href="index.php"><i class="fas fa-arrow-left"></i> Go back </a>-->
                    </div>
                </div>
                <!-- <div class="row vspt-title-row no-gutters">
                    <div class="col align-items-end d-flex">
                        <legend class="page-title mt-auto">
                            <i class="fas fa-dollar-sign"></i> Checkout
                        </legend>
                    </div>
                    <div class="col-auto">
                        <img src="data/images/Logo_vScription_Transcribe.png" width="300px" />
                    </div>
                </div> -->
                <div class="vtex-card contents w-75 pb-0">
                    <!--        CONTENTS GOES HERE        -->
                    <div class="row">
                        <div class="col-md-4">
                            <h3>Order Details</h3>
                        </div>
                        <div class="col-md-8 d-flex justify-content-end">
                            <button class="btn btn-primary" id="backBtn">Back</button>
                            <!-- <button class="btn btn-success" id="trashBtn">Trash</button> -->
                        </div>
                    </div>
                    <hr class="mb-0">
                    <!-- <div class="alert alert-danger" role="alert" display="none">
                        There was an error processing your transaction. No charges were applied to your card. Please check your information and try again.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>    
                    </div> -->
                    <form id="paymentForm" method="post" action="processing_tsprepay.php" enctype="multipart/form-data"
                        novalidate>
                        <!-- <input type="hidden" name="package" value="php echo $pkg->getSrpId()?>" /> -->
                        <?php if(isset($_POST["self"]))
                    {
                        echo '<input type="number" name="self" value="1" hidden>';
                    } ?>
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8 border-right">
 <!--                   <?php
                            echo '<div class="row no-gutters">
                                    <input id="fname" name="fname" type="text" class="col-auto vtex-editable-input typeahead" placeholder="<first name>" value="'.$_SESSION["fname"] ."\" />&nbsp;
                                    
                                    <input id='lname' name='lname' type='text' class='col vtex-editable-input typeahead' placeholder='<last name>' value=\"".$_SESSION["lname"].'" />
                                    </div>';

                            echo '<div class="row no-gutters">
                                    <input id="address" name="address" type="text" class="col vtex-editable-input typeahead" placeholder="<Address>" value="';
                            echo isset($_SESSION['userData']['address']) && !empty($_SESSION['userData']['address'])?$_SESSION['userData']['address']:'';
                            echo '"  /></div>';

                            echo '<div class="row no-gutters">
                                    <input id="city" name="city" type="text" class="col vtex-editable-input typeahead" placeholder="<City>" value="';
                            echo isset($_SESSION['userData']['city']) && !empty($_SESSION['userData']['city'])?$_SESSION['userData']['city']:'';
                            echo '"  /></div>';

                            echo '<div class="row no-gutters">
                                    <input id="state" name="state" type="text" class="col vtex-editable-input typeahead" placeholder="<Province/State>" value = "';
                            echo isset($_SESSION['userData']['state']) && !empty($_SESSION['userData']['state'])?$_SESSION['userData']['state']:'';
                            echo '" /></div>';


                            echo '<div class="row no-gutters">
                                    <input id="country" type="text" name="country" class="vtex-editable-input typeahead col w-100" placeholder="<Country>" value="';
                            echo isset($_SESSION['userData']['country']) && !empty($_SESSION['userData']['country'])?$_SESSION['userData']['country']:'';
                            echo '" />
                                   
                                </div>';
                            ?>
                            <hr>
-->
                            <div class="row no-gutters m-b-7"><b><?php if (!empty($_SESSION["userData"]["profile_id"])) {
                                    echo '<span><h5 class="pt-2">Saved Payment Details</h4><p class="manage_cards_link" style="font-weight:100;font-size: 10px;">manage saved cards</p></span>';
                                }else{
                                    echo '<h5 class="pt-2">Payment Details</h5>';
                                }
                                ?>
                                    </b></div>
                                <!-- Load the Save Card Details HTML -->
                                <?php
                                if (!empty($_SESSION["userData"]["profile_id"])) { 
                               echo '<div id="prepay-form" class="align-content-center">
                                        <div class="container-fluid mt-2">
                                                <div class="row">
                                                    <div class="col-1 mt-3">
                                                        <input class="form-check-input" type="radio" checked>
                                                    </div>
                                                    <div class="col-2 pl-2 mt-3">
                                                        <p><i class="fa fa-cc-'?><?php 
                                                        echo strtolower($_SESSION["userData"]["card_type"]);
                                                        ?><?php echo ' text-primary pr-2"></i>'?><?php echo strtoupper($_SESSION['userData']['card_type'])?><?php echo '</p>
                                                    </div>
                                                    <div class="col-4 align-self-center pr-2">XXXXXXXX'?><?php echo $_SESSION['userData']['card_number']?><?php echo '
                                                    </div>
                                                    <div class="col-3">
                                                    <input id="securitycode" class="securitycodePP mt-2" name="cvv" type="text" placeholder="CVV" pattern="[0-9]*
                                                        inputmode="numeric"
                                                        value="'?><?php echo isset($_SESSION["userData"]["security_code"])?$_SESSION["userData"]["security_code"]:'';?><?php echo '"
                                                        autofocus>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>                                          
                                    <div class="col-md-4" style="display: none">
                                            <label for="zip">Billing Zip/Postal Code</label>
                                            <br>
                                        <input id="zip" name="zipcode" type="text" class="" placeholder="<Zip/Postal Code>" value="';
                                        echo isset($_SESSION["userData"]["zipcode"]) && !empty($_SESSION["userData"]["zipcode"])?$_SESSION["userData"]["zipcode"]:'';
                                        echo '" />
                                    </div>';
                                    } else {
                                        // Load the manual card entry page details
                                echo '<div id="non-prepay-form">
                                        <div class="row no-gutters justify-content-end">
                                            <div class="col-4 m-1 container preload">
                                                <div class="creditcard">
                                                    <div class="front">
                                                        <div id="ccsingle"></div>
                                                        <svg version="1.1" id="cardfront" xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                            viewBox="0 0 750 471" style="enable-background:new 0 0 750 471;"
                                                            xml:space="preserve">
                                                            <g id="Front">
                                                                <g id="CardBackground">
                                                                    <g id="Page-1_1_">
                                                                        <g id="amex_1_">
                                                                            <path id="Rectangle-1_1_" class="lightcolor grey" d="M40,0h670c22.1,0,40,17.9,40,40v391c0,22.1-17.9,40-40,40H40c-22.1,0-40-17.9-40-40V40
                                        C0,17.9,17.9,0,40,0z" />
                                                                        </g>
                                                                    </g>
                                                                    <path class="darkcolor greydark"
                                                                        d="M750,431V193.2c-217.6-57.5-556.4-13.5-750,24.9V431c0,22.1,17.9,40,40,40h670C732.1,471,750,453.1,750,431z" />
                                                                </g>
                                                                <text transform="matrix(1 0 0 1 60.106 295.0121)" id="svgnumber"
                                                                    class="st2 st3 st4">' ?> 
                                                                <?php echo isset($_SESSION["userData"]["card_number"])?$_SESSION["userData"]["card_number"]:'' ?> 
                                                                <?php echo '</text>
                                                                <text transform="matrix(1 0 0 1 54.1064 428.1723)" id="svgname"
                                                                    class="st2 st5 st6">'?><?php echo $_SESSION["fname"] . " " . $_SESSION["lname"] ?><?php echo '</text>
                                                                <text transform="matrix(1 0 0 1 54.1074 389.8793)"
                                                                    class="st7 st5 st8">cardholder name</text>
                                                                <text transform="matrix(1 0 0 1 479.7754 388.8793)"
                                                                    class="st7 st5 st8">expiration</text>
                                                                <text transform="matrix(1 0 0 1 65.1054 241.5)"
                                                                    class="st7 st5 st8">card number</text>
                                                                <g>
                                                                    <text transform="matrix(1 0 0 1 574.4219 433.8095)"
                                                                        id="svgexpire"
                                                                        class="st2 st5 st9">'?><?php echo isset($_SESSION["userData"]["expiration_date"])?$_SESSION["userData"]["expiration_date"]:''?><?php echo '</text>
                                                                    <text transform="matrix(1 0 0 1 479.3848 417.0097)"
                                                                        class="st2 st10 st11">VALID</text>
                                                                    <text transform="matrix(1 0 0 1 479.3848 435.6762)"
                                                                        class="st2 st10 st11">THRU</text>
                                                                    <polygon class="st2"
                                                                        points="554.5,421 540.4,414.2 540.4,427.9 		" />
                                                                </g>
                                                                <g id="cchip">
                                                                    <g>
                                                                        <path class="st2" d="M168.1,143.6H82.9c-10.2,0-18.5-8.3-18.5-18.5V74.9c0-10.2,8.3-18.5,18.5-18.5h85.3
                                    c10.2,0,18.5,8.3,18.5,18.5v50.2C186.6,135.3,178.3,143.6,168.1,143.6z" />
                                                                    </g>
                                                                    <g>
                                                                        <g>
                                                                            <rect x="82" y="70" class="st12" width="1.5"
                                                                                height="60" />
                                                                        </g>
                                                                        <g>
                                                                            <rect x="167.4" y="70" class="st12" width="1.5"
                                                                                height="60" />
                                                                        </g>
                                                                        <g>
                                                                            <path class="st12" d="M125.5,130.8c-10.2,0-18.5-8.3-18.5-18.5c0-4.6,1.7-8.9,4.7-12.3c-3-3.4-4.7-7.7-4.7-12.3
                                        c0-10.2,8.3-18.5,18.5-18.5s18.5,8.3,18.5,18.5c0,4.6-1.7,8.9-4.7,12.3c3,3.4,4.7,7.7,4.7,12.3
                                        C143.9,122.5,135.7,130.8,125.5,130.8z M125.5,70.8c-9.3,0-16.9,7.6-16.9,16.9c0,4.4,1.7,8.6,4.8,11.8l0.5,0.5l-0.5,0.5
                                        c-3.1,3.2-4.8,7.4-4.8,11.8c0,9.3,7.6,16.9,16.9,16.9s16.9-7.6,16.9-16.9c0-4.4-1.7-8.6-4.8-11.8l-0.5-0.5l0.5-0.5
                                        c3.1-3.2,4.8-7.4,4.8-11.8C142.4,78.4,134.8,70.8,125.5,70.8z" />
                                                                        </g>
                                                                            <g>
                                                                            <rect x="82.8" y="82.1" class="st12" width="25.8"
                                                                                height="1.5" />
                                                                        </g>
                                                                        <g>
                                                                            <rect x="82.8" y="117.9" class="st12" width="26.1"
                                                                                height="1.5" />
                                                                        </g>
                                                                        <g>
                                                                            <rect x="142.4" y="82.1" class="st12" width="25.8"
                                                                                height="1.5" />
                                                                        </g>
                                                                        <g>
                                                                            <rect x="142" y="117.9" class="st12" width="26.2"
                                                                                height="1.5" />
                                                                        </g>
                                                                    </g>
                                                                </g>
                                                            </g>
                                                            <g id="Back">
                                                            </g>
                                                        </svg>
                                                    </div>
                                                    <div class="back">
                                                        <svg version="1.1" id="cardback" xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                            viewBox="0 0 750 471" style="enable-background:new 0 0 750 471;"
                                                            xml:space="preserve">
                                                            <g id="Front">
                                                                <line class="st0" x1="35.3" y1="10.4" x2="36.7" y2="11" />
                                                            </g>
                                                            <g id="Back">
                                                                <g id="Page-1_2_">
                                                                    <g id="amex_2_">
                                                                        <path id="Rectangle-1_2_" class="darkcolor greydark" d="M40,0h670c22.1,0,40,17.9,40,40v391c0,22.1-17.9,40-40,40H40c-22.1,0-40-17.9-40-40V40
                                    C0,17.9,17.9,0,40,0z" />
                                                                    </g>
                                                                </g>
                                                                <rect y="61.6" class="st2" width="750" height="78" />
                                                                <g>
                                                                    <path class="st3" d="M701.1,249.1H48.9c-3.3,0-6-2.7-6-6v-52.5c0-3.3,2.7-6,6-6h652.1c3.3,0,6,2.7,6,6v52.5
                                C707.1,246.4,704.4,249.1,701.1,249.1z" />
                                                                    <rect x="42.9" y="198.6" class="st4" width="664.1"
                                                                        height="10.5" />
                                                                    <rect x="42.9" y="224.5" class="st4" width="664.1"
                                                                        height="10.5" />
                                                                    <path class="st5"
                                                                        d="M701.1,184.6H618h-8h-10v64.5h10h8h83.1c3.3,0,6-2.7,6-6v-52.5C707.1,187.3,704.4,184.6,701.1,184.6z" />
                                                                </g>
                                                                <text transform="matrix(1 0 0 1 621.999 227.2734)"
                                                                    id="svgsecurity"
                                                                    class="st6 st7">'?><?php echo isset($_SESSION["userData"]["security_code"])?$_SESSION["userData"]["security_code"]:''?><?php echo '</text>
                                                                <g class="st8">
                                                                    <text transform="matrix(1 0 0 1 518.083 280.0879)"
                                                                        class="st9 st6 st10">security code</text>
                                                                </g>
                                                                <rect x="58.1" y="378.6" class="st11" width="375.5"
                                                                    height="13.5" />
                                                                <rect x="58.1" y="405.6" class="st11" width="421.7"
                                                                    height="13.5" />
                                                                <text transform="matrix(1 0 0 1 59.5073 228.6099)"
                                                                    id="svgnameback"
                                                                    class="st12 st13">'?><?php echo $_SESSION["fname"] . " " . $_SESSION["lname"]?><?php echo '</text>
                                                            </g>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row no-gutters">
                                    <div class="col pl-3 pr-3 pb-0 border-left form-container">
                                        <div class="field-container first">
                                            <label for="name" class="form-label required">Name on card</label>
                                            <br>
                                            <input id="name" class="form-control w-100" name="name_on_card" maxlength="20"
                                                type="text"
                                                value="'?><?php echo $_SESSION["fname"] . " " . $_SESSION["lname"] ?><?php echo '"
                                                >
                                        </div>

                                        <div class="field-container">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <label for="cardnumber" class="form-label required">Card Number</label>
                                                    <br>
                                                    <input id="cardnumber" class="form-control" name="card_number" type="text" pattern="[0-9xX]*"
                                                        inputmode="numeric"
                                                        value="'?><?php echo isset($_SESSION["userData"]["card_number"])?$_SESSION["userData"]["card_number"]:'';?><?php echo '" autofocus>
                                                    <svg id="ccicon" class="ccicon" width="750" height="471"
                                                        viewBox="0 0 750 471" version="1.1" xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink">

                                                    </svg>
                                                    
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="zip" class="form-label required">Billing Zip/Postal Code</label>
                                                    <br>
                                                      <input id="zip" name="zipcode" type="text" class="form-control" placeholder="" value="'?><?php echo isset($_SESSION["zipcode"]) && !empty($_SESSION["zipcode"])?$_SESSION["zipcode"]:'';?><?php echo '" />
                                                </div>
                                            </div>
                                        </div>
                                       
                                        <div class="field-container">

                                            <div class="row">
                                                <div class="col">
                                                    <label for="expirationdate" class="form-label required">Expiration (mm/yy)</label> <br>
                                                    <input id="expirationdate" class="form-control" name="expiry_date" type="text"
                                                        pattern="[0-9]*" inputmode="numeric"
                                                        value="'?><?php echo isset($_SESSION["userData"]["expiration_date"])?$_SESSION["userData"]["expiration_date"]:'';?><?php echo '"
                                                        autofocus>
                                                </div>

                                                <div class="col">
                                                    <label for="securitycode" class="form-label required">Security Code</label> <br>
                                                    <input id="securitycode" class="form-control" name="cvv" type="text" pattern="[0-9]*"
                                                        inputmode="numeric"
                                                        value="'?><?php echo isset($_SESSION["userData"]["security_code"])?$_SESSION["userData"]["security_code"]:'';?><?php echo '"
                                                        autofocus>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="field-container"
                                            style="text-align: end; vertical-align: text-bottom;">

                                            <div class="row">
                                                <div class="col-auto">
                                                    <div class="AuthorizeNetSeal mt-0">
                                                        <script type="text/javascript" language="javascript">
                                                        var ANS_customer_id = "5b6ecdfe-d529-460d-8145-acc3ade87a4c";
                                                        </script>
                                                        <script type="text/javascript" language="javascript"
                                                            src="//verify.authorize.net:443/anetseal/seal.js"></script>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                                <img src="data/images/visa_master.png"
                                                                alt="visa-master-card"></small>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="field-container">

                                        </div>

                                    </div>
                                    </div>
                                </div>';
                                }
                                ?>
                                <!-- <div class="row no-gutters m-t-10" display="none"><small><em class="text-muted">We will save your
                                            billing info for the next time but not your credit card data.</em></small>
                                </div> -->
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4 bg-light">
                                <h5 class="pt-2">Order Summary</h5>
                                <hr>

                                <?php

                            echo '
                                    <div class="row">
                                        <div class="col-auto">Total Files Uploaded</div>
                                        <div class="col text-right"> ' . $_POST["total_files"] . ' files</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-auto">Total Billed Minutes <span><i id="bill_tip" class="fas fa-question-circle"></i></span></div>
                                        <div class="col text-right">' . $_POST["total_display_minutes"] . '</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-auto">Total before tax</div>
                                        <div class="col text-right">$' . '<span id="pkgPrice">'.round(floatval($_SESSION["userData"]["bill_rate1"])*floatval($_POST["total_mins"]),2).'</span>' . ' CAD</div>
                                    </div>
                                    <hr>
                                    
                                    <span id="taxesList">
                                        <div class="row">
                                            <div class="col-auto">Taxes</div>
                                            <div class="col text-right"><span id="price_fee">5</span>%</div>
                                        </div>
                                    </span>
                                    <div class="row mt-3">
                                        <div class="col-auto">Total</div>
                                        <div class="col text-right" id="total">$' . round(round(floatval($_SESSION["userData"]["bill_rate1"])*floatval($_POST["total_mins"]),2)+round(floatval($_SESSION["userData"]["bill_rate1"])*floatval($_POST["total_mins"]),2)*(floatval(0.05)),2)  . ' CAD</div>
                                        <input type="hidden" name="total_mins" value="'.$_POST["total_mins"].'">
                                    </div>
                                    <hr>
                                    
                                    
                                    <div class="row">
                                        <div class="col-auto">Organization</div>
                                        <div class="col text-right">' .  (isset($_POST["self"]) ? $_SESSION["userData"]["admin_acc_name"] :  $_SESSION['acc_name'])  . '</div>
                                    </div>';
                            ?>
                                <div class="checkbox justify-content-center m-t-10" >
                                    <div class="form-inline justify-content-left">
                                        <label>
                                            <input type="checkbox" class="w-auto" id="accept_term" />
                                            <small class="text-sm-right fs-14"> &nbsp; I accept the <span><a
                                                        class="fs-14" id="termsLink" href="./terms.php"
                                                        target="_blank">Terms and Conditions</a></span><span style="color:red;">*</span></small>
                                        </label>
                                    </div>
                                </div>
                                <?php
                                if(!isset($_SESSION["userData"]["payment_id"]) || empty($_SESSION["userData"]["payment_id"]) || is_null($_SESSION["userData"]["payment_id"])){ 
                                    echo '<div class="checkbox justify-content-center m-t-10" >
                                    <div class="form-inline justify-content-left">
                                        <label>
                                            <input type="checkbox" name="credit_card_status" class="w-auto mr-2" id="credit_card_status" />
                                            <small class="text-sm-right fs-14">Save credit card for future transactions <span></span></small>
                                        </label>
                                    </div>
                                </div>';
                                }
                                ?> 
                                <div class="form-row justify-content-center pb-3">
                                    <button type="submit" id="payBtn" class="btn btn-primary" disabled>Pay and Upload</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<input type="hidden" id="country_name" name="" value='<?php echo $_SESSION["userData"]["country"]?>'>
    <div class="overlay" id="overlay" style="display: none">
        <div class="loading-overlay-text" id="loadingText">Processing payment..</div>
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
        <div class="text-muted">please don't refresh or click back button</div>
    </div>

    <?php include_once "data/parts/footer.php"?>
</body>
</html>