<?php
include('data/parts/session_settings.php');
include('data/parts/config.php');
?>
<!DOCTYPE html>
<html>
<body>

<?php
	$ct = date("Y-m-d H:i:s");
print_r($_SESSION);
	echo '<br/>';
	echo '<br/>';
print_r(date("Y-m-d H:i:s")); //mysql format
	echo '<br/>';
	echo '<br/>';
print_r(date("Y-m-d")); //mysql format
	
	echo '<br/>';
	echo '<br/>';
	
print_r(date("Y-m").'-01'); //mysql format usage in calendar
	echo '<br/>';
	echo $_SERVER['REQUEST_TIME'].'<br/>';
	echo '<br/>';
	echo $_SESSION['lastPing'];
	echo '<br/>';
	echo '<br/>';
	echo $ct;
	echo '<br/>';
	echo '<br/>';
	echo $_SERVER['REQUEST_TIME']-strtotime($_SESSION['lastPing']);
	
	echo '<br/>';
	echo '<br/>';
	echo date_default_timezone_get();


echo "</br>";
echo "</br>";
	echo "----------------------------";
//    $key = ini_get("session.upload_progress.prefix") . $_POST[ini_get("session.upload_progress.name")];
    $key = ini_get("session.upload_progress.prefix") . "jobUpload";
echo "</br>";
echo "</br>";
    echo"Prefix: " . ini_get("session.upload_progress.prefix");// . $_POST[ini_get("session.upload_progress.name")];
    echo "</br>";
    echo "</br>";
    echo"Progress Name: " . ini_get("session.upload_progress.name");
    echo "</br>";
    echo "</br>";
    echo "Progress Enabled: " . ini_get("session.upload_progress.enabled");
    echo "</br>";
    echo "</br>";
    echo "session.auto_start: " . ini_get("session.auto_start");
    echo "</br>";
    echo "</br>";
    echo "session.upload_progress.cleanup: " . ini_get("session.upload_progress.cleanup");
    echo "</br>";
    echo "</br>";
//    echo $key;
//    var_dump($_SESSION[$key]);
    echo json_encode($_SESSION["upload_progress_job_upload"]);
    echo "</br>";
    echo "</br>";

?>

</body>
</html>
