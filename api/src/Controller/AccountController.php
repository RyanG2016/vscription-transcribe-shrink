<?php

namespace Src\Controller;

use PHPMailer\PHPMailer\Exception;
use Src\TableGateways\AccountGateway;

class AccountController
{

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
                if ($this->accountId) {
                    $response = $this->getAccount($this->accountId);
                } else {
                    $response = $this->getAllAccounts();
                }
//                }
                break;
            case 'POST':
                $response = $this->createAccountFromRequest();
//                }
                break;
            case 'PUT':
                $response = $this->updateAccountFromRequest($this->accountId);
                break;
            case 'DELETE':
                $response = $this->deleteAccount($this->accountId);
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

    private function getAllAccounts()
    {
        $result = $this->accountGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createAccountFromRequest()
    {
        return $this->accountGateway->insertNewAccount();
    }

    private function updateAccountFromRequest($accID)
    {
        return $this->accountGateway->updateAccount($accID);
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


    private function formatAccountResult($accountName, $status, $error)
    {
        return array(
            "account_name" => $accountName,
            "status" => $status,
            "error" => $error
        );
    }

    private function deleteAccount($id)
    {
        if ($id == null) {
            return $this->notFoundResponse();
        }
        $result = $this->accountGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->accountGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'error' => false,
            'msg' => 'Account Deleted.'
        ]);
        return $response;
    }

    private function validateAccount($input)
    {
        if (!isset($input['firstname'])) {
            return false;
        }
        if (!isset($input['lastname'])) {
            return false;
        }
        return true;
    }

    private function validateAndReturnDate($date)
    {
        // (accepted format: yyyy-mm-dd)
        $dateArr = explode("-", $date);
        if (sizeof($dateArr) == 3 && checkdate($dateArr[1], $dateArr[2], $dateArr[0])) {
            return $dateArr[0] . "-" . $dateArr[1] . "-" . $dateArr[2];
        } else {
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
        $response['body'] = json_encode([
            'error' => true,
            'msg' => 'Account Not Found'
        ]);
        return $response;
    }
}
