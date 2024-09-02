<?php

namespace App\Infrastructure\Services\Application\Factories;

use App\Domain\ProductDTO;

class ProductFactory
{
    public function makeProductDTO(array $data): ProductDTO
    {
        return new ProductDTO(
            name: $data['name'],
            brand: $data['brand'] ?? null,
            sku: $data['sku'],
            count: $data['count'] ?? 1,
            cost: floatval(str_replace(' ', '',str_replace(',', '.', $data['cost']))),
            vat: $data['vat'] ?? 20,
            width: floatval(str_replace(',', '.', $data['width'])),
            height: floatval(str_replace(',', '.', $data['height'])),
            depth: floatval(str_replace(',', '.', $data['depth'])),
            volume: floatval(str_replace(',', '.', $data['volume'])),
            weight: floatval(str_replace(',', '.', $data['weight'])),
            tnved: $data['tnved'] ?? null,
            discountCost: null,
            countryCode: $data['country_code'] ?? null,
            barcode: $data['barcode'] ?? null,
            leftToPay: $data['left_to_pay'] ?? 0
        );
    }
}