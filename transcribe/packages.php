<?php
//include('../data/parts/head.php');
require '../api/vendor/autoload.php';
use Src\Enums\INTERNAL_PAGES;

$vtex_page = INTERNAL_PAGES::PACKAGES;
require '../api/bootstrap.php';
include('data/parts/head.php');
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
    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>

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

<div class="container-fluid d-flex h-auto vspt-container-fluid">
    <div class="row w-100 h-100 vspt-container-fluid-row no-gutters" style="white-space: nowrap">

        <?php include_once "data/parts/nav.php"?>

        <div class="vspt-page-container vspt-col-auto-fix">

            <div class="row">
                <div class="col">
                    <a class="logbar" href="landing.php"><i class="fas fa-arrow-left"></i> go to home page</a>
                </div>


            </div>

            <div class="row vspt-title-row no-gutters">
                <div class="col align-items-end d-flex">
                    <legend class="page-title mt-auto">
                        <i class="fas fa-cubes"></i> Packages
                    </legend>
                </div>
                <div class="col-auto">
                    <img src="data/images/Logo_vScription_Transcribe.png" width="300px"/>
                </div>
            </div>

            <div class="vtex-card contents">

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
                                    <small class="text-muted pkg-desc">'.$package->getSrpDesc().'</small>
                                </div>
                            </div>
                        </div>
                        <div class="package-card-bottom row no no-gutters align-items-center justify-content-center" id="'.$package->getSrpId().'">
                            Buy Now
                        </div>
                    </div>';
                    }

                    ?>


                </div>
            </div>

        </div>
    </div>

    <form id="purchase" action="payment.php" method="post">
        <input type="number" id="package" name="package" hidden>
        <?php
        if(isset($_GET["self"]))
        {
            echo '<input type="number" name="self" value="1" hidden>';
        }

        ?>
    </form>

</div>


<?php include_once "data/parts/footer.php"?>
</body>

</html>
