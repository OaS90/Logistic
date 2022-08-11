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
                <h2>Загрузка заказов</h2>
                <p>Сервис принимает на вход данные в формате JSON, как результат сервис возвращает детальный ответ с результатом загрузки по каждому заказу, также в формате JSON.</p>
                <p>Доступ к функции осуществляется методом POST по URL: <span class="post-api"><strong>https://lk.bortudachi.ru/api/v1/order/create</strong></span></p>
                <p>Товары заказа указываются в поле products[].</p>
                <table class="api-table">
                    <thead>
                        <tr>
                            <th>Название параметра</th>
                            <th>Описание параметра</th>
                            <th>Признак обязательности</th>
                        </tr>
                    </thead>
                    <tbody aria-live="polite" aria-relevant="all">
                        <tr>
                            <td><strong>partnerId</strong></td>
                            <td>Идентификатор заказчика в личном кабинете</td>
                            <td>обязательный</td>
                        </tr>
                        <tr>
                            <td style="text-align: left;" ><strong>id</strong></td>
                            <td >Номер заказа в системе заказчика</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td ><strong>storeId</strong></td>
                            <td >Признак склада отгрузки. Строка до 15 символов.
                                Согласовывается до старта/подключения каждого нового склада.
                            </td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td ><strong>paymentMethod</strong></td>
                            <td >Способ оплаты. Принимаемые значения: "Картой", "Наличными",
                                "Оплачен".
                            </td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td ><strong>comment</strong></td>
                            <td >Строка до 160 символов</td>
                            <td >необязательный</td>
                        </tr>
                        <tr>
                            <td ><strong>deliveryDate</strong></td>
                            <td >Дата доставки</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td ><strong>deliveryTimeFrom</strong></td>
                            <td >Время доставки "с"</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td ><strong>deliveryTimeTo</strong></td>
                            <td >Время доставки "до"</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td ><strong>buyer</strong></td>
                            <td >Информация о покупателе</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >fio</td>
                            <td colspan="1" style="margin-left: 200.0px;" >ФИО покупателя, строка до 255
                                символов
                            </td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >phone</td>
                            <td colspan="1" style="margin-left: 80.0px;" >Телефон покупателя, формат -
                                "+NNNNNNNNNN"
                            </td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td ><strong>address</strong></td>
                            <td >Адрес доставки</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >regionName</td>
                            <td >Название региона</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >cityName</td>
                            <td >Название населённого пункта</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >cityId</td>
                            <td >Код ФИАС населённого пункта</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >street</td>
                            <td >Название улицы</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >streetId</td>
                            <td >Код ФИАС улицы</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >building</td>
                            <td >Номер дома</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >floor</td>
                            <td >Этаж</td>
                            <td >необязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >entrance</td>
                            <td >Номер подъезда</td>
                            <td >необязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >flat</td>
                            <td >Номер квартиры</td>
                            <td >необязательный</td>
                        </tr>
                        <tr>
                            <td ><strong>products (array)</strong></td>
                            <td >Параметры отправляемого груза</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >name</td>
                            <td >Наименование товара</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >brand</td>
                            <td >Бренд</td>
                            <td >необязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >sku</td>
                            <td >Артикул</td>
                            <td >необязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >count</td>
                            <td >Количество</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >cost</td>
                            <td >Стоимость</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >VATRate</td>
                            <td >Ставка НДС</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >leftToPay</td>
                            <td >Сумма к получению с покупателя</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >weight</td>
                            <td >Расчетный вес, кг</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >volume</td>
                            <td >Объем, м2</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >width</td>
                            <td >Ширина, см</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >height</td>
                            <td >Высота, см</td>
                            <td >обязательный</td>
                        </tr>
                        <tr>
                            <td colspan="1" style="text-align: right;" >depth</td>
                            <td >Глубина, см</td>
                            <td >обязательный</td>
                        </tr>
                    </tbody>
                </table>
                <br>

                <div class="request-button" onclick="openRequest('order-request')">Пример запроса <strong class="show-request">+</strong></div>
                <div class="order-request" style="display: none">
                                <pre style="background-color: #dddddd">
                                [
                                    {
                                        "id": “47198”, // Номер заказа
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
                </div>


                <br>
                <h2>Описание параметров ответа</h2>
                <table class="api-table">
                    <thead>
                    <tr role="row" class="tablesorter-headerRow">
                        <th>Название параметра</th>
                        <th>Описание параметра</th>
                        <th>Признак обязательности</th>
                    </tr>
                    </thead>
                    <tbody aria-live="polite" aria-relevant="all">
                    <tr>
                        <td><strong>code</strong></td>
                        <td>Сформированный код заказа. Формируется из двух частей - номера в системе заказчика и номера в системе исполнителя. </td>
                        <td>обязательный</td>
                    </tr>
                    <tr>
                        <td><strong>success</strong></td>
                        <td>Результат загрузки заказа:
                            true – заказ успешно загружен,
                            false – заказ загружен с ошибкой.</td>
                        <td>обязательный</td>
                    </tr>
                    <tr>
                        <td><strong>message</strong></td>
                        <td>Описание ошибки (если success = false). Константой показывается фраза "Неверно указан" и
                            выводится название параметра, вызвавшего ошибку. При неверной указании более чем одного
                            параметра данные параметры выводятся через запятую.
                        </td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
                <br>

                <div class="request-button" onclick="openRequest('order-response')">Пример ответа <strong>+</strong></div>
                <div class="order-response" style="display:none;">
                    <pre style="background-color: #dddddd">
                        {
                             "code": "47198-234590",
                             "success": false,
                             "message": "Неверно указан deliveryTimeFrom"
                        },
                        {
                             "code": "4890330-234591",
                             "success": true,
                             "message": ""
                        }
                    </pre>
                </div>


                <hr style="margin: 15px 0">

                <h2>Формирование наклеек</h2>
                <p>Сервис может быть использован для формирования наклейки (стикера) в формате PDF для одной или
                    нескольких посылок (мест).</p>
                <p>Доступ к функции осуществляется методом GET по
                    URL: <span class="post-api"><strong>https://lk.bortudachi.ru/api/v1/order/{orderId}/stickers</strong></span>
                </p>
                <p>Метод возвращает готовый PDF файл (Content-Type:	application/pdf)</p>

                <hr style="margin: 15px 0">

                <h2>Обновление статуса</h2>
                <p>Получение статуса по заказу осуществляется методом GET по
                    URL: <span class="post-api">
                        <strong>http://logistic.loc/api/v1/order/get-status?partnerId=000000004&orderId=ALI-007783</strong>
                    </span>
                </p>

                <p>Параметры запроса:</p>
                <table class="api-table">
                    <thead>
                    <tr>
                        <th>Название параметра</th>
                        <th>Описание параметра</th>
                        <th>Признак обязательности</th>
                    </tr>
                    </thead>
                    <tbody aria-live="polite" aria-relevant="all">
                        <tr>
                            <td><strong>partnerId</strong></td>
                            <td>Идентификатор заказчика в личном кабинете</td>
                            <td>обязательный</td>
                        </tr>
                        <tr>
                            <td><strong>orderId</strong></td>
                            <td>Номер заказа в системе заказчика</td>
                            <td>обязательный</td>
                        </tr>
                    </tbody>
                </table>

                <div class="request-button" onclick="openRequest('order-status-response')">Пример ответа <strong>+</strong></div>
                <div class="order-status-response" style="display:none;">
                    <pre style="background-color: #dddddd">
                        {
                             "message": success,
                             "orderNumber": "Номер заказа"
                             "orderStatus": "Статус заказа",
                        },
                    </pre>
                    <p>При ошибке:</p>
                    <pre style="background-color: #dddddd">
                        {
                            "message": "Сообщение об ошибке",
                            "orderNumber": "Номер заказа"
                        }
                    </pre>
                </div>
{{--                <table class="api-table">--}}
{{--                    <thead>--}}
{{--                    <tr role="row" class="tablesorter-headerRow">--}}
{{--                        <th>Название параметра</th>--}}
{{--                        <th>Описание параметра</th>--}}
{{--                        <th>Признак обязательности</th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody aria-live="polite" aria-relevant="all">--}}
{{--                        <tr>--}}
{{--                            <td><strong>code</strong></td>--}}
{{--                            <td>код заказа</td>--}}
{{--                            <td>обязательный параметр</td>--}}
{{--                        </tr>--}}
{{--                    </tbody>--}}
{{--                </table>--}}

{{--                <br>--}}

{{--                <div class="request-button" onclick="openRequest('sticker-request')">Пример запроса <strong>+</strong></div>--}}
{{--                <div class="sticker-request" style="display:none;">--}}
{{--                    <pre style="background-color: #dddddd">--}}
{{--                        {--}}
{{--                            "code": "pl-025376-45688333HOL",--}}
{{--                        }--}}
{{--                    </pre>--}}
{{--                </div>--}}

{{--                <br>--}}

{{--                <h2>Описание ответа</h2>--}}
{{--                <table class="api-table">--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                        <th>Название параметра</th>--}}
{{--                        <th>Описание параметра</th>--}}
{{--                        <th>Признак обязательности</th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody aria-live="polite" aria-relevant="all">--}}
{{--                    <tr>--}}
{{--                        <td><strong>parcel</strong></td>--}}
{{--                        <td>код отправления. Содержит:&nbsp; номер отправления--}}
{{--                            заказчика, номер отправления исполнителя, номер места. Под номером места понимается--}}
{{--                            номенклатурная позиция в заказе.--}}
{{--                        </td>--}}
{{--                        <td>обязательный параметр</td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td><strong>name</strong></td>--}}
{{--                        <td><p><span--}}
{{--                                    style="color: rgb(51,51,51);">Наименование товара</span></p></td>--}}
{{--                        <td>обязательный параметр</td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td ><strong>fio</strong></td>--}}
{{--                        <td>ФИО получателя</td>--}}
{{--                        <td>обязательный параметр</td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td><strong>address</strong></td>--}}
{{--                        <td>адрес доставки</td>--}}
{{--                        <td>обязательный параметр</td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td><strong>storeID</strong></td>--}}
{{--                        <td>Признак склада отгрузки. Строка до 15 символов.</td>--}}
{{--                        <td>обязательный параметр</td>--}}
{{--                    </tr>--}}
{{--                    </tbody>--}}
{{--                </table>--}}
{{--                <br>--}}

{{--                <div class="request-button" onclick="openRequest('sticker-response')">Пример ответа <strong>+</strong></div>--}}
{{--                <div class="sticker-response" style="display:none;">--}}
{{--                    <pre style="background-color: #dddddd">--}}
{{--                        {--}}
{{--                             "codes_success": [--}}
{{--                              {--}}
{{--                               "code": "pl-025376-45688333HOL",--}}
{{--                                "parcels":--}}
{{--                                    [--}}
{{--                                    {--}}
{{--                                     "parcel": "pl-025376-45688333HOL-1",--}}
{{--                                     "name": "Газовая панель"--}}
{{--                                    },--}}
{{--                                    {--}}
{{--                                     "parcel": "pl-025376-45688333HOL-2",--}}
{{--                                     "name": "Стиральная машина"--}}
{{--                                    },--}}
{{--                                    ]--}}
{{--                                "fio": "Иванов Иван Иванович",--}}
{{--                                "address": "Москва, ул.Октябрьская, д.1к6, кв.55, эт.5, под.3"--}}
{{--                                "storeID": "11ff000000fff01"--}}
{{--                                }--}}
{{--                             ],--}}
{{--                             "success": true,--}}
{{--                             "message": null--}}
{{--                        }--}}
{{--                    </pre>--}}
{{--                </div>--}}

            </section>
        </div>
    </div>
@endsection
