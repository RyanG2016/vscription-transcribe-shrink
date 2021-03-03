<?php


namespace Src\Enums;
use MyCLabs\Enum\Enum;

class HTTP_RESPONSE extends Enum
{
    CONST HTTP_OK = "HTTP/1.1 200 OK";
    CONST HTTP_NOT_FOUND = "HTTP/1.1 404 Not Found";
    CONST HTTP_UNPROCESSABLE_ENTITY = "HTTP/1.1 422 Unprocessable Entity";
}