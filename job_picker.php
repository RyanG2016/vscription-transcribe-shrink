<?php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Job Picker</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <!--  $this related  -->
    <script src="data/scripts/job_picker.min.js" type="text/javascript"></script>
    <link href='data/css/job_picker.css' type='text/css' rel='stylesheet' />

    <!--  MDC Components  -->
    <link href="data/libs/node_modules/material-components-web/dist/material-components-web.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>

    <!--  Data table Jquery helping libs  -->
    <link rel="stylesheet" type="text/css" href="data/libs/DataTables/datatables.css"/>
    <script type="text/javascript" src="data/libs/DataTables/datatables.js"></script>

    <!--	Tooltip 	-->
    <link rel="stylesheet" type="text/css" href="data/tooltipster/css/tooltipster.bundle.min.css" />
    <link rel="stylesheet" type="text/css" href="data/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-punk.min.css" />
    <script type="text/javascript" src="data/tooltipster/js/tooltipster.bundle.min.js"></script>

</head>

<body>

    <div class="mdc-data-table">
        <!--Job table goes here-->
        <table class="mdc-data-table__table jobs_tbl" aria-label="Jobs List">

        </table>
    </div>

</body>

</html>
