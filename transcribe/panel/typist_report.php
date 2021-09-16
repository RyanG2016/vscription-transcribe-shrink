<?php
//include('../data/parts/head.php');

require '../../api/vendor/autoload.php';
use Src\Enums\INTERNAL_PAGES;

$vtex_page = INTERNAL_PAGES::TYPIST_REPORTS;

include('../data/parts/head.php');

// admin panel main

//redirect to main
if ($_SESSION['role'] != "1") {
//User is a System Administrator ONLY
    ob_start();
    header('Location: '."../index.php");
    ob_end_flush();
    die();
}
?>

<html lang="en">

<head>
    <title>vScription Typist Billing Report</title>
    <link rel="shortcut icon" type="image/png" href="../data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/12f6b99df9.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link href="../data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">


    <!-- BOOTSTRAP -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/js/tempusdominus-bootstrap-4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css" />



    <!--  Datatables  -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.25/b-1.7.1/b-colvis-1.7.1/b-html5-1.7.1/b-print-1.7.1/r-2.2.9/rr-1.2.8/sl-1.3.3/datatables.min.css"/>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.72/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.72/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.25/b-1.7.1/b-colvis-1.7.1/b-html5-1.7.1/b-print-1.7.1/r-2.2.9/rr-1.2.8/sl-1.3.3/datatables.min.js"></script>


    <!--    <script src="../data/scripts/typist_report.min.js"></script>-->
    <script src="../data/scripts/typist_report.min.js"></script>
    <script src="../data/thirdparty/scripts/html2pdf.bundle.min.js"></script>
    <link href="https://printjs-4de6.kxcdn.com/print.min.css" rel="stylesheet">
    <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>

    <link href="../data/css/typist_rep.css" rel="stylesheet">

</head>

<body>

<div class="container-fluid h-100 vspt-container-fluid">
        <!--        <div class="w-100 h-100 d-flex flex-nowrap vspt-container-fluid-row">-->
        <div class="vspt-container-fluid-row d-flex">

        <?php include_once "../data/parts/nav.php"?>

        <div class="vspt-page-container">

            <div class="row">
                <div class="col">
                    <a class="logbar" href="index.php"><i class="fas fa-arrow-left"></i> Go back to Admin Panel</a>
                </div>


            </div>

            <div class="row vspt-title-row no-gutters">
                <div class="col align-items-end d-flex">
                    <legend class="page-title mt-auto">
                        <i class="fas fa-file-invoice"></i> Typist Billing Reports
                    </legend>
                </div>
                <div class="col-auto">
                    <img src="../data/images/Logo_vScription_Transcribe.png" width="300px"/>
                </div>
            </div>

            <div class="vtex-card contents">

                <div class="row typ-billing-container">
                    <div class="col">
                        <div class="vtex-table-tools w-100" id="vtexTableTools"></div>
                        <div class="report-grid billing-report-container" id="printableReport">
                            <!--                <div class="billing-report-container"></div>-->

                            <div style="overflow-x: hidden" class="vspt-table-div">
                                <table id="typistTbl" class="typist-tbl table vspt-table hover compact">
                                    <!--<tfoot>
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
                                    </tfoot>-->
                                </table>
                            </div>

                        </div>
                    </div>

                    <div class="col-auto pl-3 pr-3 ml-auto mt-md-3 mt-sm-3 border-left billing-sidebar">

                        <div class="input-group">
                            <div class="input-group" id="typistName" data-target-input="nearest">
                                <div class="input-group-prepend">
                                    <span for="typistContainer" class="mt-auto mb-auto input-group-text">
                                        Typist
                                    </span>
                                </div>


                                <select id="typistContainer" class="form-control typist-select selectpicker">
                                    <option value="loading">
                                        Loading..
                                    </option>
                                </select>

                            </div>
                        </div>


                        <div class="input-group mt-3">
                            <div class="input-group date" id="startDatePicker" data-target-input="nearest">
                                <div class="input-group-prepend">
                                    <span for="startDate" class="mt-auto mb-auto input-group-text">
                                        Start Date
                                    </span>
                                </div>
                                <input type="text" class="form-control datetimepicker-input" id="startDate"
                                       data-target="#startDatePicker"/>
                                <div class="input-group-append" data-target="#startDatePicker"
                                     data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>

                        <div class="input-group mt-3">
                            <div class="input-group date" id="endDatePicker" data-target-input="nearest">
                                <div class="input-group-prepend">
                                    <span for="endDate" class="mt-auto mb-auto input-group-text">
                                        End Date
                                    </span>
                                </div>

                                <input type="text" class="form-control datetimepicker-input" id="endDate"
                                       data-target="#endDatePicker"/>
                                <div class="input-group-append" data-target="#endDatePicker"
                                     data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>


                        <button type="button" class="btn btn-primary w-100 mt-3" id="getReport" disabled>
                            <i class="fad fa-file-spreadsheet"></i> Generate Report
                        </button>

<!--                        <hr>-->
                        <div id="reportOptions" class="vspt-summary-report-options border-top mt-3">
                            <table class="vspt-summary-table mt-3">
                                <tr>
                                    <td colspan="2">Summary</td>
                                </tr>
                                <tr>
                                    <td>
                                        Generated On
                                    </td>
                                    <td>
                                        <span id="genOn"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Total Length
                                    </td>
                                    <td>
                                        <span id="totalLength"></span><i> mins</i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Invoice Total
                                    </td>
                                    <td>
                                        <span id="totalPayable"></span> <i> $CAD</i>
                                    </td>
                                </tr>
                            </table>

                        </div>

                    </div>
                </div>


                <!--<div class="report-grid billing-report-container mt-5" id="printableReport">

                </div>-->



            </div>

        </div>
    </div>
</div>


<?php include_once "../data/parts/footer.php"?>
</body>

</html>
