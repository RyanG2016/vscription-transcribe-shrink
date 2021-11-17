<?php


namespace Src\Traits;


trait modelToString
{
    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        $class_vars = get_object_vars($this);

        foreach ($class_vars as $name => $value) {
            if(!is_object($value) && !str_contains($name, "gateway") && $name != 'db')
            {
                $arr[$name] = $value;
            }
        }

        return json_encode($arr, JSON_PRETTY_PRINT);
    }
}