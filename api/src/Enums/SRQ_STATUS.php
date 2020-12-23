<?php


namespace Src\Enums;
use MyCLabs\Enum\Enum;

class SRQ_STATUS extends Enum
{
    const QUEUED = 0;
    const PROCESSING = 1;
    const COMPLETE = 2;
    const FAILED = 3;
    const MANUAL_REVISION_REQ = 5;
    const INSUFFICIENT_BALANCE = 6;
    const REVAI_FAILED_TO_RESPOND_WITH_SUCCESS = 7;
}