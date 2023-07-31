<?php

namespace App\Domain;

use Illuminate\Support\Carbon;

class ApplicationDTO
{
    public function dbRowsFromCsv(): array
    {
        return [
            "Номер заказа" => 'order_number',
            "Адрес склада" => 'store_id',
            "Регион" => 'region_name',
            "Город" => 'city_name',
            "ФИАС города" => 'city_fias',
            "Улица" => 'street',
            "ФИАС улицы" => 'street_fias',
            "Дом" => 'building',
            "Этаж" => 'floor',
            "Квартира" => 'flat',
            "ФИО" => 'client_name',
            "Мобильный телефон" => 'client_phone',
            "Комментарий" => 'comment',
            "Дата доставки" => 'delivery_date',
            "Время доствки с" => 'delivery_from',
            "Время доставки до" => 'delivery_till',
            "Товар" => 'name',
            "Артикул" => 'sku',
            "Количество" => 'count',
            "Тип оплаты" => 'payment_type',
            "Стоимость ед. товара" => 'cost',
//            "Цена со скидкой" => 'discount_cost',
            "Стоимость доставки" => 'delivery_cost',
            "НДС" => 'vat',
            "Сумма к получению" => 'left_to_pay',
            "Вес" => 'weight',
            "Бренд" => 'brand',
            "ТНВЭД" => 'tnved',
            "Страна производитель" => 'country_code',
            "Баркод" => 'barcode',
            "Объём" => 'volume',
            "Длина" => 'width',
            "Высота" => 'height',
            "Глубина" => 'depth',
        ];
    }

    public function dbRows(array $allRows): array
    {
        $data = [];

        $data['client_name'] = $allRows['client_name'];
        $data['order_number'] = $allRows['order_number'];
        $data['payment_type'] = $allRows['payment_type'];
        $data['delivery_date'] = $allRows['delivery_date'];
        $data['delivery_cost'] = $allRows['delivery_cost'] ?? 0;
        $data['delivery_from'] = $allRows['delivery_from'];
        $data['delivery_till'] = $allRows['delivery_till'];
        $data['comment'] = $allRows['comment'];
        $data['store_address'] = $allRows['store_id'];
        $data['client_phone'] = parse_phone($allRows['client_phone']);

        return $data;
    }

    public function apiRows(array $data, int $addressId): array
    {
        $data['client_phone'] = parse_phone($data['buyer']['phone']);
        $data['client_name'] = $data['buyer']['fio'];
        $data['order_number'] = $data['id'];
        $data['delivery_time'] = $data['deliveryTimeFrom'] . '-' . $data['deliveryTimeTo'];
        $data['delivery_date'] = Carbon::parse($data['deliveryDate'])->format('Y-m-d');
        $data['payment_type'] = $data['paymentMethod'];
        $data['delivery_address'] = $addressId;
        $data['warehouse_id'] = $data['storeId'];
        unset($data['products']);
        unset($data['address']);
        unset($data['buyer']);
        unset($data['id']);

        return $data;
    }
}
