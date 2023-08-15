<?php

namespace App\Domain;

class ApplicationObiDTO
{
    public function dbRowsFromXlsx(): array
    {
        return [
            "Дата доставки" => 'delivery_date',
            "Время доставки" => 'delivery_time',
            "№ заявки на доставку" => 'order_number',
            "№ поручения ОБИ" => 'order_type',
            "Клиент" => 'client_name',
            "Телефон" => 'phone',
            "Адрес доставки" => 'delivery_address',
            "Вид доставки" => 'delivery_type',
            "Зона доставки" => 'delivery_zone',
            "Превышение зоны доставки, км" => 'over_delivery_zone_km',
            "Вес заказа, кг" => 'order_weight',
            "Вид подъёма" => 'lift_type',
            "Этаж подъёма на лифте" => 'lift_floor',
            "Вес подъёма на лифте, кг" => 'lift_weight_kg',
            "Этаж подъёма вручную" => 'hand_lift_floor',
            "Вес подъёма вручную, кг" => 'hand_lift_weight_kg',
            "Расстояние переноса, м" => 'transfer_distance',
            "Вес переноса, кг" => 'transfer_weight',
            "Стоимость доставляемого товара, руб." => 'products_cost',
            "Стоимость услуг транспортировки, руб." => 'cost_of_transportation',
            "Стоимость услуг подъёма, руб." => 'lift_cost',
            "Стоимость услуг переноса, руб." => 'transfer_cost',
            "Общая стоимость услуг доставки, руб." => 'total_delivery_cost',
            "Комментарий к заявке" => 'comment',
            "Состав заказа" => 'order_list'
        ];
    }
}
