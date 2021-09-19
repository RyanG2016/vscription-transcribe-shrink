<?php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Finder</title>
    <link rel="shortcut icon" type="image/png" href="data/images/favicon.png"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!--  $this related  -->
    <script src="data/scripts/finder.min.js?v=3" type="text/javascript"></script>
<!--    <script src="data/scripts/finder.js" type="text/javascript"></script>-->


    <!--  MDC Components  -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="data/libs/node_modules/material-components-web/dist/material-components-web.js"></script>

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

    <link href='data/css/finder.css?v=2' type='text/css' rel='stylesheet' />
</head>

<body>
    <div class="vtex-card contents">
        <table id="jobs-tbl" class="jobs-tbl table vspt-table hover compact" style="width:100%">
            <!--  <thead>
              <tr>
                  <th>ID</th>
                  <th>Account</th>
                  <th>Prefix</th>
              </tr>
              </thead>-->
            <tfoot>
                <tr>
                    <?php if($_GET["col"])
                        for( $i= 0 ; $i < $_GET["col"] ; $i++ ) echo '<th></th>';
                        ?>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
