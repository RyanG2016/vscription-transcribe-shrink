<?php
namespace Src\Controller;

use Src\TableGateways\FileGateway;

class FileController {

    private $db;
    private $requestMethod;
    private $fileId;

    private $fileGateway;

    public function __construct($db, $requestMethod, $fileId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->fileId = $fileId;

        $this->fileGateway = new FileGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->fileId) {
                    $response = $this->getFile($this->fileId);
                } else {
                    $response = $this->getAllFiles();
                };
                break;
//            case 'POST':
//                $response = $this->createFileFromRequest();
//                break;
//            case 'PUT':
//                $response = $this->updateFileFromRequest($this->fileId);
//                break;
//            case 'DELETE':
//                $response = $this->deleteFile($this->fileId);
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

    private function getAllFiles()
    {
        $result = $this->fileGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getFile($id)
    {
        $result = $this->fileGateway->find($id);
        /*if (! $result) {
//            return $this->notFoundResponse();
        }*/
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createFileFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateFile($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->fileGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateFileFromRequest($id)
    {
        $result = $this->fileGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateFile($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->fileGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteFile($id)
    {
        $result = $this->fileGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->fileGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateFile($input)
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
            'error' => 'Invalid input'
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