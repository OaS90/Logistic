<table>
    <tr>
        <td>Регион</td>
        <td>Код Склада</td>
        <td>Склад</td>
        <td>Задержка дней</td>
        <td>Квота</td>
        <td>Настройки ТК</td>
    </tr>

    @foreach($warehouses as $warehouse)
        <tr>
            <td>{{ $warehouse->region ?->name }}</td>
            <td>{{ $warehouse->code }}</td>
            <td>{{ $warehouse->name }}</td>
            <td>{{ $warehouse->delay_days }}</td>
            <td>{{ $warehouse->quote }}</td>
            <td>
                <table>
                    <tr>
                        <td>Наименование</td>
                        <td>Вкл./Выкл</td>
                        <td>Ограничение по времени</td>
                        <td>Дни отгрузки</td>
                    </tr>
                    @if ($warehouse->tcSettings)
                        @foreach($warehouse->tcSettings as $setting)
                            <tr>
                                <td>{{ $setting->tc->name }}</td>
                                <td>{{ $setting->enabled ? 'Вкл.' : 'Выкл.' }}</td>
                                <td>{{ $setting->last_time }}</td>
                                <td>{{ $setting->days_name }}</td>
                            </tr>
                        @endforeach
                    @endif
                </table>
            </td>
        </tr>
    @endforeach
</table>