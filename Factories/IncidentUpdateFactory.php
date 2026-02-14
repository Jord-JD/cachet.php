<?php

namespace JordJD\CachetPHP\Factories;

use JordJD\CachetPHP\Objects\IncidentUpdate;
use JordJD\CachetPHP\Objects\MetricPoint;

abstract class IncidentUpdateFactory
{
    public function getAll($cachetInstance, $incident, $sort = null, $order = null)
    {
        $response = $cachetInstance->guzzleClient->get('incidents/'.$incident->id.'/updates',
            ['query' => ['sort' => $sort,
                'order'         => $order, ],
            ]);

        if ($response->getStatusCode() != 200) {
            throw new \Exception('Bad response from Cachet instance.');
        }

        $data = json_decode($response->getBody());

        if (!$data) {
            throw new \Exception('Could not decode JSON retrieved from Cachet instance.');
        }

        if (isset($data->data)) {
            $data = $data->data;
        }

        $toReturn = [];

        foreach ($data as $row) {
            $toReturn[] = new IncidentUpdate($cachetInstance, $row);
        }

        return $toReturn;
    }

    public function create($cachetInstance, $incident, $data)
    {
        $newComponentStatus = isset($data['component_status']) ? $data['component_status'] : null;
        unset($data['component_status']);

        $response = $cachetInstance->guzzleClient->post('incidents/'.$incident->id.'/updates', ['json' => $data, 'headers' => $cachetInstance->getAuthHeaders()]);

        if ($response->getStatusCode() != 200) {
            throw new \Exception('Bad response from Cachet instance.');
        }

        $data = json_decode($response->getBody());

        if (!$data) {
            throw new \Exception('Could not decode JSON retrieved from Cachet instance.');
        }

        if (isset($data->data)) {
            $data = $data->data;
        }

        if ($newComponentStatus) {
            $incident->component_status = $newComponentStatus;
            $incident->save();
        }

        return new IncidentUpdate($cachetInstance, $data);
    }
}
