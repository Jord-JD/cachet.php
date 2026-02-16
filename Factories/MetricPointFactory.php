<?php

namespace JordJD\CachetPHP\Factories;

use JordJD\CachetPHP\Objects\MetricPoint;

abstract class MetricPointFactory
{
    public static function getAll($cachetInstance, $metric, $sort = null, $order = null)
    {
        $requestParameters = ['query' => self::buildQueryParameters($sort, $order)];
        $response = $cachetInstance->guzzleClient->get('metrics/'.$metric->id.'/points', $requestParameters);

        if ($response->getStatusCode() != 200) {
            throw new \Exception('Bad response from Cachet instance.');
        }

        $data = json_decode((string) $response->getBody());

        if ($data === null) {
            throw new \Exception('Could not decode JSON retrieved from Cachet instance.');
        }

        $rows = (isset($data->data) && is_array($data->data)) ? $data->data : [];
        $totalPages = self::extractTotalPagesFromResponseData($data);

        for ($currentPage = 2; $currentPage <= $totalPages; $currentPage++) {
            $requestParameters['query'] = self::buildQueryParameters($sort, $order, $currentPage);
            $pageResponse = $cachetInstance->guzzleClient->get('metrics/'.$metric->id.'/points', $requestParameters);

            if ($pageResponse->getStatusCode() != 200) {
                throw new \Exception('Bad response from Cachet instance.');
            }

            $pageData = json_decode((string) $pageResponse->getBody());
            if ($pageData === null) {
                throw new \Exception('Could not decode JSON retrieved from Cachet instance.');
            }

            if (isset($pageData->data) && is_array($pageData->data)) {
                $rows = array_merge($rows, $pageData->data);
            }
        }

        $toReturn = [];

        foreach ($rows as $row) {
            $toReturn[] = new MetricPoint($cachetInstance, $metric, $row);
        }

        return $toReturn;
    }

    public static function create($cachetInstance, $metric, $data)
    {
        $response = $cachetInstance->guzzleClient->post('metrics/'.$metric->id.'/points', ['json' => $data, 'headers' => $cachetInstance->getAuthHeaders()]);

        if ($response->getStatusCode() != 200) {
            throw new \Exception('Bad response from Cachet instance.');
        }

        $responseData = json_decode((string) $response->getBody());
        if ($responseData === null) {
            throw new \Exception('Could not decode JSON retrieved from Cachet instance.');
        }

        if (isset($responseData->data)) {
            $responseData = $responseData->data;
        }

        return new MetricPoint($cachetInstance, $metric, $responseData);
    }

    private static function buildQueryParameters($sort = null, $order = null, $page = null)
    {
        $queryParameters = [];

        if ($sort !== null) {
            $queryParameters['sort'] = $sort;
        }

        if ($order !== null) {
            $queryParameters['order'] = $order;
        }

        if ($page !== null) {
            $queryParameters['page'] = $page;
        }

        return $queryParameters;
    }

    private static function extractTotalPagesFromResponseData($data)
    {
        if (isset($data->meta)
            && isset($data->meta->pagination)
            && isset($data->meta->pagination->total_pages)) {
            return max(1, (int) $data->meta->pagination->total_pages);
        }

        return 1;
    }
}
