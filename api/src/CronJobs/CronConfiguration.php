<?php


namespace Src\CronJobs;


class CronConfiguration
{
    /**
    *   Restarting CronJobs after MAX_ITERATIONS
    *   Due to PHP Limitation,
    *   PHP doesn't free up memory unless the script or scope is changed
    *   - which is not the case in a continuously running daemon
    *
    *   For more details:
    *   @link https://stackoverflow.com/questions/31863777/php-having-some-memory-issues-inside-a-loop
    *
    **/
    const MAX_ITERATIONS = 10000;            // shared with all cronJobs inside this folder
    const REVAI_SUBMITTER_SLEEP_TIME = 10;  // seconds
    const REVAI_RECEIVER_SLEEP_TIME = 10;   // seconds
    const CONVERSION_SLEEP_TIME = 5;        // seconds
}