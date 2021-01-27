<?php


namespace Src\Enums;
use MyCLabs\Enum\Enum;

class USER_ACCOUNT_STATUS extends Enum
{
    CONST LOCKED = 0;
    CONST ACTIVE = 1;
    CONST PENDING_EMAIL_VERIFICATION = 5;
}