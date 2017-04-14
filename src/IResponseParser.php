<?php


namespace KevinEm\QuickBooks\Laravel;


use Psr\Http\Message\ResponseInterface;

interface IResponseParser
{
    public function parse(ResponseInterface $response);
}