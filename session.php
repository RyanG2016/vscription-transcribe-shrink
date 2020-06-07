<?php
session_start(['cookie_lifetime' => 86400,'cookie_secure' => true,'cookie_httponly' => true]);
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


	echo "----------------------------";
    $key = ini_get("session.upload_progress.prefix") . $_POST[ini_get("session.upload_progress.name")];

    echo"Prefix: " . ini_get("session.upload_progress.prefix");// . $_POST[ini_get("session.upload_progress.name")];
    echo "</br>";
    echo "</br>";
    echo"Progress Name: " . ini_get("session.upload_progress.name");
    echo "</br>";
    echo "</br>";
    echo "Progress Enabled: " . ini_get("session.upload_progress.enabled");
    echo "</br>";
    echo "</br>";
//    echo $key;
    var_dump($_SESSION[$key]);
    echo "</br>";
    echo "</br>";

?>
	
?>

</body>
</html>
