<?php

namespace JordJD\CachetPHP\Factories;

abstract class ComponentFactory
{
    public static function getAll($cachetInstance, $sort = null, $order = null)
    {
        return CachetElementFactory::getAll($cachetInstance, 'components', $sort, $order);
    }

    public static function getById($cachetInstance, $id)
    {
        return CachetElementFactory::getById($cachetInstance, 'components', $id);
    }

    public static function create($cachetInstance, $data)
    {
        return CachetElementFactory::create($cachetInstance, 'components', $data);
    }
}
