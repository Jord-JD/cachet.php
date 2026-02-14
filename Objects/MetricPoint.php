<?php

namespace JordJD\CachetPHP\Objects;

class MetricPoint
{
    private $cachetInstance = null;
    private $metric = null;

    public function __construct($cachetInstance, $metric, $row)
    {
        $this->cachetInstance = $cachetInstance;
        $this->metric = $metric;

        foreach ($row as $key => $value) {
            $this->$key = $value;
        }
    }

    public function delete()
    {
        $this->cachetInstance->guzzleClient->delete('metrics/'.$this->metric->id.'/points/'.$this->id, ['headers' => $this->cachetInstance->getAuthHeaders()]);
    }

    public function save()
    {
        $queryParams = [];

        $queryParams['value'] = $this->value;

        $this->cachetInstance->guzzleClient->put('metrics/'.$this->metric->id.'/points/'.$this->id, ['headers' => $this->cachetInstance->getAuthHeaders(),
            'query' => $queryParams, ]);
    }
}
