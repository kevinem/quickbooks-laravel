<?php

return [

    'consumer_key' => '',

    'consumer_secret' => '',

    'callback' => '',

    'version' => 'v3',

    'env' => 'sandbox',

    'realm_id' => '',

    'parser' => \KevinEm\QuickBooks\Laravel\ResponseParser::class,

    'token_resolver' => \KevinEm\QuickBooks\Laravel\TokenResolver::class

];