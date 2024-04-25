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
            $parameters[RequestOptions::HEADERS]['Authorization'] = 'Bearer ' . $token;
        }

        if (env('APP_ENV') != 'production') {
            $parameters[RequestOptions::HEADERS]['Authorization'] .= ', Basic ' . base64_encode('holodilnik:Fin7Dater-Gola');
        }

        try {
            $request = $this->client->request($method, $uri, $parameters);
            $content = $request->getBody()->getContents();
            $result = json_decode($content, true);

            // не разобрался, почему именно на PUT и DELETE не приходит json в ответе, но
            // цены создаются/обновляются/удаляются в сервисе
            // костыль
            if ($request->getStatusCode() === 204) {
                return [
                    'status' => true,
                    'message' => 'Prices created or updated.'
                ];
            }

            if (!is_array($result)) {
                return [];
            }
        } catch (ClientException $e) {
            Log::info('Request:' . json_encode($parameters));
            Log::error('Sending to delivery service error:' . $e->getResponse()->getBody()->getContents());
        } catch (GuzzleException $e) {
            Log::info('Request:' . json_encode($data));
            Log::error('Sending to delivery service error:' . $e->getMessage());
        }

        return $result;
    }
}