<?php

namespace Src\Controller;

use Src\Enums\PAYMENT_STATUS;
use Src\Helpers\common;
use Src\Models\Account;
use Src\Models\User;
use Src\Models\Payment;
use Src\TableGateways\paymentGateway;
use Src\System\Mailer;
use Src\TableGateways\accessGateway;

class PaymentController
{

    private $db;
    private $requestMethod;
    private $rawURI;
    private $mailer;
    private $common;
    private User $userModel;

    private $paymentGateway;
    private $accessGateway;

    public function __construct($db, $requestMethod, $rawURI)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->rawURI = $rawURI;
        $this->mailer = new Mailer($db);
        $this->common = new common();

        $this->paymentGateway = new paymentGateway($db);
        $this->accessGateway = new accessGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if (isset($_GET["cancel"])) {
                    $response = $this->cancelUpload();
                } else {
                    error_log("We don't have a method setup to get payment info yet",0);
                }
                break;
            case 'POST':
                if (isset($_POST["cancel"])) {
                    $response = $this->cancelUpload();
                } else {
                    if(isset($this->rawURI[0]) && $this->rawURI[0] == "create")
                    {
                        error_log("We should next run the insertPurchase method",0);
                        $response = $this->insertPurchase();
                    }
                }
                break;

            default:
                $response = $this->notFoundResponse();
                break;
        }
        // I'm not sure what this is supposed to do is we don't have the $response var. It just generates PHP Error log entries
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

        if (isset($_POST["responsejson"])
        && isset($_POST["requestjson"])
        ) {
        $cleanJSON = $this->parseAnetResponse($_POST["responsejson"]);
        // $responseJSON = json_decode($cleanJSON, true);
        $requestJSON = json_decode($_POST["requestjson"], true);
        $responseKeys = array("accountNumber","transId","authCode");
        $responseValues = $this->getResponseValues($_POST["responsejson"], $responseKeys);
        // error_log("Decoded request JSON is: " . $requestJSON);
        // error_log("Decoded response JSON is: " . $responseJSON);
        // error_log("Amount: " . $requestJSON['createTransactionRequest']['transactionRequest']['amount'],0);
        // error_log("refID: " . $responseJSON['transactionResponse']['networkTransId'],0);
        // error_log("TransID: " . $responseJSON['transactionResponse']['transId'],0);
        // error_log("This is what we should be writing to the Payment object: " . $_POST["requestjson"] . "|&sep|" . $_POST["responsejson"],0);

        $payment = new Payment(
            0,
            $_SESSION["uid"],
            $requestJSON['createTransactionRequest']['transactionRequest']['amount'],
            $responseValues[2], //We should put the job number in here
            $responseValues[1],
            $_POST["requestjson"] . "|&sep|" . $_POST["responsejson"],
            1,
            1, //Paid response
            $this->db
        );
        $pid = $payment->save();
        // We're only calling this method on a successful transaction so no need to confirm
        $this->mailer->sendEmail(20, $_SESSION["uEmail"], "", $pid);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'error' => false,
            'msg' => 'Purchase Record Inserted'
        ]);
        return $response;
        // return $error;
    }
    else
    {
        // error_log("We should be returning the Invalid Input response here",0);
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
        // error_log("Are we even getting here",0);
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }

    // This function is used to extract the response data we need from the 
    // invalid JSON format provided from the mobile app form Authorize.net
    // response
    private function getResponseValues($inString, $keys) {
        $returnArray = [];
        for ($i = 0; $i < count($keys); $i++) {
            $keyStart = 0;
            $valueStart = 0;
            $valueLength = 0;
            $keyStart = strpos($inString, $keys[$i]);
            if ($keyStart > 0) {
                $valueStart = $keyStart + (strlen($keys[$i]) + 3);
                $valueLength = strpos(substr($inString,$valueStart,15), ';');
                array_push($returnArray, substr($inString,$valueStart,$valueLength));
            } else {
                array_push($returnArray,"Key value not found");
            }
        }
        return $returnArray;
    }

    private function parseAnetResponse($inString) 
    {
        // As a quick workaround, instead of decoding and reassembling the message
        // in Swift, we're jsut going to take the Optional response and manually parse the string
        // into a proper JSON here. $response is what we receive from Swift 
        $noSlashes = stripslashes($inString);
        $noOptionalOpenTag = str_ireplace("Optional(\"", "",$noSlashes,);
        $cleanedJSON = str_replace("\")","",$noOptionalOpenTag,);
        return $cleanedJSON;
    }
}
