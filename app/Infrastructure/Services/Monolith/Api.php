<?php

namespace App\Infrastructure\Services\Monolith;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class Api
{
    private const INTERVALS_URI = 'vtb/delivery_proxy.php?q=Holodilnik/SetIntervalSetting';
    private const DELIVERY_URI = 'delivery/';
    private const TK_QUOTES_URI = 'delivery/Holodilnik/SetQuoteTkSettings';

    public function __construct(private readonly Client $client)
    {
    }

    public function sendQuotes(array $data)
    {
        $parameters[RequestOptions::JSON] = $data;

        if (app()->environment() !== 'production') {
            $parameters[RequestOptions::AUTH] =[
                config('app.api_user'), config('app.api_password')
            ];
        }

        try {
            $response = $this->client->post(self::INTERVALS_URI, $parameters);
            $content = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() == 200 && (isset($content['success']) && $content['success'])) {
                Log::info('Response: ' .
                    $response->getBody()->getContents() .
                    ', code:' . $response->getStatusCode() .
                    ' headers: ' . json_encode($response->getHeaders()) .
                    ', request: ' . json_encode($data)
                );

                return true;
            }

        } catch (ClientException $e) {
            Log::info('Request:' . json_encode($data));
            Log::error('Sending to delivery service error:' . $e->getResponse()->getBody()->getContents());
        } catch (GuzzleException $e) {
            Log::info('Request:' . json_encode($data));
            Log::error('Sending to delivery service error:' . $e->getMessage());
        }

        return false;
    }

    public function getDeliveryDate(string $regionWithCity): string|bool
    {
        try {
            $response = $this->client->get(self::DELIVERY_URI, [RequestOptions::QUERY => ['q' => 'DeliveryDateBortUdachi', 'address' => $regionWithCity]]);
            $content = json_decode($response->getBody()->getContents(), true);

            if ($content && isset($content['date'])) {
                return $content['date'];
            }

        } catch (ClientException $e) {
            Log::info('Request:' . json_encode($regionWithCity));
            Log::error('Sending to delivery service error:' . $e->getResponse()->getBody()->getContents());
        } catch (GuzzleException $e) {
            Log::info('Request:' . json_encode($regionWithCity));
            Log::error('Sending to delivery service error:' . $e->getMessage());
        }

        return false;
    }

    public function sendTkQuotes(array $data): array
    {
        try {
            $response = $this->client->post(self::TK_QUOTES_URI, [RequestOptions::JSON => $data]);
            $content = json_decode($response->getBody()->getContents(), true);

            Log::info('Response: ' .
                $response->getBody()->getContents() .
                ', code:' . $response->getStatusCode() .
                ' headers: ' . json_encode($response->getHeaders())
            );

            if ($response->getStatusCode() == 200 &&
                (isset($content['success']) && $content['success'])
            ) {
                return ['status' => true];
            } else {
                return [
                    'status' => false,
                    'message' => $content['error'] ?? 'Ошибка получения данных из монолита.'
                ];
            }
        } catch (ClientException $e) {
            Log::error('Ошибка отправки данных в Tk квот в монолит. ' . ' Error: ' .
                $e->getResponse()->getBody()->getContents());
        } catch (GuzzleException $e) {
            Log::error('Ошибка отправки данных в Tk квот в монолит.' . ' Error: ' .
                $e->getMessage());
        }

        return [];
    }
}
