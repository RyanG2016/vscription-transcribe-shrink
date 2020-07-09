<?php
namespace Src\Controller;

use PHPMailer\PHPMailer\Exception;
use Src\TableGateways\AccountGateway;

class AccountController {

    private $db;
    private $requestMethod;
    private $accountId;

    private $accountGateway;

    public function __construct($db, $requestMethod, $accountId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->accountId = $accountId;

        $this->accountGateway = new AccountGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
//                if(isset($_GET["cancel"])){
//                    $response = $this->cancelUpload();
//                }else{
                    if ($this->accountId) {
                        $response = $this->getAccount($this->accountId);
                    } else {
                        $response = $this->getAllAccounts();
                    }
//                }
                break;
            case 'POST':
//                if(isset($_POST["cancel"])) {
//                    $response = $this->cancelUpload();
//                }else{
                    $response = $this->uploadAccountsFromRequest();
//                }
                break;
//            case 'PUT':
//                $response = $this->updateAccountFromRequest($this->accountId);
//                break;
//            case 'DELETE':
//                $response = $this->deleteAccount($this->accountId);
//                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllAccounts()
    {
        $result = $this->accountGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getAccount($id)
    {
        $result = $this->accountGateway->find($id);
        /*if (! $result) {
//            return $this->notFoundResponse();
        }*/
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createAccountFromRequest()
    {
        $input = (array) json_decode(account_get_contents('php://input'), TRUE);
        if (! $this->validateAccount($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->accountGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
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


    private function formatAccountResult($accountName, $status, $error){
        return array(
            "account_name" => $accountName,
            "status" => $status,
            "error" => $error
            );
    }

    private function updateAccountFromRequest($id)
    {
        $result = $this->accountGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(account_get_contents('php://input'), TRUE);
        if (! $this->validateAccount($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->accountGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteAccount($id)
    {
        $result = $this->accountGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->accountGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateAccount($input)
    {
        if (! isset($input['firstname'])) {
            return false;
        }
        if (! isset($input['lastname'])) {
            return false;
        }
        return true;
    }

    private function validateAndReturnDate($date)
    {
        // (accepted format: yyyy-mm-dd)
        $dateArr = explode("-",$date);
        if(sizeof($dateArr) == 3 && checkdate($dateArr[1], $dateArr[2], $dateArr[0])) {
            return $dateArr[0]."-".$dateArr[1]."-".$dateArr[2];
        }else{
            return false;
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
}
