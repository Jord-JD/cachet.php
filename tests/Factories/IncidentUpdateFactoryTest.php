<?php

namespace JordJD\CachetPHP\Tests\Factories;

use GuzzleHttp\Psr7\Response;
use JordJD\CachetPHP\Factories\IncidentUpdateFactory;
use JordJD\CachetPHP\Objects\IncidentUpdate;
use JordJD\CachetPHP\Tests\Support\CreatesMockCachetInstance;
use PHPUnit\Framework\TestCase;

class IncidentUpdateFactoryTest extends TestCase
{
    use CreatesMockCachetInstance;

    public function testCreateAppliesComponentStatusWhenValueIsZero()
    {
        $cachetInstance = $this->makeCachetInstance([
            new Response(200, [], '{"data":{"id":15,"incident_id":5,"status":2,"message":"Updated"}}'),
        ], $history);

        $incident = new class {
            public $id = 5;
            public $component_status;
            public $saveCalled = false;

            public function save()
            {
                $this->saveCalled = true;
            }
        };

        $incidentUpdate = IncidentUpdateFactory::create($cachetInstance, $incident, [
            'status'           => 2,
            'message'          => 'Updated',
            'component_status' => 0,
        ]);

        $this->assertInstanceOf(IncidentUpdate::class, $incidentUpdate);
        $this->assertTrue($incident->saveCalled);
        $this->assertSame(0, $incident->component_status);
        $this->assertCount(1, $history);
    }
}
