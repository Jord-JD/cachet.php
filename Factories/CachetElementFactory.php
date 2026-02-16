<?php

namespace JordJD\CachetPHP\Factories;

use JordJD\CachetPHP\Objects\Component;
use JordJD\CachetPHP\Objects\Incident;
use JordJD\CachetPHP\Objects\Metric;
use JordJD\CachetPHP\Objects\Subscriber;

abstract class CachetElementFactory
{
    public static function getAll($cachetInstance, $type, $sort = null, $order = null, $authorisationRequired = false)
    {
        $requestParameters = ['query' => self::buildQueryParameters($sort, $order)];

        if ($authorisationRequired) {
            $requestParameters['headers'] = $cachetInstance->getAuthHeaders();
        }

        $data = self::decodeResponse($cachetInstance->guzzleClient->get($type, $requestParameters));
        $rows = self::extractRowsFromResponseData($data);

        $totalPages = self::extractTotalPagesFromResponseData($data);

        for ($currentPage = 2; $currentPage <= $totalPages; $currentPage++) {
            $requestParameters['query'] = self::buildQueryParameters($sort, $order, $currentPage);
            $pageData = self::decodeResponse($cachetInstance->guzzleClient->get($type, $requestParameters));
            $rows = array_merge($rows, self::extractRowsFromResponseData($pageData));
        }

        self::sortRowsLocally($rows, $sort, $order);

        return self::hydrateRows($cachetInstance, $type, $rows);
    }

    public static function getById($cachetInstance, $type, $id, $authorisationRequired = false)
    {
        $requestParameters = [];

        if ($authorisationRequired) {
            $requestParameters['headers'] = $cachetInstance->getAuthHeaders();
        }

        $data = self::decodeResponse($cachetInstance->guzzleClient->get($type.'/'.$id, $requestParameters));
        $row = self::extractRowFromResponseData($data);

        return self::hydrateRow($cachetInstance, $type, $row);
    }

    public static function create($cachetInstance, $type, $data)
    {
        $requestParameters = ['json' => $data, 'headers' => $cachetInstance->getAuthHeaders()];

        $responseData = self::decodeResponse($cachetInstance->guzzleClient->post($type, $requestParameters));
        $row = self::extractRowFromResponseData($responseData);

        return self::hydrateRow($cachetInstance, $type, $row);
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

    private static function decodeResponse($response)
    {
        if ($response->getStatusCode() != 200) {
            throw new \Exception('Bad response from Cachet instance.');
        }

        $data = json_decode((string) $response->getBody());

        if ($data === null) {
            throw new \Exception('Could not decode JSON retrieved from Cachet instance.');
        }

        return $data;
    }

    private static function extractRowsFromResponseData($data)
    {
        if (isset($data->data) && is_array($data->data)) {
            return $data->data;
        }

        if (is_array($data)) {
            return $data;
        }

        return [];
    }

    private static function extractRowFromResponseData($data)
    {
        if (isset($data->data)) {
            return $data->data;
        }

        return $data;
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

    private static function sortRowsLocally(&$rows, $sort = null, $order = null)
    {
        if ($sort === null || $order === null || !is_array($rows)) {
            return;
        }

        $normalisedOrder = strtolower((string) $order);
        if ($normalisedOrder !== 'asc' && $normalisedOrder !== 'desc') {
            return;
        }

        usort($rows, function ($left, $right) use ($sort, $normalisedOrder) {
            $leftValue = isset($left->$sort) ? $left->$sort : null;
            $rightValue = isset($right->$sort) ? $right->$sort : null;

            if ($leftValue == $rightValue) {
                return 0;
            }

            if ($leftValue === null) {
                return ($normalisedOrder === 'asc') ? 1 : -1;
            }

            if ($rightValue === null) {
                return ($normalisedOrder === 'asc') ? -1 : 1;
            }

            if (is_numeric($leftValue) && is_numeric($rightValue)) {
                $comparison = ($leftValue < $rightValue) ? -1 : 1;
            } else {
                $comparison = strcasecmp((string) $leftValue, (string) $rightValue);
                if ($comparison < 0) {
                    $comparison = -1;
                } elseif ($comparison > 0) {
                    $comparison = 1;
                }
            }

            return ($normalisedOrder === 'asc') ? $comparison : -$comparison;
        });
    }

    private static function hydrateRows($cachetInstance, $type, $rows)
    {
        $toReturn = [];

        foreach ($rows as $row) {
            $toReturn[] = self::hydrateRow($cachetInstance, $type, $row);
        }

        return $toReturn;
    }

    private static function hydrateRow($cachetInstance, $type, $row)
    {
        switch ($type) {

            case 'components':
                return new Component($cachetInstance, $row);

            case 'incidents':
                return new Incident($cachetInstance, $row);

            case 'metrics':
                return new Metric($cachetInstance, $row);

            case 'subscribers':
                return new Subscriber($cachetInstance, $row);

            default:
                throw new \Exception('Invalid Cachet element type specified.');
        }
    }
}
