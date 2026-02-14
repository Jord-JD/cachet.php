<?php

namespace JordJD\CachetPHP\Factories;

abstract class SubscriberFactory
{
    public function getAll($cachetInstance, $sort = null, $order = null)
    {
        return CachetElementFactory::getAll($cachetInstance, 'subscribers', $sort, $order, true);
    }

    public function getById($cachetInstance, $id)
    {
        return CachetElementFactory::getAll($cachetInstance, 'subscribers', $id, true);
    }

    public function create($cachetInstance, $data)
    {
        return CachetElementFactory::create($cachetInstance, 'subscribers', $data);
    }
}
