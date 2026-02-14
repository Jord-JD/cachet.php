<?php

namespace JordJD\CachetPHP\Objects;

use JordJD\CachetPHP\Factories\IncidentUpdateFactory;

class Incident
{
    private $cachetInstance = null;

    public function __construct($cachetInstance, $row)
    {
        $this->cachetInstance = $cachetInstance;

        foreach ($row as $key => $value) {
            $this->$key = $value;
        }
    }

    public function delete()
    {
        $this->cachetInstance->guzzleClient->delete('incidents/'.$this->id, ['headers' => $this->cachetInstance->getAuthHeaders()]);
    }

    public function save()
    {
        $queryParams = [];

        $queryParams['name'] = $this->name;
        $queryParams['message'] = $this->message;
        $queryParams['status'] = $this->status;
        $queryParams['visible'] = $this->visible;
        $queryParams['component_id'] = $this->component_id;
        $queryParams['component_status'] = $this->component_status;

        $this->cachetInstance->guzzleClient->put('incidents/'.$this->id, ['headers' => $this->cachetInstance->getAuthHeaders(),
            'query' => $queryParams, ]);
    }

    public function getAllIncidentUpdates($sort = null, $order = null)
    {
        return IncidentUpdateFactory::getAll($this->cachetInstance, $this, $sort, $order);
    }

    public function createIncidentUpdate($data)
    {
        return IncidentUpdateFactory::create($this->cachetInstance, $this, $data);
    }
}
