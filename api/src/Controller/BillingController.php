<?php
namespace Src\Controller;

use Src\Enums\ROLES;
use Src\TableGateways\BillingGateway;
use Src\Helpers\common;

class BillingController {

//    private $db;
//    private $requestMethod;
//    private $billingId;

    private BillingGateway $billingGateway;
    private $common;

    public function __construct(
        $db, private $requestMethod, private $options
    )
    {
//        $this->db = $db;
//        $this->requestMethod = $requestMethod;
//        $this->billingId = $billingId;

        $this->billingGateway = new BillingGateway($db);
        $this->common = new common();
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->options[0]) {
                   $response = $this->getBilling($this->options[0]);
                } else {
//                    $response = $this->getAllBillings();
                    $response = $this->notFoundResponse();
                }
                break;
            case 'POST':
                if ($this->options[0] && $_SESSION["role"] == ROLES::SYSTEM_ADMINISTRATOR) {
                    $response = $this->billProcess($this->options[0]);
                }else{
                    $response = $this->notFoundResponse();
                }
                break;
//            case 'PUT':
//                $response = $this->updateBillingFromRequest($this->options);
//                break;
//            case 'DELETE':
//                $response = $this->deleteBilling($this->options);
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

    private function getAllBillings()
    {
        return $this->notFoundResponse();

        $result = $this->billingGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getBilling($orgID)
    {
        if(
            !isset($_GET['start_date'])
            ||
            !isset($_GET['end_date'])
        ){
            return $this->common->missingRequiredParametersResponse();
        }

        $result = $this->billingGateway->find($orgID);


        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function billProcess($orgID)
    {
        if(
            !isset($_GET['start_date'])
            ||
            !isset($_GET['end_date'])
            ||
            !isset($_GET['data'])
            ||
            !isset($_GET['invoice_bill'])
        ){
            return $this->common->missingRequiredParametersResponse();
        }

        $result = $this->billingGateway->billProcessing($orgID);


        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function updateBillingFromRequest($id)
    {
        $result = $this->billingGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(billing_get_contents('php://input'), TRUE);
        if (! $this->validateBilling($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->billingGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }
/*
    private function deleteBilling($id)
    {
        $result = $this->billingGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->billingGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateBilling($input)
    {
        if (! isset($input['firstname'])) {
            return false;
        }
        if (! isset($input['lastname'])) {
            return false;
        }
        return true;
    }*/

/*
    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }*/


    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}