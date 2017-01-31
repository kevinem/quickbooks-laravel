<?php

use Psr\Http\Message\ResponseInterface;

return [

    'consumer_key' => '',

    'consumer_secret' => '',

    'callback' => '',

    'version' => 'v3',

    'env' => 'sandbox',

    'realm_id' => '',

    'parser' => function (ResponseInterface $response) {
        $xml = simplexml_load_string((string)$response->getBody());
        return json_decode(json_encode($xml), TRUE);
    },

    'token_resolver' => function () {
        return [
            'access_token' => '',
            'access_token_secret' => ''
        ];
    }
];