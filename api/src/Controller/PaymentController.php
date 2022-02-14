<?php

namespace Src\Controller;

use Src\Helpers\common;
use Src\Models\Account;
use Src\TableGateways\PaymentGateway;
use Src\System\Mailer;
use Src\TableGateways\accessGateway;

class PaymentController
{

    private $db;
    private $requestMethod;
    private $fileId;
    private $rawURI;
    private $mailer;
    private $common;

    private $paymentGateway;
    private $accessGateway;

    public function __construct($db, $requestMethod, $fileId, $rawURI)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->fileId = $fileId;
        $this->rawURI = $rawURI;
        $this->mailer = new Mailer($db);
        $this->common = new common();

        $this->paymentGateway = new PaymentGateway($db);
        $this->accessGateway = new accessGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if (isset($_GET["cancel"])) {
                    $response = $this->cancelUpload();
                } else {
                    error_log("We don't have a method setup to get payment info yet",0)
                }
                break;
            case 'POST':
                if (isset($_POST["cancel"])) {
                    $response = $this->cancelUpload();
                } else {
                    if(isset($this->uri[0]) && $this->uri[0] == "create")
                    {
                        $response = $this->insertPurchase();
                    }
                }
                break;

            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }


    private function cancelUpload()
    {
        $suffix = "job_upload";
        $key = ini_get("session.upload_progress.prefix") . $suffix;
        $_SESSION[$key]["cancel_upload"] = true;
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(array(
            "msg" => "upload cancelled",
            "error" => false
        ));
        return $response;
    }

    private function insertPurchase() {

        //
        // $json = json_encode(array(
        //     "trans_id" => $transID,
        //     "ref_id" => $this->internalRefID,
        //     "taxes" => $this->taxesArr,
        //     "error" => $error,
        //     "card" => $this->cardNumber,
        //     "email" => $this->userModel->getEmail(),
        //     "total_price" => $this->totalPrice,
        //     // "pkg_name" => $this->package->getSrpName(),
        //     // "pkg_price" => $this->package->getSrpPrice(),
        //     // "pkg_minutes" => $this->package->getSrpMinutes(),
        //     "pkg_name" => "Transcription Services",
        //     "pkg_price" => $this->amount,
        //     "bill_rate" => $this->bill_rate,
        //     "pkg_minutes" => $this->totalMins,
        //     "acc_name" => $this->selfAccount?$_SESSION["userData"]["admin_acc_name"]:$_SESSION["acc_name"],
        //     "acc_id" => $this->selfAccount?$_SESSION["userData"]["account"]:$_SESSION["accID"],
        //     "msg" => $msg
        // ));
        //
        if isset($_POST["transID"])
        && isset($_POST["ref_id"])
        && isset($_POST["taxes"])
        && isset($_POST["card"])
        && isset($_POST["email"])
        && isset($_POST["total_price"])
        && isset($_POST["pkg_name"])
        && isset($_POST["pkg_price"])
        && isset($_POST["bill_rate"])
        && isset($_POST["pkg_minutes"])
        && isset($_POST["acc_id"])
        && isset($_POST["msg"])
    ) {
        // Payment Status Enums
        // CONST RECORDED = 0;
        // CONST PAID = 1;
        // CONST REFUNDED = 2;
        // CONST FAILED = 3;

        // Payment MOdel Model
        // (public ?int $payment_id = 0,
        //                         public int $user_id = 0,
        //                         public float $amount = 0.00,
        //                         public ?string $ref_id = null,
        //                         public ?string $trans_id = null,
        //                         public ?string $payment_json = null,
        //                         public ?int $pkg_id = null,
        //                         public int $status = PAYMENT_STATUS::RECORDED,
        //                         private $db = null

        $payment = new Payment(
            0,
            $_SESSION["uid"],
            $this->totalPrice,
            $this->internalRefID,
            $transID,
            $json,
            1,
            // $this->package->getSrpId(),
            $error?PAYMENT_STATUS::FAILED:PAYMENT_STATUS::PAID,
            $this->db
        );
        $pid = $payment->save();

        if (!$error) {
            $this->mailer->sendEmail(18, $this->userModel->getEmail(), "", $pid);
        }

        return $error;
        }
    else
    {
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'error' => true,
            'msg' => 'Invalid input'
        ]);
        return $response;
    }
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => true,
            'msg' => 'Invalid input'
        ]);
        return $response;
    }

    private function errorOccurredResponse($error_msg = "")
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Error Occurred';
        $response['body'] = json_encode([
            'error' => true,
            'msg' => 'Error Occurred ' . $error_msg
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }

    private function convertFileDuration($duration)
    {
        if ($duration-intval($duration > 0)){
            $roundedSeconds = intval($duration)+1;
        } else {
            $roundedSeconds = $duration;
        }
        return round($roundedSeconds/60,2);
    }
}
