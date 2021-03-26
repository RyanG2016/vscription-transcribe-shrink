<?php
function insertAuditLogEntry($con, $args) {
    /* Insert Audit Data Template
            $ip = getIP();

            $a = Array(
                'email' => $_SESSION['uEmail'],
                'activity' => 'Loading audio file into player',
                'actPage' => 'transcribe.php',
                //'actPage' => header('Location: '.$_SERVER['REQUEST_URI']),   //This isn't working. For now am going to hardcode the page into the function call
                'actIP' => $ip,
                'acc_id' => '1'
            );
            $b = json_encode($a);
            insertAuditLogEntry($con, $b);
    */
    //INSERT AUDIT LOG DATA
    $a = json_decode($args,true);
    $email = strtolower($a["email"]);
    //$actDate = gmdate("Y-m-d\TH:i:s\Z"); //Lets' try using the TIMESTAMP field in mySQL instead
    $activity = $a['activity'];
    $actPage = $a['actPage'];
    $ip = $a['actIP'];
    //$acc_id = "1";
    $acc_id = $a['acc_id'];

    $sql = "INSERT INTO act_log(username, acc_id, actPage, activity, ip_addr) VALUES(?,?,?,?,?)";
    //echo $sql;

    if($stmt = mysqli_prepare($con, $sql)){

        $stmt->bind_param("sisss", $email, $acc_id, $actPage, $activity, $ip);

        $a = mysqli_stmt_execute($stmt);
        if($a){
            //echo "audit table record added succesfully!";

            //
        } else{
            //echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);

        }
    }
    //Again we're not closing the $con as we will need it and when we return to the calling switch statement it should close there
}

function insertMaintenanceAuditLogEntry($con, $args) {
    /* Insert Maintenance Audit Data Template
            $a = Array(
                'maint_table' => 'payments',
                'maint_recs_affected' => '0',
                'maint_comments' => 'Maintenance for payments table successful'
            );
            $b = json_encode($a);
            insertMaintenanceAuditLogEntry($con, $b);
    */
    //INSERT AUDIT LOG DATA
    $a = json_decode($args,true);
    $maint_table = $a['maint_table'];
    $maint_recs_affected = $a['maint_recs_affected'];
    $maint_comments = $a['maint_comments'];

    $sql = "INSERT INTO maintenance_log(maint_table, maint_recs_affected, maint_comments) VALUES(?,?,?)";
    //echo $sql;

    if($stmt = mysqli_prepare($con, $sql)){

        $stmt->bind_param("sis", $maint_table, $maint_recs_affected, $maint_comments);

        $a = mysqli_stmt_execute($stmt);
        if($a){
            echo "Maintenance table record added successfully!";

        } else{
            echo "ERROR: Could not execute $sql. " . mysqli_error($con);

        }
    }
    //Again we're not closing the $con as we will need it and when we return to the calling switch statement it should close there
}

function getIP2()
{
    return getenv('HTTP_CLIENT_IP')?:
        getenv('HTTP_X_FORWARDED_FOR')?:
            getenv('HTTP_X_FORWARDED')?:
                getenv('HTTP_FORWARDED_FOR')?:
                    getenv('HTTP_FORWARDED')?:
                        getenv('REMOTE_ADDR');
}

function encodeStr($str) // encodes string entities as (') to show correctly in html
{
    return htmlentities($str, ENT_QUOTES);
}

function generateResponse($data, $error, $empty=false)
{
    $a = Array(
        'data' => $data,
        'no_result' => $empty,
        'error' => $error
    );
    return json_encode($a);
}