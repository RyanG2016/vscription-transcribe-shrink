<?php
namespace Src\Controller;

use Src\TableGateways\LogoutGateway;

class LogoutController {

    private $db;
    private $requestMethod;

    private $logoutGateway;

    public function __construct($db, $requestMethod)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;

        $this->logoutGateway = new LogoutGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'POST':
            case 'GET':
                $response = $this->logout();
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

    private function logout()
    {
        // Log User Out.
        $result = $this->logoutGateway->sessionLogout();

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'error' => false,
            'msg' => $result["msg"]
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