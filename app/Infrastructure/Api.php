<?php

namespace App\Infrastructure;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Api
{
    protected $client;
    protected $url;
    protected $headers;

    public function __construct($url, $headers = [])
    {
        $this->client = new Client();
        $this->url = $url;
        $this->headers = ['Content-Type' => 'application/json', 'Accept' => 'application/json'];
    }

    public function query(string $uri, array $data, string $method = 'GET')
    {
        $requestData = [];
        $responseData = false;
        $url = $this->url . $uri;

        if ($method == 'GET') {
            $requestData['query'] = $data;
        } else {
            $requestData['json'] = $data;
        }

        if ($this->headers)
            $requestData['headers'] = $this->headers;

        $requestData['auth'] = [config('app.api_user'), config('app.api_password')];

        try {
            $response = $this->client->request($method, $url, $requestData);

            if ($response->getStatusCode() == 200)
                $responseData = json_decode($response->getBody()->getContents(), true);

        } catch (\Throwable $e) {
            Log::info('Error from sending to api: ' . $e->getMessage());
        }

        return $responseData;
    }

}
