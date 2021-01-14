<?php
//include('../data/parts/head.php');

require '../../api/vendor/autoload.php';
use Src\Enums\INTERNAL_PAGES;

$vtex_page = INTERNAL_PAGES::BILLING_REPORTS;
include('../data/parts/head.php');

// admin panel main

//redirect to main
if (!isset($_SESSION['role']) || $_SESSION['role'] != "1") {
//User is a System Administrator ONLY
    ob_start();
    header('Location: '."../index.php");
    ob_end_flush();
    die();
}

?>

<html lang="en">

<head>
    <title>vScription Billing Reports</title>
    <link rel="shortcut icon" type="image/png" href="../data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/00895b9561.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <link href="../data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="../data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>
    <script src="../data/libs/node_modules/@material/textfield/dist/mdc.textfield.js"></script>
    <script src="../data/libs/node_modules/@material/linear-progress/dist/mdc.linearProgress.js"></script>


    <!-- BOOTSTRAP -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/js/tempusdominus-bootstrap-4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css" />



    <script src="../data/scripts/billing_report.min.js"></script>
    <script src="../data/thirdparty/scripts/html2pdf.bundle.min.js"></script>
    <link href="https://printjs-4de6.kxcdn.com/print.min.css" rel="stylesheet">
    <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>

    <link href="../data/css/billing_rep.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid d-flex h-auto vspt-container-fluid">
    <div class="row w-100 h-100 vspt-container-fluid-row no-gutters" style="white-space: nowrap">

        <?php include_once "../data/parts/nav.php"?>

        <div class="vspt-page-container vspt-col-auto-fix">

            <div class="row">
                <div class="col">
                    <a class="logbar" href="index.php"><i class="fas fa-arrow-left"></i> Go back to Admin Panel</a>
                </div>


            </div>

            <div class="row vspt-title-row no-gutters">
                <div class="col align-items-end d-flex">
                    <legend class="page-title mt-auto">
                        <i class="fas fa-file-invoice-dollar"></i> Client Billing Reports
                    </legend>
                </div>
                <div class="col-auto">
                    <img src="../data/images/Logo_vScription_Transcribe_Pro_White.png" width="300px"/>
                </div>
            </div>

            <div class="vtex-card contents">
                <div class="row typ-billing-container">
                    <div class="col-auto">
                        <label for="accountID">Account ID
                            <span class="find-acc-icon" id="findAccBtn">
                        <i class="material-icons mdc-button__icon" aria-hidden="true">person_search</i>
                    </span>
                        </label>
                        <input id="accountID" type="text" contenteditable="true" size="4" />
                    </div>

                    <div class="col">
                        <div class="input-group date" id="startDatePicker" data-target-input="nearest">
                            <label for="startDate" class="mt-auto mb-auto">Start Date</label>
                            <input type="text" class="form-control datetimepicker-input" id="startDate" data-target="#startDatePicker"/>
                            <div class="input-group-append" data-target="#startDatePicker" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group date" id="endDatePicker" data-target-input="nearest">
                            <label for="endDate" class="mt-auto mb-auto">End Date</label>
                            <input type="text" class="form-control datetimepicker-input" id="endDate" data-target="#endDatePicker"/>
                            <div class="input-group-append" data-target="#endDatePicker" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button class="mdc-button mdc-button--unelevated tools-button" id="getReport">
                            <div class="mdc-button__ripple"></div>

                            <span class="mdc-button__label">Retrieve Report</span>
                        </button>
                    </div>
                    <div class="col-auto">
                        <button class="mdc-button mdc-button--unelevated tools-button" id="getPDF" disabled>
                            <div class="mdc-button__ripple"></div>
                            <i class="fas fa-file-pdf"></i>
                            <span class="mdc-button__label">&nbsp;PDF</span>
                        </button>
                    </div>
                    <div class="col-auto">
                        <button class="mdc-button mdc-button--unelevated tools-button" id="getPrint" disabled>
                            <div class="mdc-button__ripple"></div>
                            <i class="fas fa-print"></i>
                            <span class="mdc-button__label">&nbsp;text</span>
                        </button>
                    </div>

                </div>



                <div class="report-grid billing-report-container mt-5" id="printableReport">
                    <!--                <div class="billing-report-container"></div>-->
                </div>

            </div>

        </div>
    </div>
</div>



<?php include_once "../data/parts/footer.php"?>
</body>

</html>
