<?php
namespace Src\Controller;

use Src\Enums\HTTP_CONTENT_TYPE;
use Src\Enums\HTTP_RESPONSE;
use Src\TableGateways\SRQueueGateway;
use Src\Traits\httpResponse;

class SRQueueController {

    private $SRQueueGateway;
    use httpResponse;

    public function __construct(private $db, private $requestMethod, private $page = null)
    {
        $this->SRQueueGateway = new SRQueueGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
//            case 'GET':

//                break;
            case 'POST':
                if ($this->page == "incoming") {
                    $response = $this->processRevaiNotification();
                } else {
                    $response = $this->$this->respond(HTTP_RESPONSE::HTTP_NOT_FOUND);
                }
                break;
//            case 'PUT':
//                $response = $this->updateCitiesFromRequest($this->citiesId);
//                break;
//            case 'DELETE':
//                $response = $this->deleteCities($this->citiesId);
//                break;
            default:
                $response = $this->$this->respond(HTTP_RESPONSE::HTTP_NOT_FOUND);
                break;
        }
        header($response['status_code_header']);
        header("Content-type:". $response['content_type']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function processRevaiNotification()
    {
        $revai = $this->readPost()["job"];
        $test = 2;
        return $this->respond(HTTP_RESPONSE::HTTP_OK);
    }

    private function readPost()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    private function getCity($id, $combobox)
    {
        $result = $this->SRQueueGateway->find($id, $combobox);
        /*if (! $result) {
//            return $this->notFoundResponse();
        }*/
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createCityFromRequest()
    {
        $input = (array) json_decode(city_get_contents('php://input'), TRUE);
        if (! $this->validateCities($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->SRQueueGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateCityFromRequest($id)
    {
        $result = $this->SRQueueGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(city_get_contents('php://input'), TRUE);
        if (! $this->validateCities($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->SRQueueGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteCity($id)
    {
        $result = $this->SRQueueGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->SRQueueGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateCities($input)
    {
        if (! isset($input['firstname'])) {
            return false;
        }
        if (! isset($input['lastname'])) {
            return false;
        }
        return true;
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


    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
