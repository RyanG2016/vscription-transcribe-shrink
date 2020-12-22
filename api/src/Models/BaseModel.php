<?php


namespace Src\Models;


class BaseModel
{
    private $gateway;

    public function __construct($defaultGateway)
    {
        $this->gateway = $defaultGateway;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        $class_vars = get_object_vars($this);

        foreach ($class_vars as $name => $value) {
            if(!is_object($value))
            {
                $arr[$name] = $value;
            }
        }

        return json_encode($arr, JSON_PRETTY_PRINT);
    }

    protected function getRecord(int $id) {
        return $this->gateway->find($id);
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