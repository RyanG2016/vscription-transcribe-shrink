<?php


namespace Src\Enums;
use MyCLabs\Enum\Enum;

class HTTP_CONTENT_TYPE extends Enum
{
    CONST JSON = "application/json";
    CONST TEXT = "text/plain";
    CONST MULTIPART = "multipart/form-data";
}