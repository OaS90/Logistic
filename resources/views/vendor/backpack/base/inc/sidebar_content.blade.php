<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
{{--<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>--}}
@if(backpack_user()->hasRole('Логисты') || backpack_user()->hasRole('admin'))
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('partners') }}'><i class="las la-user-tie"></i> Партнёры</a></li>
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('applications') }}'><i class="las la-file-alt"></i> Заявки</a></li>
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('warehouses') }}'><i class="las la-file-alt"></i> Склады</a></li>
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('quotes') }}'><i class="las la-user-tie"></i> Квоты</a></li>
    @if(!backpack_user()->hasRole('guest'))
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('quote-emails') }}'><i class="las la-user-tie"></i>Email уведомления по квотам</a></li>
    @endif
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('regions') }}'><i class='nav-icon la la-question'></i>Регионы</a></li>
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('quote-warehouse') }}'><i class='nav-icon la la-question'></i>Склады по квотам</a></li>
@endif
<!-- Users, Roles, Permissions -->

@if(backpack_user()->hasRole('quotes'))
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('quotes') }}'><i class="las la-user-tie"></i> Квоты</a></li>
@endif

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

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon la la-question"></i> Поддержка
    </a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('support-orders') }}">
                <i class="nav-icon la la-user"></i> <span>Заявки</span>
            </a>
        </li>
    </ul>
</li>