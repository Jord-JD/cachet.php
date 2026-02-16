<?php

namespace JordJD\CachetPHP\Tests\Factories;

use GuzzleHttp\Psr7\Response;
use JordJD\CachetPHP\Factories\MetricPointFactory;
use JordJD\CachetPHP\Objects\Metric;
use JordJD\CachetPHP\Objects\MetricPoint;
use JordJD\CachetPHP\Tests\Support\CreatesMockCachetInstance;
use PHPUnit\Framework\TestCase;

class MetricPointFactoryTest extends TestCase
{
    use CreatesMockCachetInstance;

    public function testCreateReturnsMetricPointObject()
    {
        $cachetInstance = $this->makeCachetInstance([
            new Response(200, [], '{"data":{"id":99,"value":42}}'),
        ], $history);
        $metric = new Metric($cachetInstance, (object) ['id' => 3]);

        $metricPoint = MetricPointFactory::create($cachetInstance, $metric, ['value' => 42]);

        $this->assertInstanceOf(MetricPoint::class, $metricPoint);
        $this->assertSame(99, $metricPoint->id);
        $this->assertSame(42, $metricPoint->value);
        $this->assertCount(1, $history);
        $this->assertSame('/api/v1/metrics/3/points', $history[0]['request']->getUri()->getPath());
        $this->assertSame('test-token', $history[0]['request']->getHeaderLine('X-Cachet-Token'));
    }
}
