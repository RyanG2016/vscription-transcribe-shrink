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
}