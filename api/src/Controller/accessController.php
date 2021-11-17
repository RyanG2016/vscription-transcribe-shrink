<?php

namespace Src\Controller;

use Src\TableGateways\accessGateway;
use Src\TableGateways\UserGateway;

class accessController
{

    private $db;
    private $requestMethod;
    private $accessId;

    private $accessGateway;
    private $userGateway;

    public function __construct($db, $requestMethod, $accessId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->accessId = $accessId;
        $this->userGateway = new UserGateway($db);

        $this->accessGateway = new accessGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':

                if ($this->accessId) {
                    $response = $this->getaccess($this->accessId);
                } else {
                    $response = $this->getAllaccess();
                }

                break;
            case 'POST':
                    $response = $this->createAccessFromRequest();
                break;
            case 'PUT':
                $response = $this->updateaccessFromRequest($this->accessId);
                break;
            case 'DELETE':
                $response = $this->deleteaccess($this->accessId);
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

    public function getPublicAccessForCurrentLoggedUser()
    {
        switch ($this->requestMethod) {
            case 'GET':

//                if ($this->accessId) {
//                    $response = $this->getaccess($this->accessId);
//                } else {
                if(isset($_REQUEST['account_id']))
                {
                    $response = $this->checkAccountAccessPermission($_REQUEST['account_id']);
                }else if(isset($_REQUEST['count']))
                {
                    $response = $this->getAccessCountCurUser();
                }else if(isset($_REQUEST['jl']))
                {
                    $response = $this->getAllowedTypists();
                }
                else{
                    $response = $this->getOutAccess();
                }
//                }

                break;
            case 'POST':
                    $response = $this->setOutAccess();
                break;
            /*case 'PUT':
                $response = $this->updateaccessFromRequest($this->accessId);
                break;
            */
            case 'DELETE':
                $response = $this->revokeAccess($this->accessId);
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

    private function getAllaccess()
    {
        $result = $this->accessGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getOutAccess()
    {
        $result = $this->accessGateway->findAllOut();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    /**
     * Retrieves current typists working for the current logged in account
     * called when jl param is added to the request
     * @return mixed
     */
    private function getAllowedTypists()
    {
        $result = $this->accessGateway->findTypistsForCurLoggedInAccount();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getAccessCountCurUser()
    {
        $result = $this->accessGateway->getPermCountForCurUser();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function checkAccountAccessPermission($acc_id)
    {
        $result = $this->accessGateway->checkAccountAccessPermission($acc_id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(array(
            "msg" => $result?"Access Granted":"Access Denied",
            "error" => !$result
        ));
        return $response;
    }

    private function setOutAccess()
    {
        $result = $this->accessGateway->findAndSetOutAccess();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
//        header($result['status_code_header']);
//        if ($result['body']) {
//            echo $result['body'];
//        }
//        exit();
    }

    private function getaccess($id)
    {
        $result = $this->accessGateway->find($id);
        /*if (! $result) {
//            return $this->notFoundResponse();
        }*/
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createAccessFromRequest()
    {
        return $this->accessGateway->insertAccessRecord();
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


    private function formataccessResult($accessName, $status, $error)
    {
        return array(
            "access_name" => $accessName,
            "status" => $status,
            "error" => $error
        );
    }

    private function updateaccessFromRequest($id)
    {
        return $this->accessGateway->updateAccess($id);
    }

    private function deleteaccess($id)
    {
        $result = $this->accessGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->accessGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'error' => false,
            'msg' => 'Access Revoked.'
        ]);
        return $response;
    }

    /**
     * Public API access - should be client admin and access should be for the same logged in Client Admin ID to be allowed
     * @param $id int id of access to revoke
     * @return mixed
     */
    private function revokeAccess($id)
    {
        if(!isset($_SESSION["role"]) || ($_SESSION["role"] != 2 && $_SESSION["role"] != 1))
        {
            return $this->accessDeniedResponse();
        }

        $result = $this->accessGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }else{
            $row = $result[0];
            if($row["acc_id"] != $_SESSION["accID"]) {
                return $this->accessDeniedResponse();
            }else{
                $user = $this->userGateway->find($row["uid"]);
                if(!$user){
                    return $this->notFoundResponse();
                }else{
                    if($user[0]["def_access_id"] == $id)
                    {
                        $this->userGateway->internalUpdateUserClearDefaultAccess($row["uid"]);
                    }
                }
            }
        }

        $pass = $this->accessGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'error' => !$pass,
            'msg' => $pass?'Access Revoked.':"Failed to revoke access."
        ]);
        return $response;
    }

    private function validateaccess($input)
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

    private function accessDeniedResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 401 ACCESS DENIED';
        $response['body'] = json_encode([
            'error' => true,
            'msg' => "You don't have permission for this action"
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
