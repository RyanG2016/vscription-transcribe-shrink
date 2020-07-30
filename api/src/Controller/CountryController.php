<?php
namespace Src\Controller;

use Src\TableGateways\CountryGateway;

class CountryController {

    private $db;
    private $requestMethod;
    private $countrysId;

    private $countrysGateway;

    public function __construct($db, $requestMethod, $countrysId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->countrysId = $countrysId;

        $this->countrysGateway = new CountryGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':

                if ($this->countrysId) {
                    $response = $this->getCountry($this->countrysId);
                } else {
                    if(isset($_GET["box_model"]))
                    {
                        $response = $this->getAllCountries(true);
                    }
                    else{
                        $response = $this->getAllCountries();
                    }
                }

                break;
//            case 'POST':
//                    $response = $this->uploadCountrysFromRequest();
//                break;
//            case 'PUT':
//                $response = $this->updateCountrysFromRequest($this->countrysId);
//                break;
//            case 'DELETE':
//                $response = $this->deleteCountrys($this->countrysId);
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

    private function getAllCountries($forComboBox = false)
    {
        $result = $this->countrysGateway->findAll($forComboBox);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getCountry($id)
    {
        $result = $this->countrysGateway->find($id);
        /*if (! $result) {
//            return $this->notFoundResponse();
        }*/
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createCountryFromRequest()
    {
        $input = (array) json_decode(country_get_contents('php://input'), TRUE);
        if (! $this->validateCountrys($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->countrysGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateCountryFromRequest($id)
    {
        $result = $this->countrysGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(country_get_contents('php://input'), TRUE);
        if (! $this->validateCountrys($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->countrysGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteCountry($id)
    {
        $result = $this->countrysGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->countrysGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateCountrys($input)
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
