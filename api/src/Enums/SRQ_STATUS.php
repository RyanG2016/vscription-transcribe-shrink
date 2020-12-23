<?php


namespace Src\Enums;
use MyCLabs\Enum\Enum;

class SRQ_STATUS extends Enum
{
    const QUEUED = 0;            // queue for sending to rev.ai
    const PROCESSING = 1;        // sent to rev.ai
    const COMPLETE = 2;          // SR is complete
    const FAILED = 3;            // SR failed reason should be in notes
    const MANUAL_REVISION_REQ = 5; // not used till now
    const INSUFFICIENT_BALANCE = 6; // Account doesn't have sufficient minute balance, file won't be sent to rev.ai and switched to normal transcription
    const REVAI_FAILED_TO_RESPOND_WITH_SUCCESS = 7; // max retries to send to rev.ai servers reached, file sent to normal trans.
    const WAITING_SWITCH_CONVERT = 8; // waiting for conversionCron
    const INTERNAL_PROCESSING = 9; // File is queued for vtt/transcript fetch from rev.ai server | rev.ai OK
}