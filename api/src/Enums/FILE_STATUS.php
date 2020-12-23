<?php


namespace Src\Enums;
use MyCLabs\Enum\Enum;

class FILE_STATUS extends Enum
{
    CONST AWAITING_TRANSCRIPTION = 0;
    CONST BEING_TYPED = 1;
    CONST SUSPENDED = 2;
    CONST COMPLETED = 3;
    CONST COMPLETED_W_INCOMPLETES = 4;
    CONST COMPLETED_NO_TEXT = 5;
    CONST RECOGNITION_IN_PROGRESS = 6;
    CONST AWAITING_CORRECTION = 7;
    CONST QUEUED_FOR_CONVERSION = 8;
    CONST QUEUED_FOR_SR_CONVERSION = 9;
    CONST QUEUED_FOR_RECOGNITION = 10;

}