@extends(backpack_view('blank'))

@section('content')
    <div id="admin-app">
        <h1 style="margin-bottom: 30px" class="ml-5">Email для отправки уведомлений по квотам</h1>

{{--        <hr style="margin-bottom: 20px">--}}

        <quote-emails-table :emails="{{ $emails }}"></quote-emails-table>
    </div>
@endsection
@push('after_scripts')
    <script src="{{ asset('js/app.js') }}" type="application/javascript"></script>
@endpush
