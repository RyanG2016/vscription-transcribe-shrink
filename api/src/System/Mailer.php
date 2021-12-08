<?php

namespace Src\System;

use Src\Models\Account;
use Src\Models\Package;
use Src\Models\Payment;
use Src\Models\Role;
use Src\Models\User;
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
     * @param $mailType int 4: reset-password <br>&nbsp; 5: verify email
     * <br> &nbsp; 6: typist-invitation
     * <br> - $extra1: access_id, $extra2: roleID
     * <br> ----------------------------------------
     * <br>7: signup with typist invitation -> (adds a token to token table and signup link will have ref param with token in it)
     * <br> - $extra1: accID, $extra2: roleID
     * <br> ----------------------------------------
     * <br>10: document-complete
     * <br>15: job(s) added/uploaded
     * <br>16: user-added-via-sys-admin
     * <br>17: SR-package-receipt
     * @param $user_email string user email address
     * @param string $account_name client admin account name for email type 6
     * @param int $extra1 extra field to insert to tokens table
     * @return bool true -> OK | false -> failed to send email
     */
    public function sendEmail($mailType, $user_email, $account_name = "", $extra1 = 0, $extra2 = 0)
    {
        global $cbaselink;
        global $link;
        global $mail;
        global $emHTML;
        global $emPlain;
        global $email;
        global $accName;
        global $token;
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


                    $emHTML = file_get_contents(__DIR__ . '/../../../mail/templates/reset_pwd.html');

                    $replace_pairs = array(
                        '{{year}}'    => date("Y"),
                        '{{url}}' => $link,
                    );

                    $emHTML = strtr($emHTML, $replace_pairs);
                    $emPlain = $emHTML;


                    $sbj = "Password Reset";
                    break;

                case 5:
                    $mailingListSize = 1;
                    $token = $this->generateToken($user_email, $mailType);
                    if(!$token) return false;
                    $link = "$cbaselink/verify.php?token=$token&user=$user_email";


                    $emHTML = file_get_contents(__DIR__ . '/../../../mail/templates/verify_your_email_alt.html');
                    // $emHTML = file_get_contents(__DIR__ . '/../../../mail/templates/verify_your_email.html');     

                    $replace_pairs = array(
                        '{{year}}'    => date("Y"),
                        '{{code}}'=> $token,
                        '{{url}}' => $link
                    );

                    $emHTML = strtr($emHTML, $replace_pairs);
                    $emPlain = $emHTML;



                    $sbj = "Account Verification";
                    $mail->addBCC("sales@vtexvsi.com");
                    break;

                case 6:
                    $mailingListSize = 1;
                    $token = $this->generateToken($user_email, $mailType, $extra1, $extra2);
                    if(!$token) return false;
                    $link = "$cbaselink/accept.php?s=$token";

                    $emHTML = file_get_contents(__DIR__ . '/../../../mail/templates/user_invitation.html');

                    $replace_pairs = array(
                        '{{year}}'    => date("Y"),
                        '{{organization}}'=> $account_name,
                        '{{role}}'=> Role::withID($extra2, $this->db)->getRoleDesc(),
                        '{{url}}' => $link
                    );

                    $emHTML = strtr($emHTML, $replace_pairs);
                    $emPlain = $emHTML;

                    $sbj = "vScription Invitation";
                    $mail->addBCC("sales@vtexvsi.com");
                    break;

                case 7:
                    $mailingListSize = 1;
                    $token = $this->generateToken($user_email, $mailType, $extra1, $extra2);
                    if(!$token) return false;
                    $link = "$cbaselink/signup.php?ref=$token&email=$user_email&org=$account_name";

                    $emHTML = file_get_contents(__DIR__ . '/../../../mail/templates/signup_with_user_invitation.html');

                    $replace_pairs = array(
                        '{{year}}'    => date("Y"),
                        '{{organization}}'=> $account_name,
                        '{{role}}'=> Role::withID($extra2, $this->db)->getRoleDesc(),
                        '{{url}}' => $link,
                    );

                    $emHTML = strtr($emHTML, $replace_pairs);
                    $emPlain = $emHTML;

                    $sbj = "vScription Invitation";
                    $mail->addBCC("sales@vtexvsi.com");
                    break;

                case 10:

                    $link = "$cbaselink";

                    $emHTML = file_get_contents(__DIR__ . '/../../../mail/templates/document_complete.html');

                    $replace_pairs = array(
                        '{{year}}'    => date("Y"),
                        '{{url}}' => $link,
                    );

                    $emHTML = strtr($emHTML, $replace_pairs);
                    $emPlain = $emHTML;

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

                    $emHTML = file_get_contents(__DIR__ . '/../../../mail/templates/job_ready_for_typing.html');

                    $replace_pairs = array(
                        '{{year}}'    => date("Y"),
                        '{{organization}}'=> $account_name
                    );

                    $emHTML = strtr($emHTML, $replace_pairs);
                    $emPlain = $emHTML;

                    $sbj = "New Job(s) Ready for Typing for $account_name";
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
                    $token = $this->generateToken($user_email, 5); // verify email token
                    if(!$token) return false;
                    $link = "$cbaselink/verify.php?token=$token&user=$user_email";

                    $emHTML = file_get_contents(__DIR__ . '/../../../mail/templates/user_added.html');

                    $replace_pairs = array(
                        '{{year}}'    => date("Y"),
                        '{{verify_url}}' => $link,
                        '{{url}}' => $cbaselink,
                        '{{username}}' => $user_email,
                        '{{pass}}' => $extra1
                    );

                    $emHTML = strtr($emHTML, $replace_pairs);
                    $emPlain = $emHTML;

                    $sbj = "vScription Transcribe Pro - New User Account Created";
                    $mail->addBCC("sales@vtexvsi.com");
                    break;

                case 17:
                    /** $extra1 : payment ID */

                    $mailingListSize = 1;
//                    global $pass;
//                    $pass = $extra1;

                    // get models
                    $payment = Payment::withID($extra1, $this->db);
                    $user = User::withID($payment->getUserId(), $this->db);

                    $emHTML = file_get_contents(__DIR__ . '/../../../mail/templates/sr_package_receipt.html');
                    $paymentJson = json_decode($payment->getPaymentJson(), true);
                    $replace_pairs = array(
                        '{{date}}'    => date("d-M-Y h:m:s a"),
                        '{{year}}'    => date("Y"),
                        '{{name}}'    => $user->getFirstName() . " " . $user->getLastName(),
                        '{{email}}'  => $user->getEmail(),
                        '{{address}}'=> $user->getAddress() . ", " . $user->getCountry(),
                        '{{pkgname}}'  => $paymentJson["pkg_name"],
                        '{{pkgmin}}'   => $paymentJson["pkg_minutes"],
                        '{{taxes}}'   => $this->generateTaxes($paymentJson["taxes"],$paymentJson["pkg_price"] ),
                        '{{totalprice}}'   => $this->formatPrice($paymentJson["total_price"]),
                        '{{ref}}'   => $payment->getRefId(),
                        '{{card}}'   => $paymentJson["card"],
                    );


                    $emHTML = strtr($emHTML, $replace_pairs);
                    $emPlain = $emHTML;

                    $sbj = "vScription Transcribe Pro Purchase Receipt";
                    $mail->addBCC("sales@vtexvsi.com");
                    break;

                case 18:
                        /** $extra1 : payment ID */
    
                        $mailingListSize = 1;
    //                    global $pass;
    //                    $pass = $extra1;
    
                        // get models
                        $payment = Payment::withID($extra1, $this->db);
                        $user = User::withID($payment->getUserId(), $this->db);
    
                        $emHTML = file_get_contents(__DIR__ . '/../../../mail/templates/prepay_ts_receipt.html');
                        $paymentJson = json_decode($payment->getPaymentJson(), true);

                        $replace_pairs = array(
                            '{{date}}'    => date("d-M-Y h:m:s a"),
                            '{{year}}'    => date("Y"),
                            '{{name}}'    => $user->getFirstName() . " " . $user->getLastName(),
                            '{{email}}'  => $user->getEmail(),
                            '{{address}}'=> $user->getAddress() . ", " . $user->getCountry(),
                            '{{pkgname}}'  => "Transcription Services",
                            '{{pkgmin}}'   => $paymentJson["pkg_minutes"],
                            '{{subtotal}}'   => $this->formatPrice($paymentJson["pkg_price"] ),
                            '{{taxes}}'   => $this->generateTaxes($paymentJson["taxes"],$paymentJson["pkg_price"] ),
                            '{{totalprice}}'   => $this->formatPrice($paymentJson["total_price"]),
                            '{{ref}}'   => $payment->getRefId(),
                            '{{card}}'   => $paymentJson["card"],
                        );
    
    
                        $emHTML = strtr($emHTML, $replace_pairs);
                        $emPlain = $emHTML;
    
                        $sbj = "vScription Transcription Services Purchase Receipt";
                        $mail->addBCC("sales@vtexvsi.com");
                        break;

                default:
                    $sbj = "vScription Transcribe";
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

    function generateTaxes($taxes, $pkgPrice)
    {
        $taxesHTML = "";

        foreach ($taxes as $tax) {
            $taxesHTML .= '<tr><td>Taxes ('.$tax["code"].'- '.number_format(floatval($tax["tax"]) * 100, 0).'%)</td><td style="text-align: right">
                    '.
                    $this->formatPrice(number_format((floatval($pkgPrice) * floatval($tax["tax"])), 2))
                .'
                </td></tr>';
        }

        return$taxesHTML;
    }
    function formatPrice($price)
    {
        return "$" . number_format(floatval($price), 2) . " CAD";
    }

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
            $token = $this->getToken($reasonCode==5?6:78);
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
     * Generates a random token
     * used for verification emails
     * @param int $length token length default is 78
     * @return string generated token
     */
    function getToken($length = 78)
    {
        $length = (int)floor($length/2);
        try {
            return bin2hex(random_bytes($length));
        } catch (\Exception $e) {
            return false;
        }
    }
}