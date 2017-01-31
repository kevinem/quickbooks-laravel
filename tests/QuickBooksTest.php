<?php


namespace KevinEm\QuickBooks\Laravel\Tests;


use Closure;
use GuzzleHttp\Client;
use KevinEm\QuickBooks\Laravel\QuickBooks;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Wheniwork\OAuth1\Client\Server\Intuit;

class QuickBooksTest extends TestCase
{

    /**
     * @var QuickBooks
     */
    private $quickBooks;

    /**
     * @var m\Mock|Client
     */
    private $clientMock;

    /**
     * @var m\Mock|Intuit
     */
    private $intuitMock;

    /**
     * @var m\Mock|Closure
     */
    private $tokenResolverMock;

    /**
     * @var m\Mock|Closure
     */
    private $parserMock;

    protected function setUp()
    {
        parent::setUp();
        $this->clientMock = m::mock(Client::class);
        $this->intuitMock = m::mock(Intuit::class);
        $this->parserMock = $this->getClosureMock();
        $this->tokenResolverMock = $this->getClosureMock();
        $config = [
            'consumer_key' => '',
            'consumer_secret' => '',
            'callback' => '',
            'version' => 'v3',
            'env' => 'staging',
            'realm_id' => 'mock_realm_id',
            'parser' => $this->parserMock,
            'token_resolver' => $this->tokenResolverMock
        ];
        $this->quickBooks = new QuickBooks($config, $this->clientMock, $this->intuitMock);
    }

    private function getClosureMock()
    {
        return $this->createPartialMock(\stdClass::class, ['__invoke']);
    }

    public function testRequest()
    {
        $this->parserMock
            ->expects(self::once())
            ->method('__invoke')
            ->willReturn(m::mock());

        $this->tokenResolverMock
            ->expects(self::once())
            ->method('__invoke')
            ->willReturn([
                'access_token' => 'mock_access_token',
                'access_token_secret' => 'mock_access_token_secret'
            ]);

        $headersMock = m::mock();
        $method = 'GET';
        $url = 'https://appcenter.intuit.com/api/v1/user/current';
        $this->intuitMock->shouldReceive('getHeaders')->andReturn($headersMock);
        $this->clientMock->shouldReceive('request')->with($method, $url, ['headers' => $headersMock]);
        $this->quickBooks->request($method, $url);
    }

    public function testSandboxUrl()
    {
        $this->assertEquals('https://sandbox-quickbooks.api.intuit.com/v3/company/mock_realm_id', $this->quickBooks->buildUrl());
    }

    public function testProductionUrl()
    {
        $this->quickBooks->setEnv('production');
        $this->assertEquals('https://quickbooks.api.intuit.com/v3/company/mock_realm_id', $this->quickBooks->buildUrl());
    }
}