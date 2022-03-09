@extends('layouts.app')

@section('title', 'Мой профиль')

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
            <section class="profile-section">
                <h3>Мой профиль</h3>
                <form id="profile-form" name="profile-form" method="post" action="{{ route('profile-save') }}"
                      enctype="multipart/form-data">
                    @csrf
                <div class="profile-section__wrapper">
                    <div class="profile-section__photo">
                        <div class="profile-section__photo-contaner">
                            <div class="profile-section__loading">
                                <label for="file-input">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2 3.993C2.00183 3.73038 2.1069 3.47902 2.29251 3.29322C2.47813 3.10742 2.72938 3.00209 2.992 3H21.008C21.556 3 22 3.445 22 3.993V20.007C21.9982 20.2696 21.8931 20.521 21.7075 20.7068C21.5219 20.8926 21.2706 20.9979 21.008 21H2.992C2.72881 20.9997 2.4765 20.895 2.29049 20.7088C2.10448 20.5226 2 20.2702 2 20.007V3.993ZM4 5V19H20V5H4ZM12 15C12.7956 15 13.5587 14.6839 14.1213 14.1213C14.6839 13.5587 15 12.7956 15 12C15 11.2044 14.6839 10.4413 14.1213 9.87868C13.5587 9.31607 12.7956 9 12 9C11.2044 9 10.4413 9.31607 9.87868 9.87868C9.31607 10.4413 9 11.2044 9 12C9 12.7956 9.31607 13.5587 9.87868 14.1213C10.4413 14.6839 11.2044 15 12 15ZM12 17C10.6739 17 9.40215 16.4732 8.46447 15.5355C7.52678 14.5979 7 13.3261 7 12C7 10.6739 7.52678 9.40215 8.46447 8.46447C9.40215 7.52678 10.6739 7 12 7C13.3261 7 14.5979 7.52678 15.5355 8.46447C16.4732 9.40215 17 10.6739 17 12C17 13.3261 16.4732 14.5979 15.5355 15.5355C14.5979 16.4732 13.3261 17 12 17ZM17 6H19V8H17V6Z"
                                        fill="#0595E6"/>
                                </svg>
                                    Загрузить фото
                                </label>
                                <input type="file" name="avatar" id="file-input">
                            </div>
                        </div>
                    </div>
                    <div class="profile-section__data">
                        <form id="profile-form" name="profile-form" method="post" action="{{ route('profile-save') }}">
                            @csrf
                            <div class="field-group">
                                <input type="text" value="{{ $user->lastname }}" id="surname" name="lastname" class="text" placeholder="Фамилия">
                                <input type="text" value="{{ $user->firstname }}" id="name" name="firstname" class="text" placeholder="Имя">
                            </div>
                            <input type="text" value="{{ $user->patronymic }}" id="mname" name="patronymic" class="text" placeholder="Отчество">
                            <input type="text" value="{{ $user->position }}" id="position" name="position" class="text" placeholder="Должность">
                            <div class="field-group">
                                <input type="text" value="{{ $user->work_phone }}" id="wphone" name="work_phone" class="text text--middle"
                                       placeholder="Рабочий телефон">
                                <input type="text" value="{{ $user->additional_number }}" id="dob" name="additional_number" class="text text--small" placeholder="доб.">
                            </div>
                            <input type="text" value="{{ $user->timezone }}" id="timezone" name="timezone" class="text" placeholder="Часовой пояс">
{{--                            <input type="text" value="" id="login" class="text" placeholder="Логин">--}}
                            <input type="text" value="{{ $user->mobile_phone }}" id="sms-mobile" class="text" name="mobile_phone"
                                   placeholder="Мобильный телефон для SMS-оповещений">
                            <input type="text" value="{{ $user->email }}" id="email" class="text" placeholder="E-mail" name="email">
                            <input type="text" value="{{ $user->company }}" id="company1" class="text" placeholder="Компания" name="company">
{{--                            <input type="text" value="" id="company2" class="text" placeholder="Компания">--}}
                            <input type="text" value="{{ $user->inn }}" id="inn" class="text" placeholder="ИНН" name="inn">
                            <input type="text" value="{{ $user->kpp }}" id="kpp" class="text" placeholder="КПП" name="kpp">
                            <input type="text" value="{{ $user->okpo }}" id="okpo" class="text" placeholder="ОКПО" name="okpo">
                            <input type="text" value="{{ $user->legal_address }}" id="leg-address" class="text" placeholder="Юридический адрес" name="legal_address">
                            <input type="submit" value="Сохранить изменения" class="btn">
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <div class="overlay js-overlay-modal"></div>
    </div>
@endsection
