<?php

namespace Src\System;

use bandwidthThrottle\tokenBucket\Rate;
use bandwidthThrottle\tokenBucket\TokenBucket;
use bandwidthThrottle\tokenBucket\storage\SessionStorage;

date_default_timezone_set('America/Winnipeg');

class Throttler
{
    private $bucket;
    private $bucketName;
    private $tokensPerUnit;
    private $unit;

    public function __construct($bucketName, $tokensPerUnit, $unit)
    {
        $this->bucketName = $bucketName;
        $this->tokensPerUnit = $tokensPerUnit;
        $this->unit = $unit;

        //$storage = new FileStorage(__DIR__ . "/api.bucket");
        $storage = new SessionStorage($bucketName . ".bucket");
        $rate = new Rate($tokensPerUnit, $unit);
        $bucket = new TokenBucket($tokensPerUnit, $rate, $storage);

        $this->bucket = $bucket;
        $bucket->bootstrap($tokensPerUnit);

        if (!$bucket->consume(1, $seconds)) {
            http_response_code(429);
            header(sprintf("Retry-After: %d", floor($seconds)));
            header("Content-Type: application/json; charset=UTF-8");

            echo json_encode(array(
                "msg" => "Slow down..<br/><br/>Retry after: " . floor($seconds) . "s",
                "error" => true,
                "retry-after" => floor($seconds),
                "code" => 429
            ));
            exit();
            //{"error":true,"msg":"Pending Email Verification.","code":5}
        }
    }

    public function bootstrapStorage()
    {
        $this->bucket->bootstrap($this->tokensPerUnit);
    }
}