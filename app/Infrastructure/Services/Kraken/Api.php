<?php

namespace App\Infrastructure\Services\Kraken;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class Api
{
    public const CONFIG_UPDATE_QUOTES_URI = 'config/update-interval-quotas-config';
    public const CONFIG_UPDATE_TC_QUOTES_URI = 'config/update-transport-companies-interval-quotas-config';
    public const MONOLITH_UPDATE_TC_QUOTES_URI = 'config-old/update-transport-companies-interval-quotas-config';
    public const MONOLITH_UPDATE_QUOTES_URI = 'config-old/update-interval-quotas-config';
    public const DELIVERY_TARIFFS_URI = 'settings/calculation/courier-delivery-price-tariffs';
    public const DELIVERY_COURIER_PRICES_URI = 'settings/calculation/group/courier-delivery-prices';


    public function __construct(private readonly Client $client)
    {
    }

    public function monolithRequest(string $uri, array $data = [], string $method = 'GET'): array
    {
        $result = [];
        $params[RequestOptions::HEADERS]['Authorization'] = config('services.monolith.token');

        Log::info('Send to monolith. Request: ' . json_encode($data) . ' uri: ' . $uri);

        if ($method !== 'GET') {
            $params[RequestOptions::JSON] = $data;
        } else {
            $params[RequestOptions::QUERY] = $data;
        }

        try {
            $response = $this->client->request($method, $uri, $params);
            $result = json_decode($response->getBody()->getContents(), true);

            Log::info('Monolith response: ' . $response->getBody()->getContents() . ' code: ' . $response->getStatusCode());

            if ($response->getStatusCode() == 200 && (isset($result['success']) && $result['success'])) {
                $result['status'] = true;
            } else {
                Log::error('Send to monolith failed: ' . $result['error'] ?? 'Ошибка получения данных из монолита.');
                $result = [
                    'status' => false,
                    'message' => $result['error'] ?? 'Ошибка получения данных из монолита.'
                ];
            }
        } catch (ClientException $e) {
            Log::error('Send to monolith error: ' . $e->getResponse()->getBody()->getContents());
        } catch (GuzzleException $e) {
            Log::error('Send to monolith error: ' . $e->getMessage());
        }

        return $result;
    }

    public function configServiceRequest(string $uri, array $data = [], string $method = 'GET'): array
    {
        $result = [];
        $params[RequestOptions::HEADERS]['X-Config-Service-Token'] = config('services.config_service.token');

        if ($method !== 'GET') {
            $params[RequestOptions::JSON] = ['data' => $data];
        } else {
            $params[RequestOptions::QUERY] = $data;
        }

        Log::info('Send to config service. Request: ' . json_encode($data) . ' uri: ' . $uri);

        try {
            $response = $this->client->request($method, $uri, $params);
            $result = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() === 204) {
                $result = ['status' => true];
            }

            Log::info('Config service response: ' . $response->getBody()->getContents() . ' code: ' . $response->getStatusCode());
        } catch (ClientException $e) {
            Log::error('Send to config service response error: ' . $e->getResponse()->getBody()->getContents());
        } catch (GuzzleException $e) {
            Log::error('Send to config service response error: ' . $e->getMessage());
        }

        return $result;
    }

    public function deliveryServiceRequest(string $uri, array $data = [], $method = 'GET'): array
    {
        $result = [];
        $params[RequestOptions::HEADERS]['Authorization'] = 'Bearer ' . config('services.delivery_service.token');
        $uri = 'holodilnik-delivery/api/v1/' . $uri;

        if ($method !== 'GET') {
            $params[RequestOptions::JSON] = $data;
        } else {
            $params[RequestOptions::QUERY] = $data;
        }

        Log::withContext(['uri' => $uri,])->info('Send to delivery service. Request: ' . json_encode($data));

        try {
            $response = $this->client->request($method, $uri, $params);
            $contents = $response->getBody()->getContents();

            Log::info('Delivery service response: ' . $contents);

            if ($response->getStatusCode() === 204 || $response->getStatusCode() == 200) {
                if (in_array($uri, [self::DELIVERY_TARIFFS_URI, self::DELIVERY_COURIER_PRICES_URI])) {
                    $result = [
                        'status' => true,
                        'message' => 'Prices created or updated.'
                    ];
                } else {
                    $result = json_decode($contents, true);
                }
            }
        } catch (ClientException $e) {
            Log::withContext([
                'uri' => $uri,
                'headers' => $e->getResponse()->getHeaders(),
                'status' => $e->getResponse()->getStatusCode()
            ])->error('Send to delivery service response error: ' . $e->getResponse()->getBody()->getContents());
        } catch (GuzzleException $e) {
            Log::withContext([
                'uri' => $uri,
                'status' => $e->getCode()
            ])->error('Send to delivery service response error: ' . $e->getMessage());
        }

        return $result;
    }
}
