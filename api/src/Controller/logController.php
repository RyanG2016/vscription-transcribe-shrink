<?php
namespace Src\Controller;

use Src\TableGateways\logGateway;

class logController {

    private $db;
    private $requestMethod;
    private $logPage;

    private $logsGateway;

    public function __construct($db, $requestMethod, $logPage)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->logPage = $logPage;

        $this->logsGateway = new logGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':

                // get all logs
                if($_SESSION['role'] == 1)
                {
                    if (isset($_GET["acc_id"]) && is_numeric($_GET["acc_id"])) {
                        $response = $this->getAccLogs($this->logPage, $_GET["acc_id"]);
                    }else if (isset($_GET["acc_id"]) && !is_numeric($_GET["acc_id"])) {
                        $response = $this->notFoundResponse();
                    }else{
                        $response = $this->getAllLogs($this->logPage);
                    }
                }
                // get client admin logs
                else if($_SESSION['role'] == 2){
                    $response = $this->getClientLogs($this->logPage);
                }
                else{
                    $response = $this->notFoundResponse();
                }
                /*if ($this->logsId) {
                    $response = $this->getlog($this->logsId);
                } else {
                    if(isset($_GET["box_model"]))
                    {
                        $response = $this->getAllLogs(true);
                    }
                    else{
                        $response = $this->getAllLogs();
                    }
                }*/

                break;
//            case 'POST':
//                    $response = $this->uploadlogsFromRequest();
//                break;
//            case 'PUT':
//                $response = $this->updatelogsFromRequest($this->logsId);
//                break;
//            case 'DELETE':
//                $response = $this->deletelogs($this->logsId);
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

    private function getAllLogs($page)
    {
        $result = $this->logsGateway->findAll($page);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getAccLogs($page, $accID)
    {
        $result = $this->logsGateway->findAllForAcc($page, $accID);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }


    private function getClientLogs($page)
    {
        $result = $this->logsGateway->findClientAll($page);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

//    private function getlog()
//    {
//        $result = $this->logsGateway->find($id);
//        /*if (! $result) {
////            return $this->notFoundResponse();
//        }*/
//        $response['status_code_header'] = 'HTTP/1.1 200 OK';
//        $response['body'] = json_encode($result);
//        return $response;
//    }

/*    private function deletelog($id)
    {
        $result = $this->logsGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->logsGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }*/
/*

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => true,
            'msg' => 'Invalid input'
        ]);
        return $response;
    }*/


    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
