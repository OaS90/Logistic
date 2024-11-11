<?php

namespace App\Domain\DTO;

class ApplicationObiDTOFor1c
{
    /**
     * @param string $orderNumber
     * @param int $docVer
     * @param string $paymentMethod
     * @param string $comment
     * @param string $deliveryDate
     * @param string $deliveryTimeFrom
     * @param string $deliveryTimeTo
     * @param int $storeId
     * @param string $clientFullName
     * @param string $clientPhone
     * @param ProductObiDTOFor1c[] $products
     */
    public function __construct(public readonly string $orderNumber,
                                public readonly int $docVer,
                                public readonly string $paymentMethod,
                                public readonly string $comment,
                                public readonly string $deliveryDate,
                                public readonly string $deliveryTimeFrom,
                                public readonly string $deliveryTimeTo,
                                public readonly int $storeId,
                                public readonly string $clientFullName,
                                public readonly string $clientPhone,
                                public readonly array $products,
                                public readonly ?string $deliveryAddress = null
    )
    {
    }
}