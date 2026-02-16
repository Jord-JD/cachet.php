<?php

namespace JordJD\CachetPHP\Tests\Objects;

use GuzzleHttp\Psr7\Response;
use JordJD\CachetPHP\Objects\Component;
use JordJD\CachetPHP\Objects\Incident;
use JordJD\CachetPHP\Objects\Metric;
use JordJD\CachetPHP\Objects\Subscriber;
use JordJD\CachetPHP\Tests\Support\CreatesMockCachetInstance;
use PHPUnit\Framework\TestCase;

class GetByIdMethodsTest extends TestCase
{
    use CreatesMockCachetInstance;

    public function testGetByIdMethodsReturnCorrectObjectTypes()
    {
        $cachetInstance = $this->makeCachetInstance([
            new Response(200, [], '{"data":{"id":1,"name":"API"}}'),
            new Response(200, [], '{"data":{"id":2,"name":"Outage","status":1}}'),
            new Response(200, [], '{"data":{"id":3,"name":"Latency"}}'),
            new Response(200, [], '{"data":{"id":4,"email":"person@example.com"}}'),
        ], $history);

        $component = $cachetInstance->getComponentById(1);
        $incident = $cachetInstance->getIncidentById(2);
        $metric = $cachetInstance->getMetricById(3);
        $subscriber = $cachetInstance->getSubscriberById(4);

        $this->assertInstanceOf(Component::class, $component);
        $this->assertInstanceOf(Incident::class, $incident);
        $this->assertInstanceOf(Metric::class, $metric);
        $this->assertInstanceOf(Subscriber::class, $subscriber);

        $this->assertCount(4, $history);
        $this->assertSame('/api/v1/components/1', $history[0]['request']->getUri()->getPath());
        $this->assertSame('/api/v1/incidents/2', $history[1]['request']->getUri()->getPath());
        $this->assertSame('/api/v1/metrics/3', $history[2]['request']->getUri()->getPath());
        $this->assertSame('/api/v1/subscribers/4', $history[3]['request']->getUri()->getPath());
        $this->assertSame('test-token', $history[3]['request']->getHeaderLine('X-Cachet-Token'));
    }
}
