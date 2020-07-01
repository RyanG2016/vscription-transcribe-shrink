<?php

$emHTML= "<b>Hi $email,</b>
		
<br/><br/><br/>
You're receiving this email because you requested a password reset for your vScription Transcribe Pro Account. If you did not request this change, you can safely ignore this email.
<br/><br/>
To choose a new password and complete your request, please follow the link below:

<div style='word-break: break-all;'>
   <a href='$link' style='color: #4dafd4; font-family:arial; font-family:sans-serif; font-size: 16px; font-weight: bold; line-height: 150%; text-align: center; text-decoration: none;'><b>$link</b></a>
   </div>
   <br>
   If it is not clickable, please copy and paste the URL into your browser's address bar.<br><br>
   
   Thanks.
   ";

$emPlain = "Hi $email,
		

You're receiving this email because you requested a password reset for your vScription Transcribe Pro Account. If you did not request this change, you can safely ignore this email.

To choose a new password and complete your request, please follow the link below:

$link

If it is not clickable, please copy and paste the URL into your browser's address bar.

Thanks.";

?>