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
        return $this->gateway->findModel($id);
    }

    protected function getRecordAlt(int $id) {
    return $this->gateway->findAltModel($id);
    }

    protected function insertRecord() {
        return $this->gateway->insertModel($this);
    }

    protected function updateRecord() {
        return $this->gateway->updateModel($this);
    }

    public function deleteRecord($id)
    {
        return $this->gateway->deleteModel($id);
    }

}