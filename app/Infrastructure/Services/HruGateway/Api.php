<?php

namespace App\Infrastructure\Services\HruGateway;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class Api
{
    private const DELIVERY_URI = 'holodilnik-delivery/api/v1/';


    public function __construct(private readonly Client $client)
    {
    }

    public function tariffs(string $uri, array $data, ?string $token, string $method = 'POST')
    {
        $parameters = [];
        $result = [];
        $fullUri = self::DELIVERY_URI . $uri;

        if ($method != 'GET' && $method != 'DELETE') {
            $parameters[RequestOptions::JSON] = $data;
        } else {
            $parameters[RequestOptions::QUERY] = $data;
        }

        if ($token) {
            $parameters[RequestOptions::HEADERS]['Authorization'] = 'Bearer ' . $token;
        }

        if (env('APP_ENV') != 'production') {
            $user = config('app.hru_base_auth_user');
            $password = config('app.hru_base_auth_pass');
            $parameters[RequestOptions::HEADERS]['Authorization'] .= ', Basic ' . base64_encode($user . ':' . $password);
        }

        try {
            $request = $this->client->request($method, $fullUri, $parameters);
            $content = $request->getBody()->getContents();
            $result = json_decode($content, true);
            Log::info('response from Delivery Tariff service ' . $content);

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
