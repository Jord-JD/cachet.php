<?php

namespace JordJD\CachetPHP\Tests\Objects;

use GuzzleHttp\Psr7\Response;
use JordJD\CachetPHP\Factories\CachetInstanceFactory;
use JordJD\CachetPHP\Tests\Support\CreatesMockCachetInstance;
use PHPUnit\Framework\TestCase;

class CachetInstanceTest extends TestCase
{
    use CreatesMockCachetInstance;

    public function testConstructorRequiresBaseUrl()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You must specify the base URL for your Cachet instance.');

        CachetInstanceFactory::create(null, 'test-token');
    }

    public function testConstructorNormalizesBaseUrlAndAcceptsClientOptions()
    {
        $cachetInstance = CachetInstanceFactory::create(
            'https://status.example/api/v1',
            'test-token',
            null,
            array('timeout' => 10.0)
        );

        $this->assertSame('https://status.example/api/v1/', (string) $cachetInstance->guzzleClient->getConfig('base_uri'));
        $this->assertSame(10.0, $cachetInstance->guzzleClient->getConfig('timeout'));
    }

    public function testConstructorAcceptsAnInjectedClient()
    {
        $client = new \GuzzleHttp\Client(array('base_uri' => 'https://custom.example/'));
        $cachetInstance = CachetInstanceFactory::create('https://status.example/api/v1/', 'test-token', $client);

        $this->assertSame($client, $cachetInstance->guzzleClient);
    }

    public function testPingReturnsPongDataAndIsWorkingReturnsTrue()
    {
        $cachetInstance = $this->makeCachetInstance([
            new Response(200, [], '{"data":"Pong!"}'),
            new Response(200, [], '{"data":"Pong!"}'),
        ], $history);

        $this->assertSame('Pong!', $cachetInstance->ping());
        $this->assertTrue($cachetInstance->isWorking());
        $this->assertCount(2, $history);
    }

    public function testPingThrowsExceptionWhenJsonCannotBeDecoded()
    {
        $cachetInstance = $this->makeCachetInstance([
            new Response(200, [], '{'),
        ], $history);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('cachet.php: Could not decode JSON response from ping endpoint.');

        $cachetInstance->ping();
    }
}
