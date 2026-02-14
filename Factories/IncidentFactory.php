<?php

namespace JordJD\CachetPHP\Factories;

abstract class IncidentFactory
{
    public function getAll($cachetInstance, $sort = null, $order = null)
    {
        return CachetElementFactory::getAll($cachetInstance, 'incidents', $sort, $order);
    }

    public function getById($cachetInstance, $id)
    {
        return CachetElementFactory::getById($cachetInstance, 'incidents', $id);
    }

    public function create($cachetInstance, $data)
    {
        return CachetElementFactory::create($cachetInstance, 'incidents', $data);
    }
}
