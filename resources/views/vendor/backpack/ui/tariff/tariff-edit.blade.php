@extends(backpack_view('blank'))

@section('content')
    <div id="admin-app">
        <h2><span class="text-capitalize">Настройки тарифа</span></h2>

        <hr>

            <tariff-name-alias-form
                    :tariff="{{ $tariff }}"
                    :is-author="{{ json_encode($isAuthor) }}"
                    :user-id="{{ $userId }}"
                    :is-admin="{{ json_encode($isAdmin) }}"
                    :is-editable="{{ json_encode($isEditable) }}"
            ></tariff-name-alias-form>

        <hr>


        <tariff-regions :tariff-regions="{{ $tariff->regions }}"
                        :regions="{{ $regions }}"
                        :tariff-id="{{ $tariff->id }}"
                        :user-id="{{ $userId }}"
                        :tariff-author="{{ $tariff->author_id }}"
                        :is-admin="{{ json_encode($isAdmin) }}"
                        :is-editable="{{ json_encode($isEditable) }}"
        >
        </tariff-regions>
    </div>
@endsection

@push('after_scripts')
    @vite(['resources/js/app.js'])
@endpush
