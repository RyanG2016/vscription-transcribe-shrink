<?php
ob_start();
header('Location: '."../logout.php");
ob_end_flush();
die();