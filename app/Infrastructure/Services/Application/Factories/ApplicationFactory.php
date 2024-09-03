<?php

namespace App\Infrastructure\Services\Application\Factories;

use App\Domain\DTO\ApplicationDTO;
use App\Domain\DTO\ApplicationObiDTO;
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

    public function makeObiApplicationDTO(array $data): ApplicationObiDTO
    {
        return new ApplicationObiDTO(
            deliveryDate: $data['deliveryDate'],
            deliveryTime: $data['deliveryTime'],
            orderNumber: $data['orderNumber'],
            orderType: $data['orderType'],
            clientName: $data['clientName'],
            phones: $data['phones'],
            deliveryType: $data['deliveryType'],
            deliveryZone: $data['deliveryZone'],
            orderList: $data['orderList'],
            orderWeight: $data['orderWeight'],
            deliveryAddress: $data['deliveryAddress'],
            overDeliveryZoneKm: $data['overDeliveryZoneKm'] ?? null,
            liftType: $data['liftType'] ?? null,
            liftFloor: $data['liftFloor'] ?? null,
            handLiftFloor: $data['handLiftFloor'] ?? null,
            handLiftWeightKg: $data['handLiftWeightKg'] ?? null,
            transferDistance: $data['transferDistance'] ?? null,
            transferWeight: $data['transferWeight'] ?? null,
            productsCost: $data['productsCost'] ?? null,
            costOfTransportation: $data['costOfTransportation'] ?? null,
            liftCost: $data['liftCost'] ?? null,
            transferCost: $data['transferCost'] ?? null,
            totalDeliveryCost: $data['totalDeliveryCost'] ?? null,
            comment: $data['comment']
        );
    }
}