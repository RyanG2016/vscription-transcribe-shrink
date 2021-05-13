<?php


namespace Src\Enums;
use MyCLabs\Enum\Enum;

class ROLES extends Enum
{
    const SYSTEM_ADMINISTRATOR = 1;
    const ACCOUNT_ADMINISTRATOR = 2;
    const TYPIST = 3;
    const REVIEWER = 4;
    const AUTHOR = 5;
    const PENDING = 6;

}