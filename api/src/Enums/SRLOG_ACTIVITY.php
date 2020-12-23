<?php


namespace Src\Enums;
use MyCLabs\Enum\Enum;

class SRLOG_ACTIVITY extends Enum
{
    const QUEUED = "Queued";
    const PROCESSING = "rev.ai processing file";
    const COMPLETE = "Complete";
    const FAILED = "Failed";
    const REVAI_SEND_ERROR = "rev.ai send error";
//    const REVAI_RECEIVE_ERROR = "rev.ai file processing failed";
    const MANUAL_REVISION_REQ = "Manual Revision Required";
    const INSUFFICIENT_BALANCE = "Insufficient Balance";
    const COULD_COPY_TO_TEMP = "Couldn't copy to temp dir";
    const COPIED_TO_TEMP = "Copied to temp dir";
    const DDL_GENERATED = "Generated DDL Link";
    const REFUND_DIFF_MINUTES = "Refund minutes";
    const DEDUCT_DIFF_MINUTES = "Deduct minutes";
    const ADDED_MINUTES_TO_ACC = self::REFUND_DIFF_MINUTES;
    const REVAI_ID_NOT_FOUND = "rev.ai returned ID not found in db";
    const UNKNOWN_WEBHOOK_BODY = "unknown webhook request body";
}