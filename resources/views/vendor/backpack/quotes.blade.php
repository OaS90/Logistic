@extends(backpack_view('blank'))

@section('content')
    <div id="admin-app">
        <h1>Квоты</h1>

        <hr>

        <quotes-table :quotes="{{ $quotes }}" :guest="@json($guest)"></quotes-table>
{{--        <example-component></example-component>--}}
    </div>
@endsection
@push('after_scripts')
    @vite(['resources/js/app.js'])
@endpush


