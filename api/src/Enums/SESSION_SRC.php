<?php


namespace Src\Enums;
use MyCLabs\Enum\Enum;

class SESSION_SRC extends Enum
{
    const EXTEND_AMOUNT = "+8 Hour";
    const DEF_EXPIRE_TIME = "+1 Day";
    // const DEF_EXPIRE_TIME = "+11 Minute";

    const WEBSITE = 0;
    const API = 1;
}