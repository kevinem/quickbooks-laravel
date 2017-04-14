<?php


namespace KevinEm\QuickBooks\Laravel;


class TokenResolver
{

    /**
     * @return array
     */
    public function resolve()
    {
        return [
            'access_token' => env('QUICKBOOKS_ACCESS_TOKEN', ''),
            'access_token_secret' => env('QUICKBOOKS_TOKEN_SECRET', '')
        ];
    }
}