<?php

namespace App\Infrastructure\Services\Dadata\Api;

use App\Domain\DTO\DadataCleanAddressDTO;
use App\Domain\DTO\DadataSuggestionAddressDTO;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class Api
{
    public function __construct(private readonly Client $suggestClient, private readonly Client $cleanClient)
    {
    }

    public function suggestions(string $address, int $count = 1): array
    {
        $result = [];

        try {
            $response = $this->suggestClient->post('suggest/address', [
                RequestOptions::JSON => [
                    'query' => $address,
                    'count' => $count
                ],

            ]);
            $content = $response->getBody()->getContents();
            $data = json_decode($content, true);

            if (isset($data['suggestions']) && count($data['suggestions']) > 0) {
                $result = $data['suggestions'];
            }

        } catch (\Throwable $e) {
            Log::error('Failed to get suggestions: ' . $e->getMessage());
        }

        return $result;
    }

    public function clean(string $address): array|DadataCleanAddressDTO
    {
        $result = [];

        try {
            $response = $this->cleanClient->post('clean/address', [
                RequestOptions::JSON => [$address]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            $result = new DadataCleanAddressDTO(
                regionFias: $data[0]['region_fias_id'],
                region: $data[0]['region_with_type'],
                cityFias: $data[0]['city_fias_id'],
                city: $data[0]['city_with_type'],
                streetFias: $data[0]['street_fias_id'],
                street: $data[0]['street_with_type'],
                house: $data[0]['house_type_full'],
                block: $data[0]['block_type_full'],
                entrance: $data[0]['entrance'],
                floor: $data[0]['floor'],
                flat: $data[0]['flat_type_full']
            );
        } catch (\Throwable $e) {
            Log::error('Failed to get clean address: ' . $e->getMessage());
        }

        return $result;
    }
}
