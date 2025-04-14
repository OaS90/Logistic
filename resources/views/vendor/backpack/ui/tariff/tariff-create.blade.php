@extends(backpack_view('blank'))

@section('content')
    <div id="admin-app">
        <tariff-create></tariff-create>
    </div>
@endsection

@push('after_scripts')
    @vite(['resources/js/app.js'])
@endpush
