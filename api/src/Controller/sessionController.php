<?php

namespace Src\Controller;

use PHPMailer\PHPMailer\Exception;
use Src\Helpers\common;
use Src\Helpers\sessionHelper;
use Src\TableGateways\sessionGateway;

class sessionController
{

    private $db;
    private $requestMethod;
    private $uri;

    private $sessionGateway;
    private $sessionHelper;
    private $common;

    public function __construct($db, $requestMethod, $uri)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->uri = $uri;

        $this->sessionHelper = new sessionHelper($db);
        $this->sessionGateway = $this->sessionHelper->sessionGateway;
        $this->common = $this->sessionHelper->common;
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                switch ($this->uri[0])
                {
                    case 'expiry':

                        $response = $this->getExpiry();
                        break;
                   case 'extend':

                        $response = $this->extendSession();
                        break;

                    case 'revoke':

                        $response = $this->revokeSessionAccess($this->uri[1]);
                        break;

                    default:

                        $response = $this->getAllSessions();
                        break;
                }

                break;
//            case 'POST':
//                if ($this->uri) {
//                    switch ($this->uri[0])
//                    {
////                        case 'invoice':
////                            // create new invoice
////                            $response = $this->generateInvoice();
//////                            $response = $this->saveInvoicePDF();
////                            break;
////
////                            case 'bill':
////                            // create new invoice
////                            $response = $this->generateBill();
//////                            $response = $this->saveInvoicePDF();
////                            break;
///*
//                            case 'attach':
//                            $response = $this->saveInvoicePDF();
//                            break;*/
//
//                        default:
//                            $response = $this->notFoundResponse();
//                            break;
//                    }
//                } else {
//                    $response = $this->notFoundResponse();
//                }
//                break;
//            case 'PUT':
//                $response = $this->saveInvoicePDF();
//                break;
//            case 'DELETE':
//                $response = $this->deletesession($this->sessionId);
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

    private function getAllSessions()
    {
        $result = $this->sessionGateway->findAllSessions();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }


    private function getExpiry()
    {
        $result = $this->sessionHelper->findSessExpiry();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }


    private function extendSession()
    {
        $result = $this->sessionHelper->extendSession();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }


    private function revokeSessionAccess($sessID)
    {
        $result = $this->sessionHelper->revokeAccess($sessID);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
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
