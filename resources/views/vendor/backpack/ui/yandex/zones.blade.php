@extends(backpack_view('blank'))

@section('content')
    <div id="admin-app">
        <h1>Настрока зон</h1>

        <yandex-zones :zones="{{  json_encode($zones) }}"
                      :polygon-types="{{  json_encode($polygon_types) }}"
                      :filials="{{ json_encode($filials) }}"
                      :regions="{{ json_encode($regions) }}"
                      :exports-history="{{ json_encode($exports_history_files) }}"
        ></yandex-zones>
    </div>
@endsection

@push('after_scripts')
    @vite(['resources/js/app.js'])
@endpush
