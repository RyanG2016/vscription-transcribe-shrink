<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'data/PHPMailer/src/PHPMailer.php';
require_once 'data/PHPMailer/src/SMTP.php';
require_once 'data/PHPMailer/src/Exception.php';
require_once 'data/PHPMailer/src/OAuth.php';
// Load Composer's autoloader
//require 'vendor/autoload.php';

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
//    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'subscriptions@vtexvsi.com';                     // SMTP username
//    $mail->Username   = 'hacker2894@gmail.com';                     // SMTP username
    $mail->Password   = 'plqjxqtmxldlczrp';                               // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
//    $mail->SMTPDebug  = 5;         
    $mail->Port       = 587;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom('subscriptions@vtexvsi.com', 'vScription Transcribe');
    $mail->addReplyTo('noreply@vtexvsi.com', 'vScription Transcribe');
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

?>