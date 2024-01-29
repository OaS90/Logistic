@extends(backpack_view('blank'))

@section('content')
    <h1>Настройки Складов и ТК</h1>

    <hr>
    <div id="admin-app">
        <transport-company-settings :warehouses="{{ $warehouses }}" :guest="@json($guest)"></transport-company-settings>
    </div>


@endsection
@push('after_scripts')
    <script src="{{ asset('js/app.js') }}" type="application/javascript"></script>
@endpush