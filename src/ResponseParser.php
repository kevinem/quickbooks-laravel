<?php


namespace KevinEm\QuickBooks\Laravel;


use Psr\Http\Message\ResponseInterface;

class ResponseParser implements IResponseParser
{

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public function parse(ResponseInterface $response)
    {
        $xml = simplexml_load_string((string)$response->getBody());
        return json_decode(json_encode($xml), TRUE);
    }
}