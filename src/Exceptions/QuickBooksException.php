<?php


namespace KevinEm\QuickBooks\Laravel\Exceptions;


use Exception;

class QuickBooksException extends \Exception
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}