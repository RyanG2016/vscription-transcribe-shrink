<?php
global $emHTML;
global $emPlain;
global $email;

$emHTML= "vScription Transcribe Pro <br/><br/>Hi $email,
		
<br/><br/><br/>
There are one or documents ready for download for your account. Please log in at <a href='$cbaselink'>$cbaselink</a> to access your reports. Click the cloud icon at the end of the job row to download.
<br/><br/>
   
   Thank you for using vScription Transcribe!
   ";

$emPlain = "Hi $email,
		

There are one or documents ready for download for your account. Please log in at <a href='$cbaselink'>$cbaselink</a> to access your reports. Click the cloud icon at the end of the job row to download.

Thank you for using vScription Transcribe!";
?>