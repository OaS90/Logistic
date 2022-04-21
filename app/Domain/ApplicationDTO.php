<?php

namespace App\Domain;

class ApplicationDTO
{
    public function dbRowsFromCsv(): array
    {
        return [
            "Номер заказа" => 'order_number',
            "Тип оплаты" => 'payment_type',
            "Адрес склада" =>  'warehouse_address',
            "Комментарий" => 'comment',
            "Дата доставки" => 'delivery_date',
            "Время доствки с" => 'delivery_from',
            "Время доставки до" => 'delivery_till',
            "ФИО" => 'client_name',
            "Мобильный телефон" => 'client_phone',
            "Регион" => 'region_name',
            "Город" => 'city_name',
            "ФИАС города" => 'city_fias',
            "Улица" => 'street',
            "ФИАС улицы" => 'street_fias',
            "Дом" => 'building',
            "Этаж" => 'floor',
            "Квартира" => 'flat',
            "Товар" => 'name',
            "Артикул" => 'sku',
            "Количество" => 'count',
            "Цена" => 'cost',
            "Цена со скидкой" => 'discount_cost',
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
        return array_slice($allRows, 0, 9);
    }
}
