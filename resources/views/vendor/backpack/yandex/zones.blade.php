@extends(backpack_view('blank'))

@section('content')
    <div id="admin-app">
        <h1>Настрока зон</h1>

        <yandex-zones></yandex-zones>
    </div>
@endsection

@push('after_scripts')
    <script src="{{ asset('js/app.js') }}" type="application/javascript"></script>
@endpush
