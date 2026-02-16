<?php

namespace JordJD\CachetPHP\Factories;

use JordJD\CachetPHP\Objects\IncidentUpdate;
abstract class IncidentUpdateFactory
{
    public static function getAll($cachetInstance, $incident, $sort = null, $order = null)
    {
        $requestParameters = ['query' => self::buildQueryParameters($sort, $order)];
        $response = $cachetInstance->guzzleClient->get('incidents/'.$incident->id.'/updates', $requestParameters);

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
            $pageResponse = $cachetInstance->guzzleClient->get('incidents/'.$incident->id.'/updates', $requestParameters);

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
            $toReturn[] = new IncidentUpdate($cachetInstance, $row);
        }

        return $toReturn;
    }

    public static function create($cachetInstance, $incident, $data)
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

        if ($newComponentStatus !== null) {
            $incident->component_status = $newComponentStatus;
            $incident->save();
        }

        return new IncidentUpdate($cachetInstance, $data);
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
