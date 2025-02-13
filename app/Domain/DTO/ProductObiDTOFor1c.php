<?php

namespace App\Domain\DTO;

class ProductObiDTOFor1c
{
    public function __construct(public readonly string $name,
                                public readonly string $vendorCode,
                                public readonly int $count,
                                public readonly float $cost,
                                public readonly float $costAfterDiscounts, // Стоимость с учетом скидки
                                public readonly int $vatRate, // Ставка НДС
                                public readonly int $leftToPay, // Сумма к получению
                                public readonly float $weight, // Расчетный вес (кг)
                                public readonly string $setId,
                                public readonly string $brand, // Бренд
                                public readonly string $tnved, // Код ТНВЭД
                                public readonly string $country, // код страны происхождения по ОКСМ
                                public readonly string $barcode, // EAN
                                public readonly int $volume, // объем в м2
                                public readonly int $width, // ширина в см
                                public readonly int $height, // высота в м2
                                public readonly int $depth, // глубина в см
                                public readonly string $shipmentCode
    )
    {
    }
}