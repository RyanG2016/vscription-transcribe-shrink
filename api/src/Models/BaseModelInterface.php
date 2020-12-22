<?php

namespace Src\Models;

interface BaseModelInterface
{
    public function fill(bool|array $row);
    public function save():int;
    public function delete():int;
}