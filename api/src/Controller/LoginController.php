<?php
namespace Src\Controller;

/*
 * base url /login
 * options:
 *      POST /login/reset -> reset password email to user
 * */

use Src\TableGateways\LoginGateway;
use Src\System\Throttler;
use Src\System\Mailer;

class LoginController {

    private $db;
    private $requestMethod;
    private $option;
    private $mailer;

    private $loginGateway;

    public function __construct($db, $requestMethod, $option)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->option = $option;
        $this->mailer = new Mailer($db);

        $this->loginGateway = new LoginGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {

            case 'GET':
            case 'POST':
                new Throttler("login", 10, \bandwidthThrottle\tokenBucket\Rate::MINUTE);


                if($this->option == "reset")
                {
                    $response = $this->sendResetPasswordEmail();
                }
                else if($this->option == null){
                    $response = $this->validateLogin();
                }
                else{
                    $response = $this->notFoundResponse();
                }

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

    // POST
    private function sendResetPasswordEmail()
    {
//        echo "hi";
        // fname=&lname=&email=hacker2894%40gmail.com&password=Iceman2801&countryID=209&stateID=70&city=
        if(!isset($_POST["email"]) ||empty($_POST["email"]) ) {
            return $this->unprocessableEntityResponse();
        }

        if($this->mailer->sendEmail(0, $_POST["email"]))
        {
            return generateApiHeaderResponse("Reset password email sent, please check your inbox", false);
        }else{
            return generateApiHeaderResponse("Failed to send reset password email", true);
        }

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