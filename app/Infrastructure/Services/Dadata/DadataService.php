<?php

namespace App\Infrastructure\Services\Dadata;

use App\Domain\DTO\DadataCleanAddressDTO;
use App\Infrastructure\DadataAdapter;
use App\Application\GeoServiceInterface;
use App\Infrastructure\Services\Dadata\Api\Api;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class DadataService implements GeoServiceInterface
{
    public function __construct(private readonly Api $apiClient)
    {}

    public function getSuggestions($address, $count = 1)
    {
        return $this->apiClient->suggestions($address, $count);
    }

    public function getCleanAddress(string $address): array|DadataCleanAddressDTO
    {
        return $this->apiClient->clean($address);
    }
}
