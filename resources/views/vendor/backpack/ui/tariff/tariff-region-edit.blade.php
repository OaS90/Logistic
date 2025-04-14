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
                :author-id="{{ $authorId }}"
                :user-id="{{ $userId }}"
                :is-admin="{{ json_encode($isAdmin) }}"
                :is-editable="{{ json_encode($isEditable) }}"
        ></tariff-region-settings>
    </div>
@endsection

@push('after_scripts')
    @vite(['resources/js/app.js'])
@endpush
