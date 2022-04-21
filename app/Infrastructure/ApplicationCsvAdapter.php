<?php

namespace App\Infrastructure;

class ApplicationCsvAdapter
{
    public function csvToArrayAppRows($data)
    {
        $rows = [
            "Номер заказа" => 'order_number',
            "Тип оплаты" => 'payment_type',
            "Комментарий" => 'comment',
            "Дата доставки" => 'delivery_date',
            "Время доствки с" => 'delivery_from',
            "Время доставки до" => 'delivery_till',
            "ФИО" => 'client_name',
            "Мобильный телефон" => 'mobile_phone',
        ];

//        return array_combine();
    }

    public function csvToArrayAddressRows($data)
    {

    }
}
