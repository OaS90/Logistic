@extends('layouts.app')

@section('content')

    <div id="contain" class="layout">
        <div id="navbar-button" class="navbar-toggler">
            <svg class="navbar-toggler__ico navbar-toggler__ico--open" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M3 4H21V6H3V4ZM3 11H21V13H3V11ZM3 18H21V20H3V18Z" fill="white"/>
            </svg>
            <svg class="navbar-toggler__ico navbar-toggler__ico--close" fill="none" xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24">
                <path
                    d="M11.9997 10.586L16.9497 5.63599L18.3637 7.04999L13.4137 12L18.3637 16.95L16.9497 18.364L11.9997 13.414L7.04974 18.364L5.63574 16.95L10.5857 12L5.63574 7.04999L7.04974 5.63599L11.9997 10.586Z"
                    fill="#fff"></path>
            </svg>
        </div>

    @include('sidebar')

        <div class="layout__right">

            @include('header')
            <section class="applications-section">
            <p>
                Для получения заявок, сервис использует брокер очередей RabbitMQ. Для того, чтобы начать пользоваться,
                требуется настроить обработчик очередей.
                Настройки:
            </p>
            <ul>
                <li>Host: </li>
                <li>Port: 15672</li>
                <li>User: partners</li>
                <li>Password: NeFHCp6KUre3</li>
                <li>Queue: partners</li>
            </ul>

            <p>Пример сообщения:</p>
            <pre style="background-color: #dddddd">
                [
                    {
                        "id": “47198”, // Номер заказа
                        "docVer": 1, // Версия (в случае повторной выгрузки увеличивается на 1)
                        "storeID": «11ff000000fff01», // признак точки забора товара у поставщика (склада). Строка до 15 символов. Согласовывается до старта/подключения нового склада по каждому.
                        "paymentMethod": "Картой", // Способ оплаты ["Картой" (при получении), "Наличными", "Предоплата"]
                        "comment": "Есть грузовой лифт", // Комментарий
                        "deliveryDate": "23.05.2020", // Дата доставки
                        "deliveryTimeFrom": "09:00" | false, // Время доставки "с"
                        "deliveryTimeTo": "14:00" | false, // Время доставки "до"
                        "buyer": {
                            "fio": "Никита Воробьев",
                            "phone": "+7 910 111-11-11"
                        },
                        "address": {
                            "regionName": "Московская обл",
                            "cityName": "Москва",
                            "cityId": "0c5b2444-70a0-4932-980c-b4dc0d3f02b5", // ФИАС код города/населенного пункта
                            "street": "ул Октябрьская",
                            "streetId": "5d305942-648a-427f-9a29-48b5b07501e2", // ФИАС код улицы

                            "building": "217, корпус 2",
                            "floor": "10", // необязательно
                            "flat": "176" // необязательно
                        },
                        "products": [
                            {
                                "name": "Газовая панель", // Товар
                                "vendorCode": "789797979797", // Артикул
                                "count": 2, // Количество
                                "cost": 10000, // Оценочная стоимость
                                "costAfterDiscounts": 8000, // Стоимость с учетом скидки
                                "VATRate": 20, // Ставка НДС
                                "leftToPay": 16000, // Сумма к получению
                                "weight": 15.5, // Расчетный вес (кг)
                                "setId": "54654_1", // Входит в состав комплекта, уникальное значение, необязательно (id записи корзины комплекта + инкремент для разделения по кол-ву)
                                "brand": "Бренд", // Бренд
                                "tnved": "8516609000", // Код ТНВЭД
                                "country": "643", // код страны происхождения по ОКСМ
                                "barcode": "8699272141522", // EAN
                                "volume": 0,119970, // объем в м2
                                "width": 62.00, // ширина в см
                                "height": 45.00, // высота в см
                               "depth": 43.00 // глубина в см
                            }, {...}
                        ]
                    }, {...}
                ]
           </pre>
            </section>
        </div>
    </div>
@endsection
