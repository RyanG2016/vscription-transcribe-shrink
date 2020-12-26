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
header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
header("Pragma: no-cache"); //HTTP 1.0
//header("Expires: Sat, 26 Jul 1993 05:00:00 GMT"); // Date in the past
header("Expires: 0"); // Now
if(!isset($_POST) || !isset($_POST["package"]))
{
    ob_start();
    header('location:packages.php');
    ob_end_flush();
    die();
}
use Src\Models\Package;
//use Src\TableGateways\PackageGateway;

//$pkgGateway = new PackageGateway($dbConnection);
$pkg = Package::withID($_POST["package"], $dbConnection);
// User Setting
?>

<html lang="en">

<head>
    <title>vScription Checkout</title>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/3.4.0/imask.min.js"></script>
    <link href="data/css/payment.css?v=2" rel="stylesheet">
    <script src="data/scripts/payment.min.js?v=2" type="text/javascript"></script>

</head>

<body>

<?php include_once "data/parts/nav.php" ?>

<div id="container" style="width: 100%">
    <div class="form-style-5">

        <table id="header-tbl">
            <tr>
                <td id="navbtn" align="left" colspan="1">
<!--                    <a class=\"logout\" href="landing.php"><i class="fas fa-arrow-left"></i> Go to landing page</a>-->
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
                    <legend class="page-title"><i class="fas fa-dollar-sign"></i> Checkout</legend>
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
                <h3>Details for your order</h3>

                <hr>
                <form id="paymentForm" method="post" action="processing.php" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="package" value="<?php echo $pkg->getSrpId()?>" />
                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-sm-3 border-right">
                            <h5>You have ordered</h5>
                            <hr>

                            <?php

                            echo '
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
                            <div class="col text-right">' . $pkg->getSrpPrice() . ' $CAD</div>
                        </div>
                        <hr>
                        
                        
                        <div class="row">
                            <div class="col-auto">Account</div>
                            <div class="col text-right">' . $_SESSION['acc_name'] . '</div>
                        </div>';
                            ?>

                        </div>


                            <div class="col-lg-6 col-md-4 col-sm-5 border-right">
                                <h5>Your Details</h5>
                                <hr>
                                <div class="row no-gutters m-b-7"><b>Billing Info</b> &ensp; <small class="mt-auto vtex-help-icon" id="edit">Edit</small></div>
                                <?php
                                echo '<div class="row no-gutters">
                                    <span id="fname" class="col-auto vtex-editable-span" aria-placeholder="<first name>">'.$_SESSION["fname"] ."</span>&nbsp;
                                    <span id='lname' class='col vtex-editable-span' aria-placeholder='<last name>'>".$_SESSION["lname"].'</span>
                                    </div>';
                                echo '<div class="row no-gutters">
                                    <span id="city" class="col vtex-editable-span" aria-placeholder="<City>">';
                                echo isset($_SESSION['userData']['city']) && !empty($_SESSION['userData']['city'])?$_SESSION['userData']['city']:'';
                                echo '</span></div>';

                                echo '<div class="row no-gutters">
                                    <span id="state" class="col vtex-editable-span" aria-placeholder="<State>">';
                                echo isset($_SESSION['userData']['state']) && !empty($_SESSION['userData']['state'])?$_SESSION['userData']['state']:'';
                                echo '</span></div>';

                                echo '<div class="row no-gutters">
                                    <span id="address" class="col vtex-editable-span" aria-placeholder="<Address>">';
                                echo isset($_SESSION['userData']['address']) && !empty($_SESSION['userData']['address'])?$_SESSION['userData']['address']:'';
                                echo '</span></div>';

                                echo '<div class="row no-gutters">
                                    <span id="country" class="col vtex-editable-span" aria-placeholder="Country">';
                                echo isset($_SESSION['userData']['country']) && !empty($_SESSION['userData']['country'])?$_SESSION['userData']['country']:'';
                                echo '</span></div>';

                                ?>

                                <hr>
                                <div class="row no-gutters m-b-7"><b>Payment Details</b></div>
                                <div class="row no-gutters">

                                    <div class="col  pr-3 container preload">
                                        <div class="creditcard">
                                            <div class="front">
                                                <div id="ccsingle"></div>
                                                <svg version="1.1" id="cardfront" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                                     x="0px" y="0px" viewBox="0 0 750 471" style="enable-background:new 0 0 750 471;" xml:space="preserve">
                        <g id="Front">
                            <g id="CardBackground">
                                <g id="Page-1_1_">
                                    <g id="amex_1_">
                                        <path id="Rectangle-1_1_" class="lightcolor grey" d="M40,0h670c22.1,0,40,17.9,40,40v391c0,22.1-17.9,40-40,40H40c-22.1,0-40-17.9-40-40V40
                                C0,17.9,17.9,0,40,0z" />
                                    </g>
                                </g>
                                <path class="darkcolor greydark" d="M750,431V193.2c-217.6-57.5-556.4-13.5-750,24.9V431c0,22.1,17.9,40,40,40h670C732.1,471,750,453.1,750,431z" />
                            </g>
                            <text transform="matrix(1 0 0 1 60.106 295.0121)" id="svgnumber" class="st2 st3 st4">0123 4567 8910 1112</text>
                            <text transform="matrix(1 0 0 1 54.1064 428.1723)" id="svgname" class="st2 st5 st6"><?php echo $_SESSION["fname"] . " " . $_SESSION["lname"]?></text>
                            <text transform="matrix(1 0 0 1 54.1074 389.8793)" class="st7 st5 st8">cardholder name</text>
                            <text transform="matrix(1 0 0 1 479.7754 388.8793)" class="st7 st5 st8">expiration</text>
                            <text transform="matrix(1 0 0 1 65.1054 241.5)" class="st7 st5 st8">card number</text>
                            <g>
                                <text transform="matrix(1 0 0 1 574.4219 433.8095)" id="svgexpire" class="st2 st5 st9">01/23</text>
                                <text transform="matrix(1 0 0 1 479.3848 417.0097)" class="st2 st10 st11">VALID</text>
                                <text transform="matrix(1 0 0 1 479.3848 435.6762)" class="st2 st10 st11">THRU</text>
                                <polygon class="st2" points="554.5,421 540.4,414.2 540.4,427.9 		" />
                            </g>
                            <g id="cchip">
                                <g>
                                    <path class="st2" d="M168.1,143.6H82.9c-10.2,0-18.5-8.3-18.5-18.5V74.9c0-10.2,8.3-18.5,18.5-18.5h85.3
                            c10.2,0,18.5,8.3,18.5,18.5v50.2C186.6,135.3,178.3,143.6,168.1,143.6z" />
                                </g>
                                <g>
                                    <g>
                                        <rect x="82" y="70" class="st12" width="1.5" height="60" />
                                    </g>
                                    <g>
                                        <rect x="167.4" y="70" class="st12" width="1.5" height="60" />
                                    </g>
                                    <g>
                                        <path class="st12" d="M125.5,130.8c-10.2,0-18.5-8.3-18.5-18.5c0-4.6,1.7-8.9,4.7-12.3c-3-3.4-4.7-7.7-4.7-12.3
                                c0-10.2,8.3-18.5,18.5-18.5s18.5,8.3,18.5,18.5c0,4.6-1.7,8.9-4.7,12.3c3,3.4,4.7,7.7,4.7,12.3
                                C143.9,122.5,135.7,130.8,125.5,130.8z M125.5,70.8c-9.3,0-16.9,7.6-16.9,16.9c0,4.4,1.7,8.6,4.8,11.8l0.5,0.5l-0.5,0.5
                                c-3.1,3.2-4.8,7.4-4.8,11.8c0,9.3,7.6,16.9,16.9,16.9s16.9-7.6,16.9-16.9c0-4.4-1.7-8.6-4.8-11.8l-0.5-0.5l0.5-0.5
                                c3.1-3.2,4.8-7.4,4.8-11.8C142.4,78.4,134.8,70.8,125.5,70.8z" />
                                    </g>
                                    <g>
                                        <rect x="82.8" y="82.1" class="st12" width="25.8" height="1.5" />
                                    </g>
                                    <g>
                                        <rect x="82.8" y="117.9" class="st12" width="26.1" height="1.5" />
                                    </g>
                                    <g>
                                        <rect x="142.4" y="82.1" class="st12" width="25.8" height="1.5" />
                                    </g>
                                    <g>
                                        <rect x="142" y="117.9" class="st12" width="26.2" height="1.5" />
                                    </g>
                                </g>
                            </g>
                        </g>
                                                    <g id="Back">
                                                    </g>
                    </svg>
                                            </div>
                                            <div class="back">
                                                <svg version="1.1" id="cardback" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                                     x="0px" y="0px" viewBox="0 0 750 471" style="enable-background:new 0 0 750 471;" xml:space="preserve">
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
                                                            <rect x="42.9" y="198.6" class="st4" width="664.1" height="10.5" />
                                                            <rect x="42.9" y="224.5" class="st4" width="664.1" height="10.5" />
                                                            <path class="st5" d="M701.1,184.6H618h-8h-10v64.5h10h8h83.1c3.3,0,6-2.7,6-6v-52.5C707.1,187.3,704.4,184.6,701.1,184.6z" />
                                                        </g>
                                                        <text transform="matrix(1 0 0 1 621.999 227.2734)" id="svgsecurity" class="st6 st7">985</text>
                                                        <g class="st8">
                                                            <text transform="matrix(1 0 0 1 518.083 280.0879)" class="st9 st6 st10">security code</text>
                                                        </g>
                                                        <rect x="58.1" y="378.6" class="st11" width="375.5" height="13.5" />
                                                        <rect x="58.1" y="405.6" class="st11" width="421.7" height="13.5" />
                                                        <text transform="matrix(1 0 0 1 59.5073 228.6099)" id="svgnameback" class="st12 st13"><?php echo $_SESSION["fname"] . " " . $_SESSION["lname"]?></text>
                                                    </g>
                    </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col pl-3 border-left form-container">
                                        <div class="field-container">
                                            <label for="name">Name on card</label>
                                            <input id="name" name="name_on_card" maxlength="20" type="text" value="<?php echo $_SESSION["fname"] . " " . $_SESSION["lname"]?>" autofocus>
                                        </div>
                                        <div class="field-container">
                                            <label for="cardnumber">Card Number</label>
                                            <!--                                    <span id="generatecard">generate random</span>-->
                                            <input id="cardnumber" name="card_number" type="text" pattern="[0-9]*" inputmode="numeric">
                                            <svg id="ccicon" class="ccicon" width="750" height="471" viewBox="0 0 750 471" version="1.1" xmlns="http://www.w3.org/2000/svg"
                                                 xmlns:xlink="http://www.w3.org/1999/xlink">

                                            </svg>
                                        </div>
                                        <div class="field-container">
                                            <label for="expirationdate">Expiration (mm/yy)</label>
                                            <input id="expirationdate" name="expiry_date" type="text" pattern="[0-9]*" inputmode="numeric">
                                        </div>
                                        <div class="field-container">
                                            <label for="securitycode">Security Code</label>
                                            <input id="securitycode" name="cvv" type="text" pattern="[0-9]*" inputmode="numeric">
                                        </div>
                                    </div>
                                </div>

                                <div class="row no-gutters m-t-10"><small><em class="text-muted">We don't save your billing info or your credit card data.</em></small></div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-4 ">
                                <h5>Order Summary</h5>
                                <hr>

                                <div class="row">
                                    <div class="col-auto" id="cardType"></div>
                                    <div class="col text-right" id="cardNumberMasked"></div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-auto">Total amount</div>
                                    <div class="col text-right"><?php echo $pkg->getSrpPrice()?> $CAD </div>
                                </div>

                                <div class="form-row mt-3 justify-content-end">
                                    <button type="submit" id="payBtn" class="btn btn-primary" disabled>Pay now</button>
                                </div>
                            </div>

                    </div>
                </form>


            </div>
        </div>


    </div>
</div>

<div class="overlay" id="overlay" style="display: none">
    <div class="loading-overlay-text" id="loadingText">Processing payment..</div>
    <div class="spinner">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
        <div class="bounce3"></div>
    </div>
    <div class="text-muted">please don't refresh or click back button</div>
</div>


</body>

</html>
