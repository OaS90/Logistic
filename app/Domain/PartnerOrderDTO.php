<?php

namespace App\Domain;

class PartnerOrderDTO
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function make(): array
    {
        return [
            'id' => $this->data['id'],
            'paymentMethod' => $this->data['payment_type'],
            'comment' => $this->data['comment'],
            'deliveryDate' => $this->data['delivery_date'],
            'deliveryTimeFrom' => $this->deliveryTime($this->data['delivery_time'])[0],
            'deliveryTimeTo' => $this->deliveryTime($this->data['delivery_time'])[1],
            'buyer' => [
                'fio' => $this->data['client_name'],
                'phone' => $this->data['client_phone']
            ],
            'address' => [
                $this->data['delivery_address']
            ],
            'products' => [
                [
                    'name' => $this->data['product_name'], // Товар
                    'vendorCode' => $this->data['product_art'], // Артикул
                    'count' => $this->data['count'], // Количество
                    'cost' => 10000, // Оценочная стоимость
                    'costAfterDiscounts' => 10000, // Стоимость с учетом скидки
                    'VATRate' => $this->data['vat'], // Ставка НДС
                    'leftToPay' => 0, // Сумма к получению
                    'weight' => $this->data['weight'], // Расчетный вес (кг)
                    'setId' => '54654_1',
                    'brand' => $this->data['product_brand'], // Бренд
                    'tnved' => '8516609000', // Код ТНВЭД
                    'country' => '643', // код страны происхождения по ОКСМ
                    'barcode' => '8699272141521', // EAN
                    'volume' => $this->data['volume'], // объем в м2
                    'width' => $this->data['width'], // ширина в см
                    'height' => $this->data['height'], // высота в м2
                    'depth' => $this->data['depth'] // глубина в см
                ]
            ]
        ];
    }

    public function deliveryTime($time)
    {
        return explode('-', $time);
    }

    public function products()
    {

    }
}
