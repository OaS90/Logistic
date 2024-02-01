<?php

namespace App\Infrastructure\Admin\Services\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class HruApi
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function query(string $uri, array $data, string $method = 'POST'): bool|array
    {
        if ($method == 'POST') {
            $params = [RequestOptions::JSON => $data];
        } else {
            $params = [RequestOptions::QUERY => $data];
        }

        try {
            $response = $this->client->request($method, $uri, $params);
            $responseContents = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() == 200 &&
                (isset($responseContents['success']) && $responseContents['success'])
            ) {
                return ['status' => true];
            } else {
                return ['status' => false, 'message' => $responseContents['error']];
            }
        } catch (ClientException $e) {
            Log::error('Ошибка отправки данных в HRU. URI: ' . $uri . ' Error: ' .
                $e->getResponse()->getBody()->getContents());
        } catch (GuzzleException $e) {
            Log::error('Ошибка отправки данных в HRU. URI: ' . $uri . ' Error: ' .
                $e->getMessage());
        }

        return false;
    }
}