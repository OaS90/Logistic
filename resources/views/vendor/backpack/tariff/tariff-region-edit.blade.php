@extends(backpack_view('blank'))

{{--@php--}}
{{--    $breadcrumbs = [--}}
{{--        'Admin' => backpack_url('dashboard'),--}}
{{--        'Dashboard' => false,--}}
{{--    ];--}}
{{--@endphp--}}

@section('content')
    <div id="admin-app">
        <tariff-region-settings
                :tariff-id="{{ $tariffId }}"
                :region-id="{{ $regionId }}"
                :categories="{{ json_encode($categories) }}"
                :zones="{{ json_encode($zones) }}"
        ></tariff-region-settings>
    </div>
@endsection

@push('after_scripts')
    <script src="{{ asset('js/app.js') }}" type="application/javascript"></script>
@endpush