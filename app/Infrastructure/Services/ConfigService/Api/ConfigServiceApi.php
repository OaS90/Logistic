<?php

namespace App\Infrastructure\Services\ConfigService\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class ConfigServiceApi
{
    public function __construct(private readonly Client $client)
    {}

    public function query(string $uri, array $data, $method = 'GET'): bool
    {
        if ($method == 'GET') {
            $params = [RequestOptions::QUERY => $data];
        } else {
            $params = [RequestOptions::JSON => ['data' => $data]];
        }

        try {
            $response = $this->client->request($method, $uri, $params);

            if ($response->getStatusCode() === 204) {
                return true;
            }
        } catch (GuzzleException $e) {
            Log::error('Send to config service error: ' . $e->getMessage());
        }

        return false;
    }
}