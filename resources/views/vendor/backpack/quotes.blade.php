@extends(backpack_view('blank'))

@section('content')
    <div id="admin-app">
        <h1>Квоты</h1>

        <hr>

        <quotes-table :quotes="{{ $quotes }}"></quotes-table>
{{--        <example-component></example-component>--}}
    </div>
@endsection
@push('after_scripts')
    <script src="{{ asset('js/app.js') }}" type="application/javascript"></script>
@endpush

