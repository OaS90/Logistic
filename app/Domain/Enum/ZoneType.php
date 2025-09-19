<?php

namespace App\Domain\Enum;

enum ZoneType
{
    /**
     * Допустимая зона доставки
     */
    const ALLOW_ZONES = 'allow-zones';

    /**
     * Зона развязок
     */
    const POLYGON_PATHS = 'polygon-paths';

    /**
     * Зона доставки
     */
    const DELIVERY_ZONES = 'delivery-zones';

    const ALL = [
        self::ALLOW_ZONES,
        self::POLYGON_PATHS,
        self::DELIVERY_ZONES
    ];
}
