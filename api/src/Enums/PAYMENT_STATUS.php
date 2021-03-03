<?php


namespace Src\Enums;
use MyCLabs\Enum\Enum;

class PAYMENT_STATUS extends Enum
{
    CONST RECORDED = 0;
    CONST PAID = 1;
    CONST REFUNDED = 2;
    CONST FAILED = 3;

}