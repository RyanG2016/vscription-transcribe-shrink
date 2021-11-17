<?php
use Src\Controller\LoginController;
if(!isset($_SESSION['loggedIn']))
{
    // check for basic auth
    if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER["PHP_AUTH_PW"]))
    {
        // Auth Headers Available -> try to login
        $loginController = new LoginController($dbConnection, $_SERVER["REQUEST_METHOD"], null);
        $result = $loginController->processSilentRequest();

        // if login OK the script will resume..
        $jsonResponse = json_decode($result["body"], true);
        if ($jsonResponse["error"] != false) { // login failed
            header($result['status_code_header']);
            echo $result['body'];
            exit();
        }
    }
    else{ // no basic auth sent
        header("HTTP/1.1 401 ACCESS DENIED");
        $body = json_encode([
            'error' => true,
            'msg' => "You don't have permission to access this web page, make sure you are logged in."
        ]);
        echo $body;
        exit();
    }
}