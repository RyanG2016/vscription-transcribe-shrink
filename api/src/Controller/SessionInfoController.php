<?php
namespace Src\Controller;

// no Gateway needed as no DB queries needed.

class SessionInfoController {

    private $db;
    private $requestMethod;
    public function __construct($db, $requestMethod)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
            case 'POST':
                $response = $this->getSessionInfo();
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

    private function getSessionInfo()
    {
        $response['status_code_header'] = "HTTP/1.1 200 OK";
        $response['body'] =
        json_encode(
            array(
                "first_name" => $_SESSION["fname"],
                "last_name" => $_SESSION["lname"],
                "email" => $_SESSION["uEmail"],
//                "role" => $_SESSION["role"],
                "account_id" => isset($_SESSION["accID"])?$_SESSION["accID"]:0
            )
        );

//            print_r($_SESSION);
        return $response;
    }


    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}