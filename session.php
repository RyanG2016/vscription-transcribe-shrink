<?php
session_start();
include('data\parts\config.php');
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
	
?>

</body>
</html>