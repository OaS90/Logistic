<?php

namespace App\Domain\DTO;

class ProductObiDTO
{
    public function __construct(public readonly string $productInfo)
    {
    }
}