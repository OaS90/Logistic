<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300&family=Montserrat:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="icon" href="{{ basset('images/icon.png') }}" type="image/icon type">
</head>
<body id="main">
    <div id="app">
        @yield('content')
    </div>
{{--    <script src="{{ asset('js/main.js') }}" type="application/javascript"></script>--}}
    @vite('resources/js/app.js')
</body>
</html>
