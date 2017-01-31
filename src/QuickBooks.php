<?php

namespace KevinEm\QuickBooks\Laravel;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use KevinEm\QuickBooks\Laravel\Exceptions\QuickBooksException;
use League\OAuth1\Client\Credentials\TokenCredentials;
use Wheniwork\OAuth1\Client\Server\Intuit;

class QuickBooks
{

    /**
     * @var string
     */
    protected $env;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var \Closure
     */
    protected $parser;

    /**
     * @var \Closure
     */
    protected $tokenResolver;

    /**
     * @var Intuit
     */
    protected $intuit;

    /**
     * @var string
     */
    protected $sandboxUrl = 'https://sandbox-quickbooks.api.intuit.com';

    /**
     * @var string
     */
    protected $productionUrl = 'https://quickbooks.api.intuit.com';

    /**
     * QuickBooksLaravel constructor.
     * @param array $config
     * @param Client $client
     * @param Intuit $intuit
     */
    public function __construct(array $config, Client $client, Intuit $intuit)
    {
        $this->client = $client;
        $this->config = $config;
        $this->parser = $config['parser'];
        $this->tokenResolver = $config['token_resolver'];
        $this->intuit = $intuit;
        $this->env = $config['env'];
    }

    /**
     * @return TokenCredentials
     */
    protected function resolveToken()
    {
        $token = call_user_func([$this->tokenResolver, '__invoke']);
        $tokenCredentials = new TokenCredentials();
        $tokenCredentials->setIdentifier($token['access_token']);
        $tokenCredentials->setSecret($token['access_token_secret']);
        return $tokenCredentials;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->env === 'production' ? $this->productionUrl : $this->sandboxUrl;
    }

    /**
     * @return string
     */
    public function buildUrl()
    {
        return $this->getBaseUrl() . '/' . $this->config['version'] . '/company/' . $this->config['realm_id'];
    }

    /**
     * @param $query
     * @param string $method
     * @return mixed
     */
    public function query($query, $method = 'GET')
    {
        return $this->request($method, $this->buildUrl() . "/query?query=$query");
    }

    /**
     * @param $endpoint
     * @param array $data
     * @return mixed
     */
    public function post($endpoint, array $data)
    {
        return $this->request('POST', $this->buildUrl() . "/$endpoint", ['json' => $data]);
    }

    /**
     * @param $endpoint
     * @return mixed
     */
    public function get($endpoint)
    {
        return $this->request('GET', $this->buildUrl() . "/$endpoint");
    }

    /**
     * @param $method
     * @param $url
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function request($method, $url, array $options = [])
    {
        $token = $this->resolveToken();

        $headers = [
            'headers' => $this->intuit->getHeaders($token, $method, $url)
        ];

        try {
            $response = $this->client->request($method, $url, array_merge($headers, $options));
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $body = $response->getBody();
            $statusCode = $response->getStatusCode();
            throw new QuickBooksException("Received error [$body] with status code [$statusCode].", $statusCode);
        }

        return call_user_func([$this->parser, '__invoke'], $response);
    }

    /**
     * @param string $env
     */
    public function setEnv($env)
    {
        $this->env = $env;
    }

    /**
     * @return \Wheniwork\OAuth1\Client\Server\Intuit
     */
    public function getIntuit()
    {
        return $this->intuit;
    }
}