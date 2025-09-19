@extends(backpack_view('blank'))

@section('content')
    <div id="app">
        <h2>Страница валидации тарифов</h2>
        <h4>Если вас перекинуло на эту страницу и в таблице ниже есть какие-то данные,
            то их нужно заполнить, перейдя по ссылке
        </h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Тариф</th>
                    <th>Регион</th>
                    <th>Категория</th>
                    <th>Ссылка</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prices as $price)
                    <tr>
                        <td>{{ $price['tariff_name'] }}</td>
                        <td>{{ $price['region_name'] }}</td>
                        <td>{{ $price['category_name'] }}</td>
                        <td><a target="_blank" href="{{ $price['uri']}}">Перейти</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection
