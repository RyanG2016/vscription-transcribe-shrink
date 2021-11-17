<?php

namespace Src\Controller;

use PHPMailer\PHPMailer\Exception;
use Src\Helpers\common;
use Src\Helpers\zohoHelper;
use Src\TableGateways\zohoGateway;

class zohoController
{

    private $db;
    private $requestMethod;
    private $uri;

    private $zohoGateway;
    private $zohoHelper;
    private $common;

    public function __construct($db, $requestMethod, $uri)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->uri = $uri;

//        $this->zohoGateway = new zohoGateway($db);
        $this->zohoHelper = new zohoHelper($db);
        $this->zohoGateway = $this->zohoHelper->zohoGateway;
        $this->common = new common();
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':

                if ($this->uri) {
                    switch ($this->uri[0])
                    {
                        case 'invoices':
                            if(isset($this->uri[1]))
                            {
                                $response = $this->getInvoice($this->uri[1]);
                            }else{
                                $response = $this->getAllInvoices();
                            }
                            break;

                        case 'users':
                            if(isset($this->uri[1]))
                            {
                                $response = $this->getUser($this->uri[1]);
                            }else{
                                $response = $this->getAllUsers();
                            }
                            break;

                        default:
                            $response = $this->notFoundResponse();
                            break;
                    }
                } else {
                    $response = $this->notFoundResponse();
                }

                break;
            case 'POST':
                if ($this->uri) {
                    switch ($this->uri[0])
                    {
                        case 'invoice':
                            // create new invoice
                            $response = $this->generateInvoice();
//                            $response = $this->saveInvoicePDF();
                            break;

                            case 'bill':
                            // create new invoice
                            $response = $this->generateBill();
//                            $response = $this->saveInvoicePDF();
                            break;
/*
                            case 'attach':
                            $response = $this->saveInvoicePDF();
                            break;*/

                        default:
                            $response = $this->notFoundResponse();
                            break;
                    }
                } else {
                    $response = $this->notFoundResponse();
                }
                break;
//            case 'PUT':
//                $response = $this->saveInvoicePDF();
//                break;
//            case 'DELETE':
//                $response = $this->deletezoho($this->zohoId);
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

    private function getAllInvoices()
    {
        $result = $this->zohoGateway->findAllInvoices();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getInvoice($invoice_number)
    {
        $result = $this->zohoGateway->findZohoInvoice($invoice_number);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getUser($zoho_id)
    {
        $result = $this->zohoGateway->findZohoUser($zoho_id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getAllUsers()
    {
        $result = $this->zohoGateway->findAllUsers();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getzoho($id)
    {
        $result = $this->zohoGateway->find($id);
        /*if (! $result) {
//            return $this->notFoundResponse();
        }*/
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function generateInvoice()
    {

        if(
            !isset($_POST['invoiceData']) ||
            !isset($_POST['pdfName']) ||
            !isset($_FILES['pdf'])
        ){
            return $this->common->missingRequiredParametersResponse();
        }
//        return $this->common->missingRequiredParametersResponse();

        $result = $this->zohoHelper->generateInvoiceBill(json_decode($_POST['invoiceData'],1));


        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function generateBill()
    {

        if(
            !isset($_POST['billData']) ||
            !isset($_POST['pdfName']) ||
            !isset($_FILES['pdf'])
        ){
            return $this->common->missingRequiredParametersResponse();
        }
//        return $this->common->missingRequiredParametersResponse();

        $result = $this->zohoHelper->generateBill(json_decode($_POST['billData'],1));


        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }
/*
    private function saveInvoicePDF()
    {

        $testo = 'pause';
//        $_FILES['pdf'];
//        if(
//            !isset($_POST['invoiceData'])
//        ){
//            return $this->common->missingRequiredParametersResponse();
//        }
        return $this->common->missingRequiredParametersResponse();

//        $result = $this->zohoHelper->generateInvoiceBill(json_decode($_POST['invoiceData'],1));


        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }*/


    private function formatzohoResult($zohoName, $status, $error)
    {
        return array(
            "zoho_name" => $zohoName,
            "status" => $status,
            "error" => $error
        );
    }

    private function updatezohoFromRequest($id)
    {
        $result = $this->zohoGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $input = (array)json_decode(zoho_get_contents('php://input'), TRUE);
        if (!$this->validatezoho($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->zohoGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deletezoho($id)
    {
        $result = $this->zohoGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->zohoGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validatezoho($input)
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
        $response['body'] = null;
        return $response;
    }
}
