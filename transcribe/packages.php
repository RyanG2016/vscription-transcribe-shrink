<?php
//include('../data/parts/head.php');
$vtex_page = 10;
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
use Src\Models\Package;
use Src\TableGateways\PackageGateway;

$pkgGateway = new PackageGateway($dbConnection);
// User Setting
?>

<html lang="en">

<head>
    <title>vScription Packages</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!--    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">-->
<!--    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">-->
<!--    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>-->
<!--    <script src="data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>-->
<!--    <script src="data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>-->
<!--    <script src="data/libs/node_modules/@material/switch/dist/mdc.switch.js"></script>-->
    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>

    <!--    Jquery confirm  -->
    <link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">
    <script src="data/dialogues/jquery-confirm.min.js"></script>

    <!-- BOOTSTRAP -->

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
    <link href="data/css/packages.css?v=2" rel="stylesheet">
    <script src="data/scripts/packages.min.js?v=2" type="text/javascript"></script>
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
                    <legend class="page-title"><i class="fas fa-cubes"></i> Packages</legend>
                </td>
                <!--<td align="right" rowspan="2" id="fix-td">

                    </td>-->

                <td width="300px" style="text-align: right">
                    <img src="data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
                </td>
            </tr>


        </table>

        <div class="root">

            <div class="vtex-card contents first">

                <!--        CONTENTS GOES HERE        -->

                <table class="welcome">
                    <tr>
                        <td rowspan="2">
                            <i class="fas fa-comments-dollar welcome-icon"></i>
                        </td>
                        <td rowspan="1" style="font-size: 1.6rem;">
                            <span style="vertical-align: top"> Speech Recognition Packages</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 1rem; font-style: italic; color: dimgrey">
<!--                            <span style="vertical-align: bottom">Here you can choose your next job start by clicking <b>switch account/role</b> from the sidebar.</span>-->
                            <!--                            <span style="vertical-align: bottom">Here you can find all your assigned work and data.</span>-->
                        </td>
                    </tr>
                </table>

                <hr>
                <div class="form-row justify-content-center">

                    <?php

                $rows = $pkgGateway->findAllModel();
                foreach ($rows as $row)
                {
                    $package = Package::withRow($row);
//                                <div class="row w-auto no-gutters justify-content-center package-name m-b-10">'..'</div>
                    echo '<div class="col-sm-3 mb-4">
                        <div class="package-card-top row no no-gutters align-items-center justify-content-center">
                            '.$package->getSrpName().'
                        </div>
                        <div>
                            <div class="package-card-top-bottom">

                                <div class="row w-auto no-gutters justify-content-center package-mins m-b-10">'.$package->getSrpMins().' minutes</div>
                                <div class="row w-auto no-gutters justify-content-center">
                                    <small class="text-muted">$'.$package->getSrpPrice().' CAD</small>
                                </div>
                                <div class="row w-auto no-gutters justify-content-center">
                                    <small class="text-muted">$'.$package->getSrpDesc().' CAD</small>
                                </div>
                            </div>
                        </div>
                        <div class="package-card-bottom row no no-gutters align-items-center justify-content-center" id="'.$package->getSrpId().'">
                            Buy Now
                        </div>
                    </div>';
                }

                ?>


                    <!--<div class="col-sm-3">
                        <div>
                            <div class="package-card">
                                <div class="row w-auto no-gutters justify-content-center package-mins m-b-10">200 minutes</div>
                                <div class="row w-auto no-gutters justify-content-center package-name m-b-10">Starter</div>
                                <div class="row w-auto no-gutters justify-content-center">
                                    <small class="text-muted">100$</small>
                                </div>
                            </div>
                        </div>
                        <div class="package-card-bottom row no no-gutters align-items-center justify-content-center">
                            Purchase
                        </div>
                    </div>-->

                </div>

            </div>
        </div>


    </div>

    <form id="purchase" action="payment.php" method="post">
        <input type="number" id="package" name="package" hidden>
    </form>

</div>


</body>

</html>
