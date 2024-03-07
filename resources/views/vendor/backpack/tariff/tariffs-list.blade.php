@extends(backpack_view('blank'))

@section('content')
    <div id="admin-app">
    <h2><span class="text-capitalize">Тарифы</span></h2>
        <div class="row">
            <div class="col-sm-2">
                <div class="d-print-none with-border">
                    <a href="{{ backpack_url('tariffs/show') }}"
                       class="btn btn-primary"
                    >
                        <span class="ladda-label"><i class="la la-plus"></i> Добавить Тариф</span>
                    </a>
                </div>
            </div>

            <tariffs-get-from-service-button></tariffs-get-from-service-button>

            <div class="col-sm-7">
                <div id="datatable_search_stack" class="mt-sm-0 mt-2 d-print-none"><div id="crudTable_filter" class="dataTables_filter"><label><input type="search" class="form-control" placeholder="Поиск..." aria-controls="crudTable"></label></div></div>
            </div>
        </div>

        <table id="warehouses" class="table-content bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline collapsed has-hidden-columns">
            <thead>
            <tr>
                <th>ID</th>
                <th>Наименование</th>
                <th>Алиас</th>
                <th>Действие</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tariffs as $tariff)
                <tr>
                    <td>{{ $tariff->id }}</td>
                    <td>{{ $tariff->name }}</td>
                    <td>{{ $tariff->alias }}</td>
                    <td>
                        <a href="{{ backpack_url('tariffs/' . $tariff->id . '/edit') }}" class="btn btn-sm btn-link">
                            <i class="la la-eye"></i> Редактировать
                        </a>
                        <a href="{{ backpack_url('tariffs/' . $tariff->id . '/clone') }}" class="btn btn-sm btn-link"><i class="la la-clone"></i> Клонировать</a>
                        <a href="{{ backpack_url('tariffs/' . $tariff->id . '/delete') }}" class="btn btn-sm btn-link"><i class="la la-trash"></i> Удалить</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('after_scripts')
    <script src="{{ asset('js/app.js') }}"></script>
@endsection