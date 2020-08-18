<?php

namespace Src\System;

use Src\TableGateways\logger;
use Src\TableGateways\UserGateway;

date_default_timezone_set('America/Winnipeg');
include_once(__DIR__ . '/../../../transcribe/data/parts/constants.php');
include_once(__DIR__ . "/../../../mail/mail_init.php");

class Mailer
{
     private $db;
     private $logger;
     private $userGateway;
     private $API_NAME = "Mailer";

    public function __construct($db)
    {
        $this->db = $db;
        $this->logger = new logger($db);
        $this->userGateway = new UserGateway($db);
    }

    // * @param $mailType int reset-password: 0 | sign-up -> 1 | password reset -> 4 | verify email: 5
    /**
     * Token is internally generated and inserted to tokens table
     * @param $mailType int reset-password 0 | verify email: 5 | document-complete: 10 | job added: 15
     * @param $user_email string user email address
     * @return bool true -> OK | false -> failed to send email
     */
    public function sendEmail($mailType, $user_email)
    {
        global $cbaselink;
        global $link;
        global $mail;
        global $emHTML;
        global $emPlain;
        global $email;
        $email = $user_email;

        $token = $this->generateToken($user_email, $mailType);
        if(!$token) return false;

        $link = "$cbaselink/verify.php?token=$token";
        try {
            switch ($mailType) {
                case 0:
                    include(__DIR__ . '/../../../mail/templates/reset_pwd.php');
                    $sbj = "Password Reset";
                    break;

                case 5:
                    include(__DIR__ . '/../../../mail/templates/verify_your_email.php');
                    $sbj = "Account Verification";
//                $mail->addCC("sales@vtexvsi.com");
                    break;

                case 10:
                    include(__DIR__ . '/../../../mail/templates/document_complete.php');
                    $sbj = "New Document(s) Ready for Download";
//                    $mail->addCC("sales@vtexvsi.com"); // duplicate do not uncomment
                    $emailsArray = $this->userGateway->getClientAccAdminsEmailForJobUpdates();
                    if(sizeof($emailsArray) > 0)
                    {
                        foreach ($emailsArray as $row) {
                            $mail->addCC($row["email"]);
                        }
                    }
                    break;

                case 15:
                    include(__DIR__ . '/../../../mail/templates/job_ready_for_typing.php');
                    $sbj = "New Job(s) Ready for Typing";
                    $emailsArray = $this->userGateway->getCurrentTypistsForJobUpdates();
                    if(sizeof($emailsArray) > 0)
                    {
                        foreach ($emailsArray as $row) {
                            $mail->addCC($row["email"]);
                        }
                    }
//                    $mail->addCC("sales@vtexvsi.com"); // duplicate do not uncomment
                    break;

                default:
                    $sbj = "vScription Transcribe Pro";
                    break;
            }

            $mail->addAddress($email); //recipient
            $mail->Subject = $sbj;
            $mail->Body = $emHTML;
            $mail->AltBody = $emPlain;

            $result = $mail->send();
            $this->logger->insertAuditLogEntry($this->API_NAME, "$sbj email sent to '$email'");
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
     * @param $reasonCode 5 -> email verification |
     * @return string token or (false) if failed
     */
    public function generateToken($email ,$reasonCode)
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
                   token_type) 
               values(?, ?, ?, ?)
        ;";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(
                array(
                    $email,
                    $token,
                    0,
                    $reasonCode
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