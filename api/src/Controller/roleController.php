<?php

namespace Src\Controller;

use PHPMailer\PHPMailer\Exception;
use Src\TableGateways\roleGateway;

class roleController
{

    private $db;
    private $requestMethod;
    private $rolesId;

    private $rolesGateway;

    public function __construct($db, $requestMethod, $rolesId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->rolesId = $rolesId;

        $this->rolesGateway = new roleGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':

                if ($this->rolesId) {
                    $response = $this->getroles($this->rolesId);
                } else {
                    $response = $this->getAllroles();
                }

                break;
//            case 'POST':
//                    $response = $this->uploadrolesFromRequest();
//                break;
//            case 'PUT':
//                $response = $this->updaterolesFromRequest($this->rolesId);
//                break;
//            case 'DELETE':
//                $response = $this->deleteroles($this->rolesId);
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

    private function getAllroles()
    {
        $result = $this->rolesGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getroles($id)
    {
        $result = $this->rolesGateway->find($id);
        /*if (! $result) {
//            return $this->notFoundResponse();
        }*/
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createrolesFromRequest()
    {
        $input = (array)json_decode(roles_get_contents('php://input'), TRUE);
        if (!$this->validateroles($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->rolesGateway->insert($input);
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


    private function formatrolesResult($rolesName, $status, $error)
    {
        return array(
            "roles_name" => $rolesName,
            "status" => $status,
            "error" => $error
        );
    }

    private function updaterolesFromRequest($id)
    {
        $result = $this->rolesGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $input = (array)json_decode(roles_get_contents('php://input'), TRUE);
        if (!$this->validateroles($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->rolesGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteroles($id)
    {
        $result = $this->rolesGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->rolesGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateroles($input)
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
