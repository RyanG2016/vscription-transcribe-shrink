<?php


namespace Src\Enums;
use MyCLabs\Enum\Enum;

class SRLOG_ACTIVITY extends Enum
{
    const QUEUED = "QUEUED";
    const PROCESSING = "PROCESSING";
    const COMPLETE = "COMPLETE";
    const FAILED = "FAILED";
    const MANUAL_REVISION_REQ = "MANUAL REVISION REQ";
    const INSUFFICIENT_BALANCE = "INSUFFICIENT BALANCE";
}