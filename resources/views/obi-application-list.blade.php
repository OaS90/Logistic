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
                        <div class="app-item app-item--created">
                            <div class="app-item__ttl">Создано</div>
                            <div class="app-item__amount">{{ $statuses['created'] }} заявок</div>
                        </div>
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
            </section>
            <section class="report-section">
                <div class="report-section__hdr">
                    <span>Отчет составлен: {{ \Illuminate\Support\Carbon::now()->format('d-m-Y H:i') }}</span>
                    <span>Найдено записей: {{ count($list) }}</span>
                </div>
                <div class="report-section__container">
                    <table class="report-table">
                        <tr>
                            <th>Номер заказа</th>
                            <th>Наименование товара</th>
                            <th>Дата доставки</th>
                            <th>Адрес доставки</th>
                            <th>Статус</th>
                        </tr>
                        @foreach($list as $application)
                            <tr>
                                <td><a href="{{ route('application-show', ['id' => $application->id]) }}">{{ $application->order_number }}</a></td>
                                <td>{{ $application->products->first()?->name }}</td>
                                <td>{{ $application->delivery_date }}</td>
                                <td>
                                    <div class="hidden-td">
                                        {{--                                        {{ (strlen($application->full_address) > 24) ? substr($application->full_address,0, 24).'...' : $application->full_address }}--}}
                                        {{ $application->delivery_address }}
                                    </div>
                                </td>
                                <td>
                                    {{ $application->getStatus($application->status) }}
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
