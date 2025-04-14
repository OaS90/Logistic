@extends(backpack_view('blank'))

@section('content')
    <div id="admin-app">
        <support-apps></support-apps>
    </div>
@endsection

@push('after_scripts')
    @vite(['resources/js/app.js'])
@endpush
