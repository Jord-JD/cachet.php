<?php

namespace JordJD\CachetPHP\Factories;

use JordJD\CachetPHP\Objects\Component;
use JordJD\CachetPHP\Objects\Incident;
use JordJD\CachetPHP\Objects\Metric;
use JordJD\CachetPHP\Objects\Subscriber;

abstract class CachetElementFactory
{
    public function getAll($cachetInstance, $type, $sort = null, $order = null, $authorisationRequired = false)
    {
        $requestParameters = ['query' => ['sort' => $sort, 'order' => $order]];

        if ($authorisationRequired) {
            $requestParameters['headers'] = $cachetInstance->getAuthHeaders();
        }

        $response = $cachetInstance->guzzleClient->get($type, $requestParameters);

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
            switch ($type) {

                case 'components':
                    $toReturn[] = new Component($cachetInstance, $row);
                    break;

                case 'incidents':
                    $toReturn[] = new Incident($cachetInstance, $row);
                    break;

                case 'metrics':
                    $toReturn[] = new Metric($cachetInstance, $row);
                    break;

                case 'subscribers':
                    $toReturn[] = new Subscriber($cachetInstance, $row);
                    break;

                default:
                    throw new \Exception('Invalid Cachet element type specified.');
                    break;
            }
        }

        return $toReturn;
    }

    public function getById($cachetInstance, $type, $id, $authorisationRequired = false)
    {
        $requestParameters = [];

        if ($authorisationRequired) {
            $requestParameters['headers'] = $cachetInstance->getAuthHeaders();
        }

        $response = $cachetInstance->guzzleClient->get($type.'/'.$id, $requestParameters);

        if ($response->getStatusCode() != 200) {
            throw new \Exception('Bad response from Cachet instance.');
        }

        $data = json_decode($response->getBody());

        if (!$data) {
            throw new \Exception('Could not decode JSON retrieved from Cachet instance.');
        }

        if (isset($data->data)) {
            $row = $data->data;
        }

        switch ($type) {

            case 'components':
                return new Component($cachetInstance, $row);
                break;

            case 'incidents':
                return new Incident($cachetInstance, $row);
                break;

            case 'metrics':
                return new Metric($cachetInstance, $row);
                break;

            case 'subscribers':
                return new Subscriber($cachetInstance, $row);
                break;

            default:
                throw new \Exception('Invalid Cachet element type specified.');
                break;
        }
    }

    public function create($cachetInstance, $type, $data)
    {
        $requestParameters = ['json' => $data, 'headers' => $cachetInstance->getAuthHeaders()];

        $response = $cachetInstance->guzzleClient->post($type, $requestParameters);

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

        $toReturn = null;

        $row = $data;

        switch ($type) {

            case 'components':
                $toReturn = new Component($cachetInstance, $row);
                break;

            case 'incidents':
                $toReturn = new Incident($cachetInstance, $row);
                break;

            case 'metrics':
                $toReturn = new Metric($cachetInstance, $row);
                break;

            case 'subscribers':
                $toReturn = new Subscriber($cachetInstance, $row);
                break;

            default:
                throw new \Exception('Invalid Cachet element type specified.');
                break;
            }

        return $toReturn;
    }
}
