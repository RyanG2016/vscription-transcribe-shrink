<?php


namespace Src\Models;


class BaseModel
{
    private $gateway;

    public function __construct($defaultGateway)
    {
        $this->gateway = $defaultGateway;
    }

    protected function getRecord(int $id) {
        return $this->gateway->find($id);
    }

    protected function getRecordAlt(int $id) {
    return $this->gateway->findAlt($id);
    }

    protected function insertRecord() {
        return $this->gateway->insert($this);
    }

    protected function updateRecord() {
        return $this->gateway->update($this);
    }

    public function deleteRecord($id)
    {
        return $this->gateway->delete($id);
    }

}