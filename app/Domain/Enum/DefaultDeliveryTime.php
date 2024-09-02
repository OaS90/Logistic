<?php

namespace App\Domain\Enum;

enum DefaultDeliveryTime: string
{
    case FROM = '10:00';
    case TO = '18:00';
}
