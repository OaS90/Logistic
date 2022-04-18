@extends('layouts.app')

@section('title', 'Список заявок')

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
                <h3>Список заявок</h3>
                <div class="app-list">
                    <div class="app-items">
                        <div class="app-item app-item--new">
                            <div class="app-item__ttl">Новый</div>
                            <div class="app-item__amount">{{ $statuses['new'] }} заявок</div>
                        </div>
                        <div class="app-item app-item--in-work">
                            <div class="app-item__ttl">В работе</div>
                            <div class="app-item__amount">{{ $statuses['inProgress'] }} заявок</div>
                        </div>
                        <div class="app-item app-item--loaded">
                            <div class="app-item__ttl">Загружен</div>
                            <div class="app-item__amount">{{ $statuses['loaded'] }} заявок</div>
                        </div>
                        <div class="app-item app-item--aside">
                            <div class="app-item__ttl">Отложен</div>
                            <div class="app-item__amount">{{ $statuses['postponed'] }} заявок</div>
                        </div>
                        <div class="app-item app-item--no">
                            <div class="app-item__ttl">Отказ</div>
                            <div class="app-item__amount">{{ $statuses['refusal'] }} заявок</div>
                        </div>
                        <div class="app-item app-item--completed">
                            <div class="app-item__ttl">Выполнен</div>
                            <div class="app-item__amount">{{ $statuses['completed'] }} заявок</div>
                        </div>
                        <div class="app-item app-item--flaw">
                            <div class="app-item__ttl">Брак</div>
                            <div class="app-item__amount">{{ $statuses['defect'] }} заявок</div>
                        </div>
                    </div>
                </div>
                <div class="app-filter">
                    <div class="app-filter__options">
                        <div class="app-filter__wrapper">
                            <div class="app-filter__ttl">Номер договора</div>
                            <select class="select">
                                <option>Все</option>
                                <option>1</option>
                                <option>2</option>
                            </select>
                        </div>
                        <div class="app-filter__wrapper app-filter__wrapper-period">
                            <div class="app-filter__ttl">За период</div>
                            <div class="field-group justify-unset">
                                <div class="field-group__label">С</div>
{{--                                <select class="select select--time">--}}
{{--                                    <option>01.02.2022</option>--}}
{{--                                    <option>02.02.2022</option>--}}
{{--                                    <option>03.02.2022</option>--}}
{{--                                </select>--}}
                                <date-picker input-class="text time-picker"></date-picker>
                                <div class="field-group__label">До</div>
{{--                                <select class="select select--time">--}}
{{--                                    <option>__.__.___</option>--}}
{{--                                    <option>04.02.2022</option>--}}
{{--                                    <option>05.02.2022</option>--}}
{{--                                </select>--}}
                                <date-picker input-class="text time-picker"></date-picker>
                            </div>
                        </div>
                        <input type="submit" value="Найти" class="btn">
                    </div>
                </div>
                <div class="extra-options">
                    <div class="extra-options__nav">
                        Дополнительные параметры
                    </div>
                    <div class="extra-options__content">
                        Какие-то параметры...
                    </div>
                </div>
            </section>
            <section class="report-section">
                <div class="report-section__hdr">
                    <span>Отчет составлен: 24 февраля, 15:00</span>
                    <span>Найдено записей: {{ count($list) }}</span>
                </div>
                <div class="report-section__container">
                    <table class="report-table">
                        <tr>
                            <th>Номер заказа</th>
                            <th>Наименование товара</th>
                            <th>Артикул</th>
                            <th>Бренд</th>
                            <th>Форма оплаты</th>
                            <th>Ставка НДС</th>
                            <th>Стоимость</th>
                            <th>Признак склада отгрузки</th>
                            <th>Дата доставки</th>
                            <th>Адрес доставки</th>
                        </tr>
                        @foreach($list as $application)
                            <tr>
                                <td><a href="{{ route('application-show', ['id' => $application->id]) }}">{{ $application->order_number }}</a></td>
                                <td>{{ $application->product_name }}</td>
                                <td>{{ $application->product_art }}</td>
                                <td>{{ $application->product_brand }}</td>
                                <td>{{ $application->payment_type }}</td>
                                <td>{{ $application->vat }}</td>
                                <td>{{ $application->cost }} ₽</td>
                                <td>
                                    <div class="hidden-td">{{ $application->warehouse_address }}</div>
                                </td>
                                <td>{{ $application->delivery_date }}</td>
                                <td>
                                    <div class="hidden-td">
{{--                                        {{ (strlen($application->full_address) > 24) ? substr($application->full_address,0, 24).'...' : $application->full_address }}--}}
                                        {{ $application->full_address }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </section>
        </div>
        <div class="overlay js-overlay-modal"></div>
    </div>
@endsection
