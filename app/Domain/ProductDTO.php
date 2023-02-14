<?php

namespace App\Domain;

class ProductDTO
{
    public function dbRows(array $allRows): array
    {
        $data = [];
        $data['name'] = $allRows['name'];
        $data['sku'] = $allRows['sku'];
        $data['count'] = $allRows['count'];
        $data['cost'] = floatval(str_replace(' ', '', $allRows['cost']));
        $data['vat'] = $allRows['vat'];
        $data['width'] = $allRows['width'];
        $data['height'] = $allRows['height'];
        $data['depth'] = $allRows['depth'];
        $data['brand'] = $allRows['brand'] ?? null; // Бренд
        $data['tnved'] = $allRows['tnved'] ?? null; // Код ТНВЭД
        $data['country'] = $allRows['country_code'] ?? null; // код страны происхождения по ОКСМ
        $data['barcode'] = $allRows['barcode'] ?? null; // EAN
        $data['volume'] = floatval(str_replace(' ', '', $allRows['volume']));
        $data['weight'] = $allRows['weight'];

        return $data;
    }

    public function toArray(int $appId, array $data): array
    {
        return [
            'app_id' => $appId,
            'name' => $data['name'], // Товар
            'sku' => $data['sku'], // Артикул
            'count' => $data['count'], // Количество
            'cost' => floatval(str_replace(' ', '', $data['cost'])), // Оценочная стоимость
            //'discount_cost' => floatval(str_replace(' ', '', $data['discount_cost'])), // Стоимость с учетом скидки
            'vat' => $data['vat'], // Ставка НДС
            //'leftToPay' => $data['left_to_pay'], // Сумма к получению
            'weight' => $data['weight'], // Расчетный вес (кг)
            'brand' => $data['brand'] ?? null, // Бренд
            'tnved' => $data['tnved'] ?? null, // Код ТНВЭД
            'country' => $data['country_code'] ?? null, // код страны происхождения по ОКСМ
            'barcode' => $data['barcode'] ?? null, // EAN
            'volume' => floatval(str_replace(' ', '', $data['volume'])), // объем в м2
            'width' => $data['width'], // ширина в см
            'height' => $data['height'], // высота в м2
            'depth' => $data['depth'], // глубина в см
        ];
    }

    public function apiRows(array $data, int $appId): array
    {
        $data['discount_cost'] = $data['costAfterDiscounts'] ?? null;
        $data['vat'] = $data['VATRate'];
        $data['left_to_pay'] = $data['leftToPay'];
        $data['country_code'] = $data['country'] ?? null;
        $data['app_id'] = $appId;
        // TODO потом убрать, когда протестируем
        $data['width'] = !$data['width'] ? rand(1, 5) : $data['width'];
        $data['height'] = !$data['height'] ? rand(1, 5) : $data['height'];
        $data['depth'] = !$data['depth'] ? rand(1, 5) : $data['depth'];

        unset($data['costAfterDiscounts']);
        unset($data['VATRate']);
        unset($data['leftToPay']);
        unset($data['country']);

        return $data;
    }
}
