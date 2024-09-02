<?php

namespace App\Infrastructure\Services\Application\Factories;

use App\Application\DeliveryAddressDTOInterface;
use App\Domain\DTO\ApplicationDTO;
use App\Domain\DTO\DeliveryAddressDTO;

class ApplicationFactory
{
    public function makeApplicationDTO(array $data,
                                       DeliveryAddressDTO $deliveryAddressDTO,
                                       array $products
    ): ApplicationDTO
    {
        return new ApplicationDTO(
            addressDTO: $deliveryAddressDTO,
            products: $products,
            orderNumber: $data['orderNumber'],
            paymentType: $data['paymentType'],
            deliveryTime: $data['deliveryFrom'] . '-' . $data['deliveryTill'],
            deliveryDate: $data['deliveryDate'],
            clientFullName: $data['clientName'],
            clientPhone: $data['clientPhone'],
            comment: $data['comment'] ?? null,
            storeAddress: $data['storeAddress'],
            deliveryCost: $data['deliveryCost'] ?? 0
        );
    }
}