<?php


namespace Src\Enums;
use MyCLabs\Enum\Enum;

/** USED IN settings.php PAGE */
class CUSTOM_FIELD_ERRORS extends Enum
{
    CONST NAME = "Name should only be letters and between 2 and 50 characters";
    CONST ORG = "May only contain underscores and spaces, between 2 and 255 characters";
    CONST RETENTION_TIME = "Should be between 1 and 180 days";
    CONST LOG_RETENTION_TIME = "Should be between 1 and 180 days";
    CONST CITY = "Should only be letters between 2 and 100 characters";
    CONST STATE = "Should only be letters between 2 and 100 characters";
    CONST ZIP = "Should be between 0 and 20 alphanumeric chars";
    CONST STREET = "alphanumeric, dots, spaces, underscores between 5 and 100 chars";
}