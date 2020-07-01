<?php
namespace Src\Controller;

use Src\TableGateways\LoginGateway;

class LoginController {

    private $db;
    private $requestMethod;
    private $loginId;

    private $loginGateway;

    public function __construct($db, $requestMethod)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;

        $this->loginGateway = new LoginGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                $response = $this->validateLogin($_GET);
                break;

            case 'POST':
                $response = $this->validateLogin($_POST);
                break;
            /*case 'PUT':
                $response = $this->updateLoginFromRequest($this->loginId);
                break;
            case 'DELETE':
                $response = $this->deleteLogin($this->loginId);
                break;*/
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllLogins()
    {
        $result = $this->loginGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getLogin($id)
    {
        $result = $this->loginGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function validateLogin($arr)
    {
        $email = null;
        $pass = null;
        if(sizeof($arr) > 0)
        {
            foreach ($arr as  $key=>$value){

                switch ($key)
                {
                    case "email":
                        $email = $value;
                        break;
                    case "pass":
                        $pass = $value;
                        break;
                }
            }
        }

        if ($email == null || $pass == null) {
            return $this->unprocessableEntityResponse();
        }

        // validate user email
        if(!$this->validateEmail($email))
        {
            return $this->unprocessableEntityResponse();
        }

        // logUserIn
        $result = $this->loginGateway->find($email, $pass);
        if($result["err"] == true){
            return $this -> AuthenticationFailed($result["msg"]);
        }
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = $result['msg'];
        return $response;
    }
    /*
    private function createLoginFromRequest()
    {
        $input = (array) json_decode(login_get_contents('php://input'), TRUE);
        if (! $this->validateLogin($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->loginGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }*/

    private function updateLoginFromRequest($id)
    {
        $result = $this->loginGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(login_get_contents('php://input'), TRUE);
        if (! $this->validateLogin($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->loginGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

/*    private function deleteLogin($id)
    {
        $result = $this->loginGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->loginGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }*/

    private function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function AuthenticationFailed($err)
    {
        $response['status_code_header'] = 'HTTP/1.1 401 Authentication Failed';
        $response['body'] = json_encode([
            'error' => $err
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