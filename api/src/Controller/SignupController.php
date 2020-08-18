<?php
namespace Src\Controller;

use Src\TableGateways\SignupGateway;
use Src\System\Throttler;
use Src\System\Mailer;

class SignupController {

    private $db;
    private $requestMethod;
    private $option;
    private $signupGateway;
    private $mailer;

    public function __construct($db, $requestMethod, $option)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->option = $option;
        $this->mailer = new Mailer($db);
        $this->signupGateway = new SignupGateway($db);
    }

    public function processRequest()
    {
        new Throttler("signup", 10, \bandwidthThrottle\tokenBucket\Rate::MINUTE);
        switch ($this->requestMethod) {

//            case 'GET':
                /*new Throttler("signup", 10, \bandwidthThrottle\tokenBucket\Rate::MINUTE);
                $response = $this->validateSignup();
                break;*/

            case 'POST':
                 new Throttler("sign_up", 6, \bandwidthThrottle\tokenBucket\Rate::MINUTE);
                if($this->option == "resend")
                {
                    $response = $this->resendVerifyEmail();
                }else {
                    $response = $this->validateAndSignUp();
                }
                break;

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
//            case 'GET':
                $response = $this->validateSignup();
                break;

            default:
                $response = $this->notFoundResponse();
                break;
        }

        return $response; // pass response to the requester
    }


    // POST
    private function resendVerifyEmail()
    {
//        echo "hi";
        // fname=&lname=&email=hacker2894%40gmail.com&password=Iceman2801&countryID=209&stateID=70&city=
        if(!isset($_POST["email"]) ||empty($_POST["email"]) ) {
            return $this->unprocessableEntityResponse();
        }

        if($this->mailer->sendEmail(5, $_POST["email"]))
        {
            return generateApiHeaderResponse("Verification Email sent, please check your inbox", false);
        }else{
            return generateApiHeaderResponse("Failed to resend verification email", true);
        }

    }


    private function validateAndSignUp()
    {
//        echo "hi";
        // fname=&lname=&email=hacker2894%40gmail.com&password=Iceman2801&countryID=209&stateID=70&city=
        if(
//            empty("city") optional
            !isset($_POST["email"]) ||
            !isset($_POST["password"]) ||
            !isset($_POST["fname"]) ||
            !isset($_POST["lname"]) ||
            !isset($_POST["countryID"]) ||

            ( isset($_POST["stateID"]) && !is_numeric($_POST["stateID"]) )
            ||
            empty($_POST["email"]) ||
            empty($_POST["password"]) ||
            empty($_POST["fname"]) ||
            empty($_POST["lname"]) ||
            empty($_POST["countryID"]) ||
//            empty($_POST["stateID"]) ||
            !is_numeric($_POST["countryID"])
        ){
            return $this->unprocessableEntityResponse();
        }

        // validate user email
        if(!$this->validateEmail($_POST["email"]))
        {
            return $this->unprocessableEntityResponse();
        }

        // check if user already exists
        if($this->signupGateway->userExist($_POST["email"]))
        {
            return generateApiHeaderResponse("You already have an account, login instead?", true,false,301);
        }

        if(!$this->validatePasswordRequirements($_POST["password"])){
            return generateApiHeaderResponse("Password doesn't meet requirements", true);
        }

        return $this->signupGateway->signUp();
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