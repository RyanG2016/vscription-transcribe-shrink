<?php

namespace Src\System;

use Src\TableGateways\logger;
use Src\TableGateways\MailingGateway;

date_default_timezone_set('America/Winnipeg');
include_once(__DIR__ . '/../../../transcribe/data/parts/constants.php');
include_once(__DIR__ . "/../../../mail/mail_init.php");

class Mailer
{
     private $db;
     private $logger;
     private $mailingGateway;
     private $API_NAME = "Mailer";

    public function __construct($db)
    {
        $this->db = $db;
        $this->logger = new logger($db);
        $this->mailingGateway = new MailingGateway($db);
    }

    // * @param $mailType int reset-password: 0 | sign-up -> 1 | password reset -> 4 | verify email: 5

    /**
     * Token is internally generated and inserted to tokens table
     * @param $mailType int  >4: reset-password <br>&nbsp; 5: verify email
     * <br> &nbsp; 6: typist-invitation
     * <br>7: signup with typist invitation -> (adds a token to token table and signup link will have ref param with token in it)
     * <br>10: document-complete
     * <br>15: job added
     * <br>16: user-added-via-sys-admin
     * <br>16: user-added-via-sys-admin
     * @param $user_email string user email address
     * @param string $account_name client admin account name for email type 6
     * @param int $extra1 extra field to insert to tokens table
     * @return bool true -> OK | false -> failed to send email
     */
    public function sendEmail($mailType, $user_email, $account_name = "", $extra1 = 0)
    {
        global $cbaselink;
        global $link;
        global $mail;
        global $emHTML;
        global $emPlain;
        global $email;
        global $accName;
        $email = $user_email;
        $accName = $account_name;
        $mailingListSize = 0;

//        $link = "$cbaselink/verify.php?token=$token";
        try {
            switch ($mailType) {
                case 4:
                    $mailingListSize = 1;
                    $token = $this->generateToken($user_email, $mailType);
                    if(!$token) return false;
                    $link = "$cbaselink/reset.php?token=$token";
                    include(__DIR__ . '/../../../mail/templates/reset_pwd.php');
                    $sbj = "Password Reset";
                    break;

                case 5:
                    $mailingListSize = 1;
                    $token = $this->generateToken($user_email, $mailType);
                    if(!$token) return false;
                    $link = "$cbaselink/verify.php?token=$token";
                    include(__DIR__ . '/../../../mail/templates/verify_your_email.php');
                    $sbj = "Account Verification";
                    $mail->addBCC("sales@vtexvsi.com");
                    break;

                case 6:
                    $mailingListSize = 1;
                    $token = $this->generateToken($user_email, $mailType, $extra1);
                    if(!$token) return false;
                    $link = "$cbaselink/secret.php?s=$token";
                    include(__DIR__ . '/../../../mail/templates/typist_invitation.php');
                    $sbj = "vScription Transcribe Pro - New Typist Account Access Granted";
                    $mail->addBCC("sales@vtexvsi.com");
                    break;

                case 7:
                    $mailingListSize = 1;
                    $token = $this->generateToken($user_email, $mailType, $extra1);
                    if(!$token) return false;
                    $link = "$cbaselink/signup.php?ref=$token";
                    include(__DIR__ . '/../../../mail/templates/signup_with_typist_invitation.php');
                    $sbj = "Typist Invitation";
                    $mail->addBCC("sales@vtexvsi.com");
                    break;

                case 10:
                    include(__DIR__ . '/../../../mail/templates/document_complete.php');
                    $sbj = "New Document(s) Ready for Download";
                    $mail->addBCC("sales@vtexvsi.com"); // duplicate do not uncomment
                    $emailsArray = $this->mailingGateway->getClientAccAdminsEmailForJobUpdates();
                    $mailingListSize = sizeof($emailsArray);
                    if($mailingListSize > 0)
                    {
                        foreach ($emailsArray as $key=>$row) {
                            if($key == 0) {
                                $email = $row["email"];
                            }else{
                                $mail->addCC($row["email"]);
                            }
                        }
                    }
                    break;

                case 15:
                    include(__DIR__ . '/../../../mail/templates/job_ready_for_typing.php');
                    $sbj = "New Job(s) Ready for Typing";
                    $emailsArray = $this->mailingGateway->getCurrentTypistsForJobUpdates();
                    $mailingListSize = sizeof($emailsArray);
                    if($mailingListSize > 0)
                    {
                        foreach ($emailsArray as $key=>$row) {
                            if($key == 0) {
                                $email = $row["email"];
                            }else{
                                $mail->addCC($row["email"]);
                            }
                        }
                    }
                    $mail->addBCC("sales@vtexvsi.com"); // duplicate do not uncomment
                    break;

                case 16:
                    $mailingListSize = 1;
//                    $token = $this->generateToken($user_email, $mailType, $extra1);
//                    if(!$token) return false;
                    $link = "$cbaselink/index.php";
                    global $pass;
                    $pass = $extra1;
                    include(__DIR__ . '/../../../mail/templates/user_added.php');
                    $sbj = "vScription Transcribe Pro - New User Account Password";
                    $mail->addBCC("sales@vtexvsi.com");
                    break;

                default:
                    $sbj = "vScription Transcribe Pro";
                    break;
            }

            if($mailingListSize > 0)
            {
                if($email) $mail->addAddress($email); //recipient
//                if($email) $mail->addAddress($email); //recipient
                $mail->Subject = $sbj;
                $mail->Body = $emHTML;
                $mail->AltBody = $emPlain;

                $result = $mail->send();
            }else{
                return true;
            }
            if($mailingListSize == 0)
            {
                $this->logger->insertAuditLogEntry($this->API_NAME, "$sbj mailing list is empty");
            }
            else if($mailingListSize > 1)
            {
                $this->logger->insertAuditLogEntry($this->API_NAME, "$sbj email sent to ".$mailingListSize . " emails");
            }else{
                $this->logger->insertAuditLogEntry($this->API_NAME, "$sbj email sent to '$email'");
            }
            return $result;
        } catch (\PHPMailer\PHPMailer\Exception $e) {
//        $_SESSION['error'] = true;  //error=1 in session
//        $_SESSION['msg'] = "Email couldn\'t be sent at this time please try again. {$mail->ErrorInfo}";
            $this->logger->insertAuditLogEntry($this->API_NAME, "[Failed] $sbj email to '$email' error: " . $mail->ErrorInfo);
            return false;
        }
    } // send Email end

    /**
     * Generates a random 78 length token, inserts it to tokens table
     * @param $reasonCode 5 -> email verification | 6 -> signup with typist invite
     * @return string token or (false) if failed
     */
    public function generateToken($email ,$reasonCode, $extra1 = 0, $extra2 = 0)
    {
        $token = null;

        while(true)
        {
            $token = $this->getToken();
            if($token != 0)
            {
                break;
            }
        }

        $statement = "
        insert into 
            tokens(
                   email,
                   identifier,
                   used,
                   token_type,
                   extra1,
                   extra2
                   ) 
               values(?, ?, ?, ?, ?, ?)
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(
                array(
                    $email,
                    $token,
                    0,
                    $reasonCode,
                    $extra1,
                    $extra2
                )
            );
            if($statement->rowCount() > 0)
            {
                return $token;
            }
            return false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Generates a random token of 78 characters length
     * used for verification emails
     * @return string generated token
     */
    function getToken()
    {
        $length = 78;
        try {
            return bin2hex(random_bytes($length));
        } catch (\Exception $e) {
            return false;
        }
    }
}