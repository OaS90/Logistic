<?php

namespace App\Domain;

class ProductDTO
{
    public function dbRows(array $allRows): array
    {
        return array_slice($allRows, 17);
    }

    public function toArray(int $appId, array $data): array
    {
        return [
            'app_id' => $appId,
            'name' => $data['name'], // Товар
            'sku' => $data['sku'], // Артикул
            'count' => $data['count'], // Количество
            'cost' => floatval(str_replace(' ', '', $data['cost'])), // Оценочная стоимость
            'discount_cost' => floatval(str_replace(' ', '', $data['discount_cost'])), // Стоимость с учетом скидки
            'vat' => $data['vat'], // Ставка НДС
            'leftToPay' => $data['left_to_pay'], // Сумма к получению
            'weight' => $data['weight'], // Расчетный вес (кг)
            'brand' => $data['brand'], // Бренд
            'tnved' => $data['tnved'], // Код ТНВЭД
            'country' => $data['country_code'], // код страны происхождения по ОКСМ
            'barcode' => $data['barcode'], // EAN
            'volume' => floatval(str_replace(' ', '', $data['volume'])), // объем в м2
            'width' => $data['width'], // ширина в см
            'height' => $data['height'], // высота в м2
            'depth' => $data['depth'], // глубина в см
        ];
    }

    public function apiRows(array $data, int $appId): array
    {
        $data['discount_cost'] = $data['costAfterDiscounts'];
        $data['vat'] = $data['VATRate'];
        $data['left_to_pay'] = $data['leftToPay'];
        $data['country_code'] = $data['country'];
        $data['app_id'] = $appId;

        unset($data['costAfterDiscounts']);
        unset($data['VATRate']);
        unset($data['leftToPay']);
        unset($data['country']);

        return $data;
    }
}
