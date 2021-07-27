<!DOCTYPE html>
<html lang="en">

<?php

require '../api/vendor/autoload.php';
use Src\Enums\INTERNAL_PAGES;

$vtex_page = INTERNAL_PAGES::JOB_LISTER;

include('data/parts/head.php');
include('data/parts/constants.php');

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] != "2" && $_SESSION['role'] != "1" && $_SESSION['role'] != "5") {
        header('location:index.php');
    }
}
else {
        header('location:index.php');
}

?>

<head>
    <?php include_once("gaTrackingCode.php");?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <noscript>
        <meta http-equiv="refresh" content="0;url=noscript.php">
    </noscript>
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!--  MDC Components  -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>



    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>

    <title>vScription Job Lister</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>

    <!--	Scroll Bar Dependencies    -->

<!--    <script src="data/scrollbar/jquery.nicescroll.js"></script>-->

    <!--  JQUERY  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="data/dialogues/jquery-confirm.min.css">
    <script src="data/dialogues/jquery-confirm.min.js"></script>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.ui.position.js"></script>


    <!--	///// End of scrollbar depdns   /////-->


    <!-- BOOTSTRAP -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>


    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/4.0.0/material-components-web.min.css"/>

    <!--  Datatables  -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

    <!--  css  -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" crossorigin="anonymous">



    <!--	Tooltip 	-->
    <link rel="stylesheet" type="text/css" href="data/tooltipster/css/tooltipster.bundle.min.css" />
    <link rel="stylesheet" type="text/css" href="data/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-punk.min.css" />
    <script type="text/javascript" src="data/tooltipster/js/tooltipster.bundle.min.js"></script>

	
	 <!-- Enjoyhint library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/kineticjs/5.2.0/kinetic.js"> </script>
    <link href="data/thirdparty/enjoyhint/enjoyhint.css" rel="stylesheet">
    <script src="data/thirdparty/enjoyhint/enjoyhint.min.js"></script>

	<?php $tuts=(isset($_SESSION['tutorials']))?$_SESSION['tutorials']:'{}'; ?>
    <script type="text/javascript">
        var tutorials='<?php echo $tuts;?>';
    </script>
	
	<link rel="stylesheet" href="data/css/main.css?v=1">
	<script src="data/scripts/main.min.js?v=9"></script>
	
</head>

<body>

<div class="container-fluid h-100 vspt-container-fluid">
        <!--        <div class="w-100 h-100 d-flex flex-nowrap vspt-container-fluid-row">-->
        <div class="vspt-container-fluid-row d-flex">

        <?php include_once "data/parts/nav.php"?>

        <div class="vspt-page-container">

<!--            <div class="row">-->
<!--                <div class="col">-->
<!--                    <a class="logbar" href="landing.php"><i class="fas fa-arrow-left"></i> Go back to landing page</a>-->
<!--                </div>-->
<!---->
<!---->
<!--            </div>-->

            <div class="row vspt-title-row no-gutters">
                <div class="col align-items-end d-flex">
                    <legend class="page-title mt-auto">
                        <span class="fas fa-list-alt fa-fw mr-3"></span>
                        vScription Transcribe Pro Job Lister
                    </legend>
                </div>
                <div class="col-auto">
                    <img src="data/images/Logo_vScription_Transcribe.png" width="300px"/>
                </div>
            </div>

            <div class="vtex-card contents">

                <div class="vtex-top-bar form-row m-0">
                    <h2 class="users-tbl-title col">Jobs List</h2>
<!--                    <a href="https://vscriptionpro.helpdocsonline.com/" class="col-auto mt-auto mb-auto" target="_blank" title="">Need help <i class="far fa-question-circle"></i></a>-->

                    <button class="mdc-button mdc-button--unelevated mr-2 foo-button" id="newupload_btn">
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">cloud_upload</i>
                        <span class="mdc-button__label">Upload Jobs</span>
                    </button>

                    <button class="mdc-button col-auto pr-2 pl-2 mdc-button--unelevated refresh-button" id="refresh_btn">
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">refresh</i>
                        <span class="mdc-button__label">Refresh</span>
                    </button>
                </div>


                <div style="overflow-x: hidden" class="vspt-table-div">
                    <table id="jobs-tbl" class="users-tbl table vspt-table hover compact">
                        <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="row mt-3 pt-2 border-top">
                    <div class="col" id="tjd">

                    </div>
                    <div class="col-auto" id="cbm"></div>
                </div>
                <div class="row mt-0 pt-0">
                    <div class="col-auto" id="jlr"></div>
                </div>
            </div>

        </div>
    </div>
</div>


<?php include_once "data/parts/footer.php"?>
</body>

</html>
