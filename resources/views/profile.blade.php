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
                <div class="profile-section__wrapper">
                    <div class="profile-section__photo">
                        <div class="profile-section__photo-contaner">
                            <img class="profile-photo" src="{{ asset('storage/' . $user->avatar) }}"
                                 alt="{{ $user->full_name }}">
                        </div>
                        <div class="profile-section__photo-control">
                            <label for="file-input">
                                <span class="profile-photo-change">
                                   Сменить
                                    <input type="file" hidden name="avatar" id="file-input">
                                </span>
                            </label>
                            <span class="profile-photo-delete"><a class="no-underline" href="{{ route('avatar-delete') }}">Удалить</a></span>
                        </div>
                    </div>
                    <div class="profile-section__data">
                        <h3>{{ $user->full_name }}</h3>
                        @if($user->position)
                            <div class="profile-position">
                                {{ $user->position }}
                            </div>
                        @endif
                        @if($user->work_phone)
                            <div class="profile-data">
                                <div class="profile-data__ttl">Телефон рабочий:</div>
                                <div class="profile-data__value">{{ $user->work_phone }}</div>
                            </div>
                        @endif
                        @if($user->additional_number)
                            <div class="profile-data">
                                <div class="profile-data__ttl">доб.</div>
                                <div class="profile-data__value">{{ $user->additional_number }}</div>
                            </div>
                        @endif
                        <div class="profile-data">
                            <div class="profile-data__ttl">Телефон мобильный:</div>
                            <div class="profile-data__value">{{ $user->mobile_phone }}</div>
                        </div>
                        @if($user->email)
                            <div class="profile-email">
                                {{ $user->email }}
                            </div>
                        @endif
                        <a href="{{ route('profile-edit-form') }}" class="btn btn--middle no-underline" style="padding: 13px; display: block">Редактировать профиль</a>
                    </div>
                </div>
            </section>
            @if($user->company)
                <section class="profile-section--company">
                    <div class="profile-section__data profile-section__data--company">
                        <div class="company_name">{{ $user->company }}</div>
                        @if($user->inn)
                            <div class="profile-data">
                                <div class="profile-data__ttl">ИНН</div>
                                <div class="profile-data__value">{{ $user->inn }}</div>
                            </div>
                        @endif
                        @if($user->kpp)
                            <div class="profile-data">
                                <div class="profile-data__ttl">КПП</div>
                                <div class="profile-data__value">{{ $user->kpp }}</div>
                            </div>
                        @endif
                        @if($user->okpo)
                            <div class="profile-data">
                                <div class="profile-data__ttl">ОКПО</div>
                                <div class="profile-data__value">{{ $user->okpo }}</div>
                            </div>
                        @endif
                        @if($user->legal_address)
                            <div class="profile-data">
                                <div class="profile-data__ttl">Юридический адрес</div>
                                <div class="profile-data__value">{{ $user->legal_address }}
                                </div>
                            </div>
                        @endif
                    </div>
                </section>
            @endif
        </div>
        <div class="overlay js-overlay-modal"></div>
    </div>
@endsection
