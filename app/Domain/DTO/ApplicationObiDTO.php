<?php

namespace App\Domain\DTO;

class ApplicationObiDTO
{
    /**
     * @param ProductObiDTO[] $orderList
     */
    public function __construct(public readonly string $deliveryDate,
                                public readonly string $deliveryTime,
                                public readonly string $orderNumber,
                                public readonly string $orderType,
                                public readonly string $clientName,
                                public readonly string $phones,
                                public readonly ?string $deliveryType,
                                public readonly string $deliveryZone,
                                public readonly array $orderList,
                                public readonly float $orderWeight,
                                public readonly ?string $deliveryAddress = null,
                                public readonly ?int $overDeliveryZoneKm = null,
                                public readonly ?string $liftType = null,
                                public readonly ?int $liftFloor = null,
                                public readonly ?float $liftWeightKg = null,
                                public readonly ?int $handLiftFloor = null,
                                public readonly ?float $handLiftWeightKg = null,
                                public readonly ?float $transferDistance = null,
                                public readonly ?float $transferWeight = null,
                                public readonly ?float $productsCost = null,
                                public readonly ?float $costOfTransportation = null,
                                public readonly ?float $liftCost = null,
                                public readonly ?float $transferCost = null,
                                public readonly ?float $totalDeliveryCost = null,
                                public readonly string $comment = ''
    )
    {
    }
}
