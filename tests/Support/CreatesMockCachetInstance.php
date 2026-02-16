<?php

namespace JordJD\CachetPHP\Tests\Support;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use JordJD\CachetPHP\Factories\CachetInstanceFactory;

trait CreatesMockCachetInstance
{
    protected function makeCachetInstance(array $responses, &$history, $apiToken = 'test-token')
    {
        $mockHandler = new MockHandler($responses);
        $history = [];

        $handlerStack = HandlerStack::create($mockHandler);
        $handlerStack->push(Middleware::history($history));

        $cachetInstance = CachetInstanceFactory::create('https://status.example/api/v1/', $apiToken);
        $cachetInstance->guzzleClient = new Client([
            'handler'  => $handlerStack,
            'base_uri' => 'https://status.example/api/v1/',
        ]);

        return $cachetInstance;
    }
}
