<?php


namespace Src\Traits;


use Src\Enums\HTTP_CONTENT_TYPE;
use Src\Enums\HTTP_RESPONSE;

trait httpResponse
{
    public function respond(string $HTTP_RESPONSE, $body = '',
                            string $CONTENT_TYPE = HTTP_CONTENT_TYPE::TEXT_PLAIN): array
    {
        $response['status_code_header'] = $HTTP_RESPONSE;
        $response['body'] = $body;
        $response['content_type'] = $CONTENT_TYPE;
        /*$response['body'] = json_encode([
            'error' => true,
            'msg' => 'Invalid input'
        ]);*/
        return $response;
    }

}