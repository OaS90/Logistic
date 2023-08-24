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
                <h3 class="sticker-wrapper"><span>Заявка на доставку №&nbsp;{{ $application->id }}</span>
                    <span class="sticker">
                        {{ $application->status ? $application->getStatus($application->status) : 'Создано'}}
                    </span>
                </h3>
{{--                <div class="dates-wrapper">--}}
{{--                    Действует с 16.02.2022 по 17.02.2022--}}
{{--                </div>--}}
{{--                <div class="app-control">--}}
{{--                    <div class="app-control__item">--}}
{{--                        <svg class="app-svg" width="20" height="20" viewBox="0 0 20 20" fill="none"--}}
{{--                             xmlns="http://www.w3.org/2000/svg">--}}
{{--                            <path--}}
{{--                                    d="M3.33366 4.16667V15.8333H16.667V4.16667H3.33366ZM2.50033 2.5H17.5003C17.7213 2.5 17.9333 2.5878 18.0896 2.74408C18.2459 2.90036 18.3337 3.11232 18.3337 3.33333V16.6667C18.3337 16.8877 18.2459 17.0996 18.0896 17.2559C17.9333 17.4122 17.7213 17.5 17.5003 17.5H2.50033C2.27931 17.5 2.06735 17.4122 1.91107 17.2559C1.75479 17.0996 1.66699 16.8877 1.66699 16.6667V3.33333C1.66699 3.11232 1.75479 2.90036 1.91107 2.74408C2.06735 2.5878 2.27931 2.5 2.50033 2.5ZM5.00033 5.83333H7.50033V14.1667H5.00033V5.83333ZM8.33366 5.83333H10.0003V14.1667H8.33366V5.83333ZM10.8337 5.83333H11.667V14.1667H10.8337V5.83333ZM12.5003 5.83333H15.0003V14.1667H12.5003V5.83333Z"/>--}}
{{--                        </svg>--}}
{{--                        <span class="app-control__txt" onclick="sticker.submit()">Сформировать наклейку</span>--}}
{{--                        <form action="{{ route('make-sticker', ['id' => $application->id]) }}" hidden name="sticker"></form>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </section>
            <section class="data-section">
                <div class="profile-section__data">
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Номер заказа</div>
                        <div class="profile-data__value">{{ $application->order_number }}</div>
                    </div>
                    <div class="profile-data profile-data--all">
                        <div class="profile-data__ttl">Наименование товара</div>
                        <div class="profile-data__value">{{ $application->products[0]->name }}</div>
                    </div>

                    @if($application->comment)
                        <div class="profile-data profile-data--all">
                            <div class="profile-data__ttl">Коментарии</div>
                            <div class="profile-data__value">
                                {{ $application->comment }}
                            </div>
                        </div>
                    @endif

                    <div class="profile-data__hdr">Информация о покупателе</div>
                    <div class="profile-data">
                        <div class="profile-data__ttl">ФИО покупателя</div>
                        <div class="profile-data__value">{{ $application->client_name }}</div>
                    </div>
                    <div class="profile-data">
                        <div class="profile-data__ttl">Телефон покупателя</div>
                        <div class="profile-data__value">{{ $application->phone }}</div>
                    </div>
                    <div class="total-block">
                        <div class="total-block__ttl">Сумма к получению с покупателя</div>
                        <div class="total-block__amount">{{ $application->products_cost }} ₽</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
