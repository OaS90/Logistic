{{-- tariff-regions_field field --}}
@php
    $field['value'] = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));
@endphp

@include('crud::fields.inc.wrapper_start')
    @include('crud::fields.inc.translatable_icon')

    <div id="admin-app">
        <tariff-regions :tariff-regions="{{ $field['value'] }}"
                        :tariff-id="{{ $entry['id'] }}"
                        :regions="{{ $entry->regions }}"
        >
        </tariff-regions>
    </div>
{{--    <input type="text"--}}
{{--        name="{{ $field['name'] }}"--}}
{{--        data-init-function="bpFieldInitDummyFieldElement"--}}
{{--        value="{{ $field['value'] }}"--}}
{{--        @include('crud::fields.inc.attributes')>--}}

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')

{{-- CUSTOM CSS --}}
@push('crud_fields_styles')
    {{-- How to load a CSS file? --}}
    @loadOnce('tariff-regionsFieldStyle.css')

    {{-- How to add some CSS? --}}
    @loadOnce('tariff-regions_field_style')
        <style>
            .tariff-regions_field_class {
                display: none;
            }
        </style>
    @endLoadOnce
@endpush
@push('crud_fields_scripts')
    @vite(['resources/js/app.js'])
@endpush
{{-- CUSTOM JS --}}
{{--@push('crud_fields_scripts')--}}
    {{-- How to load a JS file? --}}
{{--    @loadOnce('tariff-regionsFieldScript.js')--}}
{{--    @loadOnce('js/app.js')--}}
    {{-- How to add some JS to the field? --}}
{{--    @loadOnce('bpFieldInitDummyFieldElement')--}}
{{--    <script>--}}
{{--        function bpFieldInitDummyFieldElement(element) {--}}
{{--            // this function will be called on pageload, because it's--}}
{{--            // present as data-init-function in the HTML above; the--}}
{{--            // element parameter here will be the jQuery wrapped--}}
{{--            // element where init function was defined--}}
{{--            console.log(element.val());--}}
{{--        }--}}
{{--    </script>--}}
{{--    @endLoadOnce--}}
{{--@endpush--}}
