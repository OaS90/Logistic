{{-- tariff_permissions_field field --}}
@php
    $field['value'] = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));
@endphp

@include('crud::fields.inc.wrapper_start')
{{--    <label>{!! $field['label'] !!}</label>--}}
    @include('crud::fields.inc.translatable_icon')

    <div id="admin-app">
        <user-tariff-permissions :tariffs="{{ $field['value']['tariffs'] }}"
                                 :allowed-tariffs="{{ $field['value']['allowedTariffs'] }}"
                                 :user-id="{{ $field['value']['userId'] }}"
        ></user-tariff-permissions>
    </div>

@include('crud::fields.inc.wrapper_end')

{{-- CUSTOM CSS --}}
@push('crud_fields_styles')
    {{-- How to load a CSS file? --}}
    @loadOnce('tariff_permissionsFieldStyle.css')

    {{-- How to add some CSS? --}}
    @loadOnce('tariff_permissions_field_style')
        <style>
            .tariff_permissions_field_class {
                display: none;
            }
        </style>
    @endLoadOnce
@endpush

{{-- CUSTOM JS --}}
{{--@push('crud_fields_scripts')--}}
{{--    @loadOnce('js/app.js')--}}
{{--@endpush--}}
@push('crud_fields_scripts')
    @vite(['resources/js/app.js'])
@endpush
