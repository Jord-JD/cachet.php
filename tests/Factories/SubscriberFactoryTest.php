<?php

namespace JordJD\CachetPHP\Tests\Factories;

use GuzzleHttp\Psr7\Response;
use JordJD\CachetPHP\Factories\SubscriberFactory;
use JordJD\CachetPHP\Objects\Subscriber;
use JordJD\CachetPHP\Tests\Support\CreatesMockCachetInstance;
use PHPUnit\Framework\TestCase;

class SubscriberFactoryTest extends TestCase
{
    use CreatesMockCachetInstance;

    public function testGetByIdUsesSubscriberEndpointAndAuthHeader()
    {
        $cachetInstance = $this->makeCachetInstance([
            new Response(200, [], '{"data":{"id":7,"email":"person@example.com"}}'),
        ], $history);

        $subscriber = SubscriberFactory::getById($cachetInstance, 7);

        $this->assertInstanceOf(Subscriber::class, $subscriber);
        $this->assertSame(7, $subscriber->id);
        $this->assertCount(1, $history);
        $this->assertSame('/api/v1/subscribers/7', $history[0]['request']->getUri()->getPath());
        $this->assertSame('test-token', $history[0]['request']->getHeaderLine('X-Cachet-Token'));
    }
}
