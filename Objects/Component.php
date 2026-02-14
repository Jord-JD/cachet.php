<?php

namespace JordJD\CachetPHP\Objects;

class Component
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
        $this->cachetInstance->guzzleClient->delete('components/'.$this->id, ['headers' => $this->cachetInstance->getAuthHeaders()]);
    }

    public function save()
    {
        $queryParams = [];

        $queryParams['name'] = $this->name;
        $queryParams['description'] = $this->description;
        $queryParams['link'] = $this->link;
        $queryParams['status'] = $this->status;
        $queryParams['order'] = $this->order;
        $queryParams['group_id'] = $this->group_id;

        $this->cachetInstance->guzzleClient->put('components/'.$this->id, ['headers' => $this->cachetInstance->getAuthHeaders(),
            'query' => $queryParams, ]);
    }
}
