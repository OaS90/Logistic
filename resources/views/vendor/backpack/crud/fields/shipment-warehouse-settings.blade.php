@php
    // Подготовка значения (если null, то пустой массив/объект json)
    $value = old($field['name']) ?? $field['value'] ?? $field['default'] ?? [];
    // Если значение пришло массивом (из $casts), кодируем в JSON для атрибута

    $valueJson = is_string($value) ? $value : json_encode($value);
@endphp

@include('crud::fields.inc.wrapper_start')
@include('crud::fields.inc.translatable_icon')

<div id="admin-app">
    <shipment-warehouse-settings
        :initial-value="{{ json_encode($value) }}"
    >
    </shipment-warehouse-settings>
    <input type="text"
           hidden
           id="hiddenInput"
           name="{{ $field['name'] }}"
           value="{{ $valueJson }}"
        @include('crud::fields.inc.attributes')>
</div>

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')

{{-- CUSTOM CSS --}}
@push('crud_fields_styles')
    {{-- How to load a CSS file? --}}
    @loadOnce('shipment-warehouse-settingsFieldStyle.css')

    {{-- How to add some CSS? --}}
    @loadOnce('shipment-warehouse-settings_style')
    <style></style>
    @endLoadOnce
@endpush
@push('crud_fields_scripts')
    @vite(['resources/js/app.js'])
@endpush
