<?php

namespace JordJD\CachetPHP\Factories;

abstract class SubscriberFactory
{
    public static function getAll($cachetInstance, $sort = null, $order = null)
    {
        return CachetElementFactory::getAll($cachetInstance, 'subscribers', $sort, $order, true);
    }

    public static function getById($cachetInstance, $id)
    {
        return CachetElementFactory::getById($cachetInstance, 'subscribers', $id, true);
    }

    public static function create($cachetInstance, $data)
    {
        return CachetElementFactory::create($cachetInstance, 'subscribers', $data);
    }
}
