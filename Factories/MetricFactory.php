<?php

namespace JordJD\CachetPHP\Factories;

abstract class MetricFactory
{
    public function getAll($cachetInstance, $sort = null, $order = null)
    {
        return CachetElementFactory::getAll($cachetInstance, 'metrics', $sort, $order);
    }

    public function getById($cachetInstance, $id)
    {
        return CachetElementFactory::getById($cachetInstance, 'metrics', $id);
    }

    public function create($cachetInstance, $data)
    {
        return CachetElementFactory::create($cachetInstance, 'metrics', $data);
    }
}
