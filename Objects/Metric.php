<?php

namespace JordJD\CachetPHP\Objects;

use JordJD\CachetPHP\Factories\MetricPointFactory;

class Metric
{
    private $cachetInstance = null;

    public function __construct($cachetInstance, $row)
    {
        $this->cachetInstance = $cachetInstance;

        foreach ($row as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getAllMetricPoints($sort = null, $order = null)
    {
        return MetricPointFactory::getAll($this->cachetInstance, $this, $sort, $order);
    }

    public function delete()
    {
        $this->cachetInstance->guzzleClient->delete('metrics/'.$this->id, ['headers' => $this->cachetInstance->getAuthHeaders()]);
    }

    public function save()
    {
        $queryParams = [];

        $queryParams['name'] = $this->name;
        $queryParams['description'] = $this->description;
        $queryParams['suffix'] = $this->suffix;
        $queryParams['display_chart'] = $this->display_chart;

        $this->cachetInstance->guzzleClient->put('metrics/'.$this->id, ['headers' => $this->cachetInstance->getAuthHeaders(),
            'query' => $queryParams, ]);
    }

    public function createMetricPoint($data)
    {
        return MetricPointFactory::create($this->cachetInstance, $this, $data);
    }
}
