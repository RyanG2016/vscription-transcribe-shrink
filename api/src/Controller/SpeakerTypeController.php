<?php
namespace Src\Controller;

use PHPMailer\PHPMailer\Exception;
use Src\TableGateways\SpeakerTypeGateway;

class SpeakerTypeController {

    private $db;
    private $requestMethod;
    private $speakerTypesId;

    private $speakerTypesGateway;

    public function __construct($db, $requestMethod, $speakerTypesId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->speakerTypesId = $speakerTypesId;

        $this->speakerTypesGateway = new SpeakerTypeGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':

                if ($this->speakerTypesId) {
                    $response = $this->getSpeakerTypes($this->speakerTypesId);
                } else {
                    if(isset($_GET["box_model"]))
                    {
                        $response = $this->getAllSpeakerTypes(true);
                    }
                    else{
                        $response = $this->getAllSpeakerTypes();
                    }
                }

                break;
//            case 'POST':
//                    $response = $this->uploadSpeakerTypesFromRequest();
//                break;
//            case 'PUT':
//                $response = $this->updateSpeakerTypesFromRequest($this->speakerTypesId);
//                break;
//            case 'DELETE':
//                $response = $this->deleteSpeakerTypes($this->speakerTypesId);
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

    private function getAllSpeakerTypes($forComboBox = false)
    {
        $result = $this->speakerTypesGateway->findAll($forComboBox);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getSpeakerTypes($id)
    {
        $result = $this->speakerTypesGateway->find($id);
        /*if (! $result) {
//            return $this->notFoundResponse();
        }*/
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createSpeakerTypesFromRequest()
    {
        $input = (array) json_decode(speakerTypes_get_contents('php://input'), TRUE);
        if (! $this->validateSpeakerTypes($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->speakerTypesGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
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


    private function formatSpeakerTypesResult($speakerTypesName, $status, $error){
        return array(
            "speakerTypes_name" => $speakerTypesName,
            "status" => $status,
            "error" => $error
            );
    }

    private function updateSpeakerTypesFromRequest($id)
    {
        $result = $this->speakerTypesGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(speakerTypes_get_contents('php://input'), TRUE);
        if (! $this->validateSpeakerTypes($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->speakerTypesGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteSpeakerTypes($id)
    {
        $result = $this->speakerTypesGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->speakerTypesGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateSpeakerTypes($input)
    {
        if (! isset($input['firstname'])) {
            return false;
        }
        if (! isset($input['lastname'])) {
            return false;
        }
        return true;
    }

    private function validateAndReturnDate($date)
    {
        // (accepted format: yyyy-mm-dd)
        $dateArr = explode("-",$date);
        if(sizeof($dateArr) == 3 && checkdate($dateArr[1], $dateArr[2], $dateArr[0])) {
            return $dateArr[0]."-".$dateArr[1]."-".$dateArr[2];
        }else{
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
