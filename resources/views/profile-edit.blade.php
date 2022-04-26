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
            <profile-form :user="{{ $user }}"></profile-form>
        </div>
        <div class="overlay js-overlay-modal"></div>
    </div>
@endsection
