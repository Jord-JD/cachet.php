<?php

namespace JordJD\CachetPHP\Factories;

abstract class IncidentFactory
{
    public static function getAll($cachetInstance, $sort = null, $order = null)
    {
        return CachetElementFactory::getAll($cachetInstance, 'incidents', $sort, $order);
    }

    public static function getById($cachetInstance, $id)
    {
        return CachetElementFactory::getById($cachetInstance, 'incidents', $id);
    }

    public static function create($cachetInstance, $data)
    {
        return CachetElementFactory::create($cachetInstance, 'incidents', $data);
    }
}
