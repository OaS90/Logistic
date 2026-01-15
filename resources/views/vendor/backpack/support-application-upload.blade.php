@extends(backpack_view('blank'))

@section('content')
    <div id="admin-app">
        <support-app-import :partners="{{ json_encode($partners) }}"></support-app-import>
    </div>
@endsection

@push('after_scripts')
    @vite(['resources/js/app.js'])
@endpush
