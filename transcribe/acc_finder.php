<?php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Account Finder</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <!--  $this related  -->
    <script src="data/scripts/acc_finder.min.js" type="text/javascript"></script>


    <!--  MDC Components  -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>


    <!--  Data table Jquery helping libs  -->
    <link rel="stylesheet" type="text/css" href="data/libs/DataTables/datatables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/4.0.0/material-components-web.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.material.min.css"/>
    <script type="text/javascript" src="data/libs/DataTables/datatables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.material.min.js"></script>

    <link href='data/css/acc_finder.css' type='text/css' rel='stylesheet' />
</head>

<body>

    <table id="jobs-tbl" class="display" style="width:100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Account</th>
            <th>Prefix</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>ID</th>
            <th>Account</th>
            <th>Prefix</th>
        </tr>
        </tfoot>
    </table>

</body>

</html>
