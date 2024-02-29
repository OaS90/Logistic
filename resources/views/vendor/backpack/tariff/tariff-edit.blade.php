@extends(backpack_view('blank'))

@section('content')
    <h2><span class="text-capitalize">Тариф "{{ $tariff->name }}"</span></h2>

    <hr>

    <div id="admin-app">
        <tariff-regions :tariff-regions="{{ $tariff->regions }}"
                        :regions="{{ $regions }}"
                        :tariff-id="{{ $tariff->id }}"
        >
        </tariff-regions>
    </div>
@endsection

@push('after_scripts')
    <script src="{{ asset('js/app.js') }}" type="application/javascript"></script>
@endpush
