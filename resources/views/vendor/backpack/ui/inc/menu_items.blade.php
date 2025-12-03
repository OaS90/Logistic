<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
{{--<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>--}}
@if(backpack_user()->hasRole('Логисты') ||
    backpack_user()->hasRole('admin') ||
    backpack_user()->hasRole('transportation department') ||
    backpack_user()->hasRole('support')
    )
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i>Борт Удачи</a>
        <ul class="nav-dropdown-items">
            <li class='nav-item'>
                <a class='nav-link' href='{{ backpack_url('partners') }}'><i class="las la-user-tie"></i> Партнёры</a>
            </li>
            <li class='nav-item'>
                <a class='nav-link' href='{{ backpack_url('applications') }}'><i class="las la-file-alt"></i> Заявки</a>
            </li>
            <li class='nav-item'>
                <a class='nav-link' href='{{ backpack_url('warehouses') }}'><i class="las la-store-alt"></i> Партнёрские
                    Склады</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('application-obi') }}"><i class="las la-file-alt"></i> Заявки Obi</a></li>
        </ul>
    </li>
    @if(!backpack_user()->hasRole('guest') && !backpack_user()->hasRole('transportation department'))
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('quote-emails') }}'><i class="las la-mail-bulk"></i> Email уведомления по квотам</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('regions') }}'><i class='nav-icon la la-map'></i> Регионы</a></li>
    @endif
@endif
<!-- Users, Roles, Permissions -->
@if(backpack_user()->hasRole('Логисты') ||
    backpack_user()->hasRole('admin') ||
    backpack_user()->hasRole('transportation department') ||
    backpack_user()->hasRole('support')
    )
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('filial') }}">
            <i class="nav-icon las la-store"></i>Филиалы</a>
    </li>
{{--    <li class="nav-item">--}}
{{--        <a class="nav-link" href="{{ backpack_url('hru-warehouses') }}">--}}
{{--            <i class="nav-icon las la-warehouse"></i> Склады--}}
{{--        </a>--}}
{{--    </li>--}}
@endif
@if(backpack_user()->hasRole('quotes') || backpack_user()->hasRole('admin'))
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('quotes') }}'><i class="las la-user-tie"></i> Квоты</a></li>
@endif

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-gears"></i>Настройка Тарифов</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('tariffs') }}"><i class="nav-icon la la-file-invoice"></i> Тарифы</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('tariff-categories') }}"><i class="nav-icon la la-list-ul"></i> Категории</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('tariffs/validation') }}"><i class="las la-list-alt"></i> Валидация тарифов</a>
        </li>
        @if(backpack_user()->email == 'oas90@bk.ru')
            <li class="nav-item">
                <a class="nav-link" href="{{ backpack_url('tariff-zones') }}"><i class="nav-icon la la-map-marked"></i> Зоны</a>
            </li>
        @endif
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon la la-shuttle-van"></i> Транспортные компании
    </a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('transport-company') }}">
                <i class="nav-icon la la-briefcase"></i> Компании
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('transport-company-settings') }}">
                <i class="nav-icon la la-gears"></i> Настройки
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('transport-company-shipment-warehouse') }}">
                <i class="nav-icon las la-warehouse"></i> Склады отгрузки
            </a>
        </li>
    </ul>
</li>
@if(backpack_user()->hasRole('admin'))
    <li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i>Настройки ролей</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>Пользователи</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-id-badge"></i> <span>Роли</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>Разрешения</span></a></li>
    </ul>
</li>
@endif

@if(backpack_user()->hasRole('transportation department') ||
    backpack_user()->hasRole('support') ||
    backpack_user()->hasRole('admin')
)
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#">
            <i class="nav-icon la la-map-marked"></i> Яндекс зоны
        </a>
        <ul class="nav-dropdown-items">
            <li class="nav-item">
                <a class="nav-link" href="{{ backpack_url('yandex-zones') }}">
                    <i class="nav-icon la la-gears"></i> <span>Настройка</span>
                </a>
            </li>
        </ul>
    </li>
@endif

@if(backpack_user()->hasRole('transportation department') ||
    backpack_user()->hasRole('support') ||
    backpack_user()->hasRole('admin')
)
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon la la-support"></i> Поддержка
    </a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('support/apps') }}">
                <i class="nav-icon la la-check"></i> <span>Статусы Заявок</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('support/show-upload-page') }}">
                <i class="nav-icon la la-upload"></i><span>Загрузка/Обновление заявок</span>
            </a>
        </li>
    </ul>
</li>
@endif
@if(backpack_user()->hasRole('admin'))
    <x-backpack::menu-item title="Логи изменений" icon="la la-stream" :link="backpack_url('activity-log')" />
@endif
