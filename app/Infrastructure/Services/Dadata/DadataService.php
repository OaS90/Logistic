<?php

namespace App\Infrastructure\Services\Dadata;

use App\Domain\DTO\DadataCleanAddressDTO;
use App\Infrastructure\DadataAdapter;
use App\Application\GeoServiceInterface;
use App\Infrastructure\Services\Dadata\Api\Api;
use GuzzleHttp\Exception\ClientException;

class DadataService implements GeoServiceInterface
{
    public function __construct(private readonly Api $apiClient)
    {}

    public function getSuggestions($address, $count = 1)
    {
        try {
            $response = $this->apiClient->suggestions($address, $count);
        } catch (ClientException $e) {

        } catch (\Throwable $e) {
            dd($e->getMessage());
        }

        return (new DadataAdapter())->getAddress($address, $count);
    }

    public function getCleanAddress(string $address): array|DadataCleanAddressDTO
    {
        return $this->apiClient->clean($address);
    }
}
