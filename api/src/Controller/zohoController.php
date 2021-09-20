<?php

namespace Src\Controller;

use PHPMailer\PHPMailer\Exception;
use Src\TableGateways\zohoGateway;

class zohoController
{

    private $db;
    private $requestMethod;
    private $uri;

    private $zohosGateway;

    public function __construct($db, $requestMethod, $uri)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->uri = $uri;

        $this->zohosGateway = new zohoGateway($db);
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
//            case 'POST':
//                    $response = $this->uploadzohosFromRequest();
//                break;
//            case 'PUT':
//                $response = $this->updatezohosFromRequest($this->zohosId);
//                break;
//            case 'DELETE':
//                $response = $this->deletezohos($this->zohosId);
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
        $result = $this->zohosGateway->findAllInvoices();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getInvoice($invoice_number)
    {
        $result = $this->zohosGateway->findZohoInvoice($invoice_number);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getUser($zoho_id)
    {
        $result = $this->zohosGateway->findZohoUser($zoho_id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getAllUsers()
    {
        $result = $this->zohosGateway->findAllUsers();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getzohos($id)
    {
        $result = $this->zohosGateway->find($id);
        /*if (! $result) {
//            return $this->notFoundResponse();
        }*/
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createzohosFromRequest()
    {
        $input = (array)json_decode(zohos_get_contents('php://input'), TRUE);
        if (!$this->validatezohos($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->zohosGateway->insert($input);
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


    private function formatzohosResult($zohosName, $status, $error)
    {
        return array(
            "zohos_name" => $zohosName,
            "status" => $status,
            "error" => $error
        );
    }

    private function updatezohosFromRequest($id)
    {
        $result = $this->zohosGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $input = (array)json_decode(zohos_get_contents('php://input'), TRUE);
        if (!$this->validatezohos($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->zohosGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deletezohos($id)
    {
        $result = $this->zohosGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->zohosGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validatezohos($input)
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
