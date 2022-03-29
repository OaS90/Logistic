@extends('layouts.app')

@section('title', 'Заявка на доставку №' . $application->id)

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

            <section class="delivery-app-section">
                <div class="breadcrumb">
                    <a class="breadcrumb-item" href="{{ route('application-list') }}">Список заявок</a>
                    <span class="breadcrumb-item">Заявка на доставку №&nbsp;{{ $application->id }}</span>
                </div>
                <h3 class="sticker-wrapper"><span>Заявка на доставку №&nbsp;{{ $application->id }}</span><span
                        class="sticker">В работе</span></h3>
                <div class="dates-wrapper">
                    Действует с 16.02.2022 по 17.02.2022
                </div>
                <div class="app-control">
                    <div class="app-control__item">
                        <svg class="app-svg" width="20" height="20" viewBox="0 0 20 20" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M3.33366 4.16667V15.8333H16.667V4.16667H3.33366ZM2.50033 2.5H17.5003C17.7213 2.5 17.9333 2.5878 18.0896 2.74408C18.2459 2.90036 18.3337 3.11232 18.3337 3.33333V16.6667C18.3337 16.8877 18.2459 17.0996 18.0896 17.2559C17.9333 17.4122 17.7213 17.5 17.5003 17.5H2.50033C2.27931 17.5 2.06735 17.4122 1.91107 17.2559C1.75479 17.0996 1.66699 16.8877 1.66699 16.6667V3.33333C1.66699 3.11232 1.75479 2.90036 1.91107 2.74408C2.06735 2.5878 2.27931 2.5 2.50033 2.5ZM5.00033 5.83333H7.50033V14.1667H5.00033V5.83333ZM8.33366 5.83333H10.0003V14.1667H8.33366V5.83333ZM10.8337 5.83333H11.667V14.1667H10.8337V5.83333ZM12.5003 5.83333H15.0003V14.1667H12.5003V5.83333Z"/>
                        </svg>
                       <span class="app-control__txt" onclick="sticker.submit()">Сформировать наклейку</span>
                       <form action="{{ route('make-sticker', ['id' => $application->id]) }}" hidden name="sticker"></form>
                    </div>
                    <div class="app-control__item">
                        <svg class="app-svg" width="20" height="20" viewBox="0 0 20 20" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M5.83333 4.99999V2.49999C5.83333 2.27898 5.92113 2.06701 6.07741 1.91073C6.23369 1.75445 6.44565 1.66666 6.66667 1.66666H16.6667C16.8877 1.66666 17.0996 1.75445 17.2559 1.91073C17.4122 2.06701 17.5 2.27898 17.5 2.49999V14.1667C17.5 14.3877 17.4122 14.5996 17.2559 14.7559C17.0996 14.9122 16.8877 15 16.6667 15H14.1667V17.5C14.1667 17.96 13.7917 18.3333 13.3275 18.3333H3.33917C3.22927 18.334 3.12033 18.3129 3.0186 18.2713C2.91687 18.2298 2.82436 18.1685 2.74638 18.0911C2.6684 18.0136 2.60649 17.9216 2.56421 17.8201C2.52193 17.7187 2.50011 17.6099 2.5 17.5L2.5025 5.83332C2.5025 5.37332 2.8775 4.99999 3.34083 4.99999H5.83333ZM4.16833 6.66666L4.16667 16.6667H12.5V6.66666H4.16833ZM7.5 4.99999H14.1667V13.3333H15.8333V3.33332H7.5V4.99999ZM5.83333 9.16666H10.8333V10.8333H5.83333V9.16666ZM5.83333 12.5H10.8333V14.1667H5.83333V12.5Z"/>
                        </svg>
                        <span class="app-control__txt">Копировать</span>
                    </div>
                    <div class="app-control__item">
                        <svg class="app-svg" width="20" height="20" viewBox="0 0 20 20" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10.8333 9.99999H13.3333L10 13.3333L6.66667 9.99999H9.16667V6.66666H10.8333V9.99999ZM12.5 3.33332H4.16667V16.6667H15.8333V6.66666H12.5V3.33332ZM2.5 2.49332C2.5 2.03666 2.8725 1.66666 3.3325 1.66666H13.3333L17.5 5.83332V17.4942C17.5008 17.6036 17.48 17.7121 17.4388 17.8135C17.3976 17.9149 17.3369 18.0072 17.2601 18.0851C17.1832 18.1631 17.0918 18.2251 16.991 18.2677C16.8902 18.3102 16.7819 18.3326 16.6725 18.3333H3.3275C3.10865 18.3318 2.89918 18.2442 2.74435 18.0896C2.58951 17.9349 2.50175 17.7255 2.5 17.5067V2.49332Z"/>
                        </svg>
                        <span class="app-control__txt">Скачать</span>
                    </div>
                    <div class="app-control__item">
                        <svg class="app-svg" width="20" height="20" viewBox="0 0 20 20" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M5.345 13.3333L13.7967 4.88166L12.6183 3.70333L4.16667 12.155V13.3333H5.345ZM6.03583 15H2.5V11.4642L12.0292 1.935C12.1854 1.77877 12.3974 1.69101 12.6183 1.69101C12.8393 1.69101 13.0512 1.77877 13.2075 1.935L15.565 4.2925C15.7212 4.44877 15.809 4.66069 15.809 4.88166C15.809 5.10263 15.7212 5.31456 15.565 5.47083L6.03583 15ZM2.5 16.6667H17.5V18.3333H2.5V16.6667Z"/>
                        </svg>
                        <span class="app-control__txt">Редактировать</span>
                    </div>
                    <div class="app-control__item">
                        <svg class="app-svg" width="20" height="20" viewBox="0 0 20 20" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M14.167 4.99999H18.3337V6.66666H16.667V17.5C16.667 17.721 16.5792 17.933 16.4229 18.0892C16.2666 18.2455 16.0547 18.3333 15.8337 18.3333H4.16699C3.94598 18.3333 3.73402 18.2455 3.57774 18.0892C3.42146 17.933 3.33366 17.721 3.33366 17.5V6.66666H1.66699V4.99999H5.83366V2.49999C5.83366 2.27898 5.92146 2.06701 6.07774 1.91073C6.23402 1.75445 6.44598 1.66666 6.66699 1.66666H13.3337C13.5547 1.66666 13.7666 1.75445 13.9229 1.91073C14.0792 2.06701 14.167 2.27898 14.167 2.49999V4.99999ZM15.0003 6.66666H5.00033V16.6667H15.0003V6.66666ZM7.50033 9.16666H9.16699V14.1667H7.50033V9.16666ZM10.8337 9.16666H12.5003V14.1667H10.8337V9.16666ZM7.50033 3.33332V4.99999H12.5003V3.33332H7.50033Z"/>
                        </svg>
                        <span class="app-control__txt">Удалить</span>
                    </div>
                </div>
            </section>
            <section class="data-section">
                <div class="profile-section__data">
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Номер заказа</div>
                        <div class="profile-data__value">{{ $application->order_number }}</div>
                    </div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Наименование товара</div>
                        <div class="profile-data__value">{{ $application->product_name }}</div>
                    </div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Бренд</div>
                        <div class="profile-data__value">{{ $application->product_brand }}</div>
                    </div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Форма оплаты</div>
                        <div class="profile-data__value">{{ $application->payment_type }}</div>
                    </div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Ставка НДС</div>
                        <div class="profile-data__value">{{ $application->vat }}</div>
                    </div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Стоимость</div>
                        <div class="profile-data__value profile-data__value-cost">{{ $application->cost }} ₽</div>
                    </div>
                    <div class="profile-data__hdr">Параметры отправляемого груза</div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Ширина</div>
                        <div class="profile-data__value">{{ $application->width }}см</div>
                    </div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Высота</div>
                        <div class="profile-data__value">{{ $application->height }} см</div>
                    </div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Глубина</div>
                        <div class="profile-data__value">{{ $application->depth }} см</div>
                    </div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Количество</div>
                        <div class="profile-data__value">{{ $application->count }}</div>
                    </div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Объем</div>
                        <div class="profile-data__value">{{ $application->volume }} м2</div>
                    </div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Расчетный вес</div>
                        <div class="profile-data__value">{{ $application->weight }} кг</div>
                    </div>
                    <div class="profile-data profile-data--address">
                        <div class="profile-data__ttl">Адрес склада отгрузки</div>
                        <div class="profile-data__value">{{ $application->warehouse_address }}
                        </div>
                    </div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Квартира</div>
                        <div class="profile-data__value">{{ $application->flat }}</div>
                    </div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Этаж</div>
                        <div class="profile-data__value">{{ $application->floor }}</div>
                    </div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Подъезд</div>
                        <div class="profile-data__value">{{ $application->entrance }}</div>
                    </div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Почтовый индекс</div>
                        <div class="profile-data__value">{{ $application->postcode }}</div>
                    </div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Коментарии</div>
                        <div class="profile-data__value">
                            {{ $application->comment }}
                        </div>
                    </div>
                    <div class="profile-data__hdr">Информация о покупателе</div>
                    <div class="profile-data">
                        <div class="profile-data__ttl">ФИО покупателя</div>
                        <div class="profile-data__value">{{ $application->client_name }}</div>
                    </div>
                    <div class="profile-data">
                        <div class="profile-data__ttl">Телефон покупателя</div>
                        <div class="profile-data__value">{{ $application->client_phone }}</div>
                    </div>
                    <div class="total-block">
                        <div class="total-block__ttl">Сумма к получению с покупателя</div>
                        <div class="total-block__amount">{{ $application->cost }} ₽</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
