<?php

namespace JordJD\CachetPHP\Factories;

use GuzzleHttp\ClientInterface;
use JordJD\CachetPHP\Objects\CachetInstance;

abstract class CachetInstanceFactory
{
    public static function create($baseUrl, $apiToken, ClientInterface $client = null, array $clientOptions = array())
    {
        return new CachetInstance($baseUrl, $apiToken, $client, $clientOptions);
    }
}
