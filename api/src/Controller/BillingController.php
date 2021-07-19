<?php
namespace Src\Controller;

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
                    $response = $this->getAllBillings();
                }
                break;
            case 'POST':
                $response = $this->createBillingFromRequest();
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
        $result = $this->billingGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getBilling($orgID)
    {
        if(
            !isset($_GET['startDate'])
            ||
            !isset($_GET['endDate'])
        ){
            return $this->common->missingRequiredParametersResponse();
        }

        $result = $this->billingGateway->find($orgID);


        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createBillingFromRequest()
    {
        $input = (array) json_decode(billing_get_contents('php://input'), TRUE);
        if (! $this->validateBilling($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->billingGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
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