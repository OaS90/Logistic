@extends('layouts.app')
@section('title', 'Регистрация клиента')
@section('content')
    <div class="main-wrapper">
        <header class="main-header">
            <div class="logo">
                <a href="/"><img src="{{ basset('images/logo.png') }}" alt="Транспорт Логистика"></a>
            </div>
            <div class="contacts">
                <span class="contacts__text">Служба поддержки клиентов:</span>
                <a href="tel:+79262910083">+7(926)291-00-83</a>
            </div>
        </header>
        <div style="width: 50%; margin: 0 auto; padding-bottom: 20px">
            <register-form></register-form>
        </div>
    </div>
@endsection
