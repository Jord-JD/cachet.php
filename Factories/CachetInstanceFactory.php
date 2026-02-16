<?php

namespace JordJD\CachetPHP\Factories;

use JordJD\CachetPHP\Objects\CachetInstance;

abstract class CachetInstanceFactory
{
    public static function create($baseUrl, $apiToken)
    {
        return new CachetInstance($baseUrl, $apiToken);
    }
}
