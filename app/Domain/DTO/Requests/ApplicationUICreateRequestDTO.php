<?php

namespace App\Domain\DTO\Requests;

class ApplicationUICreateRequestDTO
{
    public function __construct(public readonly ApplicationUICreateProductDTO $productDTO,
                                public readonly ApplicationUICreateAddressDTO $addressDTO
    )
    {
    }
}