@extends('layouts.app')
@section('title', 'Регистрация клиента')
@section('content')
    <div id="contain" class="layout">
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="field-group">
                <input type="text" id="surname" name="lastname" class="text" placeholder="Фамилия">
                <input type="text" id="name" name="firstname" class="text" placeholder="Имя">
            </div>
            <input type="text" id="mname" name="patronymic" class="text" placeholder="Отчество">
            <input type="text" id="position" name="position" class="text" placeholder="Должность">
            <div class="field-group">
                <input type="text" id="wphone" name="work_phone" class="text text--middle"
                       placeholder="Рабочий телефон">
                <input type="text" id="dob" name="additional_number" class="text text--small" placeholder="доб.">
            </div>
            <input type="text" id="timezone" name="timezone" class="text" placeholder="Часовой пояс">
            <input type="text" id="sms-mobile" class="text" name="mobile_phone"
                   placeholder="Мобильный телефон для SMS-оповещений">
            <input type="text" id="email" class="text" placeholder="E-mail" name="email">
            <input type="text" id="company1" class="text" placeholder="Компания" name="company">
            <input type="text" id="inn" class="text" placeholder="ИНН" name="inn">
            <input type="text" id="kpp" class="text" placeholder="КПП" name="kpp">
            <input type="text" id="okpo" class="text" placeholder="ОКПО" name="okpo">
            <input type="text" id="leg-address" class="text" placeholder="Юридический адрес" name="legal_address">
            <input id="password" type="password" class="text" name="password" required autocomplete="new-password"
                   placeholder="Пароль">
            <input id="password-confirm" type="password" class="text" name="password_confirmation" required
                   placeholder="Подтвердите пароль">
            <div class="row mb-0">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Зарегистрировать') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
