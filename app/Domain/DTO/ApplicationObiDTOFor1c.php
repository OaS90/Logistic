<?php

namespace App\Domain\DTO;

class ApplicationObiDTOFor1c
{
    /**
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
                                public readonly DadataCleanAddressDTO $deliveryAddress
    )
    {
    }
}
