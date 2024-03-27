@extends(backpack_view('blank'))

@section('content')
    <div id="admin-app">
        <support-apps></support-apps>
    </div>
@endsection

@push('after_scripts')
    <script src="{{ asset('js/app.js') }}" type="application/javascript"></script>
@endpush