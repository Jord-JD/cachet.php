<?php

namespace JordJD\CachetPHP\Tests\Factories;

use GuzzleHttp\Psr7\Response;
use JordJD\CachetPHP\Factories\ComponentFactory;
use JordJD\CachetPHP\Tests\Support\CreatesMockCachetInstance;
use PHPUnit\Framework\TestCase;

class ComponentFactoryTest extends TestCase
{
    use CreatesMockCachetInstance;

    public function testGetAllMergesPaginatedResultsAndAppliesAscendingSortFallback()
    {
        $cachetInstance = $this->makeCachetInstance([
            new Response(200, [], '{"data":[{"id":3,"name":"Zulu"},{"id":2,"name":"Bravo"}],"meta":{"pagination":{"total_pages":2}}}'),
            new Response(200, [], '{"data":[{"id":1,"name":"Alpha"}],"meta":{"pagination":{"total_pages":2}}}'),
        ], $history);

        $components = ComponentFactory::getAll($cachetInstance, 'name', 'asc');

        $this->assertCount(3, $components);
        $this->assertSame(['Alpha', 'Bravo', 'Zulu'], array_map(function ($component) {
            return $component->name;
        }, $components));

        $this->assertCount(2, $history);
        parse_str($history[0]['request']->getUri()->getQuery(), $firstQuery);
        parse_str($history[1]['request']->getUri()->getQuery(), $secondQuery);

        $this->assertSame('name', $firstQuery['sort']);
        $this->assertSame('asc', $firstQuery['order']);
        $this->assertArrayNotHasKey('page', $firstQuery);

        $this->assertSame('name', $secondQuery['sort']);
        $this->assertSame('asc', $secondQuery['order']);
        $this->assertSame('2', $secondQuery['page']);
    }
}
