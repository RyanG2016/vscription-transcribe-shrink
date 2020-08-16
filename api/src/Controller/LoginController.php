<?php
namespace Src\Controller;

use Src\TableGateways\LoginGateway;
use Src\System\Throttler;

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
            case 'POST':
                new Throttler("login", 10, \bandwidthThrottle\tokenBucket\Rate::MINUTE);
                $response = $this->validateLogin();
                break;

//                 new Throttler("sign_up", 5, \bandwidthThrottle\tokenBucket\Rate::MINUTE);
//                $response = $this->validateAndSignUp();
//                break;

            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
        exit();
    }

    public function processSilentRequest()
    {
        switch ($this->requestMethod) {
            case 'POST':
            case 'GET':
                $response = $this->validateLogin();
                break;

            default:
                $response = $this->notFoundResponse();
                break;
        }

        return $response; // pass response to the requester
    }

    private function validateLogin()
    {
        $email = $_SERVER["PHP_AUTH_USER"];
        $pass = $_SERVER["PHP_AUTH_PW"];

        if ($email == "" || $pass == "") {
            return $this->unprocessableEntityResponse();
        }

        // validate user email
        if(!$this->validateEmail($email))
        {
            return $this->unprocessableEntityResponse();
        }

        // logUserIn
        $result = $this->loginGateway->find($email, $pass);
        if($result["error"] == true){
            return $this -> AuthenticationFailed($result);
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'error' => false,
            'msg' => $result["msg"]
        ]);
        return $response;
    }

    /**
     * Validates password requirements for a given password - (min 8, uppercase, lowercase, special char.)
     * @param $password string password to be validated
     * @return bool (bool) password valid
     */
    private function validatePasswordRequirements($password)
    {
        $output_array = null;
        $ptn = "/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};\':\"\\\\|,. <>\/?]).{8,}/";
        preg_match($ptn, $password, $output_array);

        return $output_array != false;
    }

    private function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => true,
            "msg" => 'Invalid input (E)'
        ]);
        return $response;
    }

    private function AuthenticationFailed($body)
    {
        $response['status_code_header'] = 'HTTP/1.1 401 Authentication Failed';
        $response['body'] = json_encode($body);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}