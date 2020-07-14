<?php

namespace Src\Controller;

//use PHPMailer\PHPMailer\Exception;
use Src\TableGateways\UserGateway;

class UserController
{

    private $db;
    private $requestMethod;
    private $userId;

    private $userGateway;

    public function __construct($db, $requestMethod, $userId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->userId = $userId;

        $this->userGateway = new UserGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->userId) {
                    $response = $this->getUser($this->userId);
                } else {
                    $response = $this->getAllUsers();
                }
                break;
            case 'POST':

                if ($this->userId == "set-default") {
                    // set user default access
//                    echo "setting-default-access-for-current-logged-in-user";
                    $response = $this->updateUserDefaultAccess();
                } else {
                    $response = $this->createUserFromRequest();
                }
                break;
            case 'PUT':
                $response = $this->updateUserFromRequest($this->userId);
                break;
            case 'DELETE':
                $response = $this->deleteUser($this->userId);
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

    private function getAllUsers()
    {
        $result = $this->userGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createUserFromRequest()
    {
        return $this->userGateway->insertNewUser();
    }

    private function updateUserDefaultAccess()
    {
        return $this->userGateway->updateDefaultAccess();
    }

    private function updateUserFromRequest($accID)
    {
        return $this->userGateway->updateUser($accID);
    }

    private function getUser($id)
    {
        $result = $this->userGateway->find($id);
        /*if (! $result) {
//            return $this->notFoundResponse();
        }*/
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }


    /*private function formatUserResult($userName, $status, $error)
    {
        return array(
            "user_name" => $userName,
            "status" => $status,
            "error" => $error
        );
    }*/

    private function deleteUser($id)
    {
        if ($id == null) {
            return $this->notFoundResponse();
        }
        $result = $this->userGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->userGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'error' => false,
            'msg' => 'User Deleted.'
        ]);
        return $response;
    }

    /*private function validateUser($input)
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
    }*/
/*
    private function errorOccurredResponse($error_msg = "")
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Error Occurred';
        $response['body'] = json_encode([
            'error' => true,
            'msg' => 'Error Occurred ' . $error_msg
        ]);
        return $response;
    }*/

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = json_encode([
            'error' => true,
            'msg' => 'User Not Found'
        ]);
        return $response;
    }
}
