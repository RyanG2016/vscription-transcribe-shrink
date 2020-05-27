<?php

$emHTML= "<b>Hi $email,</b>
		
<br/><br/><br/>
Please verify your email address by following the link below:
<br/><br/>

<div style='word-break: break-all;'>
   <a href='$link' style='color: #4dafd4; font-family:arial; font-family:sans-serif; font-size: 16px; font-weight: bold; line-height: 150%; text-align: center; text-decoration: none;'><b>$link</b></a>
   </div>
   <br>
   If it is not clickable, please copy and paste the URL into your browser's address bar.<br><br>
   
   Thanks.
   ";

$emPlain = "Hi $email,
		

Please verify you email address by following the link below:

To choose a new password and complete your request, please follow the link below:

$link

If it is not clickable, please copy and paste the URL into your browser's address bar.

Thanks.";

?>