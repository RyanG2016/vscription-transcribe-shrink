<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../api/vendor/PHPMailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../api/vendor/PHPMailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../api/vendor/PHPMailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../api/vendor/PHPMailer/phpmailer/src/OAuth.php';
// Load Composer's autoloader
//require 'vendor/autoload.php';

// Instantiation and passing `true` enables exceptions
global $mail;
$mail = new PHPMailer(true);

try {
    //Server settings
//    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
	$mail->Encoding = "base64";
    $mail->isSMTP();  	// Send using SMTP   
	$mail->isHTML(true);
	$mail->CharSet = "UTF-8";
	$mail->From = "mailer@vscription.com";
    $mail->Host       = 'smtp.transmail.com';                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'emailapikey';                     // SMTP username
//    $mail->Username   = 'hacker2894@gmail.com';                     // SMTP username
    $mail->Password   = '<token>';                               // SMTP password
    $mail->SMTPSecure = 'TLS';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
//    $mail->SMTPDebug  = 5;         
    $mail->Port       = 587;                                    // TCP port to connect to
	$mail->Sender = 'vsvoice@bounce.vscription.com';


    //Recipients
    $mail->setFrom('mailer@vscription.com', 'vScription Transcribe');
    //$mail->addReplyTo('noreply@vscription.com', 'vScription Transcribe');
//	$mail->addAddress('cc@example.com', 'Recipient User');     // Add a recipient TO BE ADDED FROM METHOD
//    $mail->addCC('cc@example.com');
//    $mail->addBCC('bcc@example.com');

    // Attachments
//    $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    // Content
//    $mail->isHTML(true);                                  // Set email format to HTML
//    $mail->Subject = 'Here is the subject';
//    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
//    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

//    $mail->send();
	$_SESSION['error'] = false;
//    echo 'Message has been sent';
} catch (Exception $e) {
	$_SESSION['error'] = true;
	$_SESSION['msg'] = "{$mail->ErrorInfo}";
    //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}