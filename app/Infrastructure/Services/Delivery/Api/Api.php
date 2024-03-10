<?php

namespace App\Infrastructure\Services\Delivery\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class Api
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function query(string $uri, array $data = [], $method = 'POST', string $token = ''): array
    {
        $parameters = [];
        $result = [];

        if ($method != 'GET' && $method != 'DELETE') {
            $parameters[RequestOptions::JSON] = $data;
        } else {
            $parameters[RequestOptions::QUERY] = $data;
        }

        if ($token) {
            $parameters[RequestOptions::HEADERS] = [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ];
        }

        try {
            $request = $this->client->request($method, $uri, $parameters);
            $content = $request->getBody()->getContents();
            $result = json_decode($content, true);
        } catch (ClientException $e) {
            Log::error('Sending to delivery service error:' . $e->getResponse()->getBody()->getContents());
        } catch (GuzzleException $e) {
            Log::error('Sending to delivery service error:' . $e->getMessage());
        }

        return $result;
    }
}