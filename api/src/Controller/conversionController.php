<?php

namespace Src\Controller;

//use PHPMailer\PHPMailer\Exception;
use Src\TableGateways\conversionGateway;

class conversionController
{

    private $db;
    private $requestMethod;
    private $conversionsId;

    private $conversionsGateway;

    public function __construct($db, $requestMethod, $conversionsId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->conversionsId = $conversionsId;

        $this->conversionsGateway = new conversionGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':

                if ($this->conversionsId) {
                    $response = $this->getconversions($this->conversionsId);
                } else {
                    $response = $this->getAllconversions();
                }

                break;
            /*case 'POST':

                if ($this->conversionsId) {
                    if(sizeof($_POST) > 0)
                    {
                        // update record
                        $response = $this->updateConversionFromRequest($this->conversionsId);

                    }else{
                        // create new record
                        $response = $this->createConversionFromRequest($this->conversionsId);
                    }

                } else {
                    $response = $this->notFoundResponse();
                }
                break;*/
//            case 'PUT':
//                $response = $this->updateconversionsFromRequest($this->conversionsId);
//                break;
//            case 'DELETE':
//                $response = $this->deleteconversions($this->conversionsId);
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

    private function getAllconversions()
    {
        $result = $this->conversionsGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getconversions($id)
    {
        $result = $this->conversionsGateway->find($id);
        /*if (! $result) {
//            return $this->notFoundResponse();
        }*/
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createConversionFromRequest($fileID)
    {
//        $input = (array)json_decode(conversions_get_contents('php://input'), TRUE);
//        if (!$this->validateconversions($input)) {
//            return $this->unprocessableEntityResponse();
//        }
        $result = $this->conversionsGateway->insertNewConversion($fileID);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = $result['body'];
        return $response;
    }


    private function updateConversionFromRequest($fileID)
    {
//        $input = (array)json_decode(conversions_get_contents('php://input'), TRUE);
//        if (!$this->validateconversions($input)) {
//            return $this->unprocessableEntityResponse();
//        }
        $result = $this->conversionsGateway->updateConversionStatus($fileID);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = $result['body'];
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
