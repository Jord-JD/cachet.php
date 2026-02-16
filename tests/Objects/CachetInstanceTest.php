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
