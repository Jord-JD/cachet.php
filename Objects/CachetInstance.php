<?php

namespace JordJD\CachetPHP\Objects;

use JordJD\CachetPHP\Factories\ComponentFactory;
use JordJD\CachetPHP\Factories\IncidentFactory;
use JordJD\CachetPHP\Factories\MetricFactory;
use JordJD\CachetPHP\Factories\SubscriberFactory;
use GuzzleHttp\Client;

class CachetInstance
{
    public $guzzleClient;

    private $baseUrl;
    private $apiToken;

    public function __construct($baseUrl, $apiToken)
    {
        if (!$baseUrl) {
            throw new \Exception('You must specify the base URL for your Cachet instance.');
        }

        $this->baseUrl = $baseUrl;
        $this->apiToken = $apiToken;

        $this->guzzleClient = new Client([
            'base_uri' => $baseUrl,
            'timeout'  => 3.0,
        ]);
    }

    public function getApiToken()
    {
        return $this->apiToken;
    }

    public function getAuthHeaders()
    {
        $authHeaderKey = 'X-Cachet-Token';
        $authHeaderValue = $this->getApiToken();

        return [
                    $authHeaderKey => $authHeaderValue,
                ];
    }

    public function ping()
    {
        $response = $this->guzzleClient->get('ping');

        if ($response->getStatusCode() != 200) {
            throw new \Exception('cachet.php: Bad response.');
        }

        $data = json_decode($response->getBody());

        if (!$data) {
            throw new \Exception('cachet.php: Could not decode JSON from '.$url);
        }

        if (isset($data->data)) {
            $data = $data->data;
        }

        return $data;
    }

    public function isWorking()
    {
        return ($this->ping() == 'Pong!');
    }

    public function getAllComponents($sort = null, $order = null)
    {
        return ComponentFactory::getAll($this, $sort, $order);
    }

    public function getAllIncidents($sort = null, $order = null)
    {
        return IncidentFactory::getAll($this, $sort, $order);
    }

    public function getAllMetrics($sort = null, $order = null)
    {
        return MetricFactory::getAll($this, $sort, $order);
    }

    public function getAllSubscribers($sort = null, $order = null)
    {
        return SubscriberFactory::getAll($this, $sort, $order);
    }

    public function getComponentById($id)
    {
        return ComponentFactory::getById($this, $id);
    }

    public function getIncidentById($id)
    {
        return IncidentFactory::getById($this, $id);
    }

    public function getMetricById($id)
    {
        return MetricFactory::getById($this, $id);
    }

    public function getSubscriberById($id)
    {
        return SubscriberFactory::getById($this, $id);
    }

    public function createComponent($data)
    {
        return ComponentFactory::create($this, $data);
    }

    public function createIncident($data)
    {
        return IncidentFactory::create($this, $data);
    }

    public function createMetric($data)
    {
        return MetricFactory::create($this, $data);
    }

    public function createSubscriber($data)
    {
        return SubscriberFactory::create($this, $data);
    }
}
