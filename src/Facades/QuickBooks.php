<?php

namespace KevinEm\QuickBooks\Laravel\Facades;


use Illuminate\Support\Facades\Facade;

class QuickBooks extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'quickbooks';
    }
}