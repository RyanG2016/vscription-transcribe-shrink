<?php
namespace  Src\System;

use bandwidthThrottle\tokenBucket\Rate;
use bandwidthThrottle\tokenBucket\TokenBucket;
use bandwidthThrottle\tokenBucket\storage\SessionStorage;

date_default_timezone_set('America/Winnipeg');
class Throttler {

    public function __construct($bucketName, $tokensPerUnit, $unit)
    {
        //$storage = new FileStorage(__DIR__ . "/api.bucket");
        $storage = new SessionStorage($bucketName.".bucket");
        $rate    = new Rate($tokensPerUnit, $unit);
        $bucket  = new TokenBucket($tokensPerUnit, $rate, $storage);
        //$bucket->bootstrap(5);

        if (!$bucket->consume(1, $seconds)) {
            http_response_code(429);
            header(sprintf("Retry-After: %d", floor($seconds)));
            print_r(array(
                "msg" => "slow down..",
                "retry-after" => floor($seconds)
            ));
            exit();
        }
    }

    /*public function getSth()
    {
        return "Sth";
    }*/
}