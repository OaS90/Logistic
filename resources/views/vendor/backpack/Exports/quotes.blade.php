<table>
    <thead>
    <tr>
        <th>Склад</th>
        <th>Регионы России</th>
        <th>Дневная квота</th>
        <th>Временная квота</th>
        <th>Срок действия</th>
        <th colspan="2">10-14</th>
        <th colspan="2">14-18</th>
        <th colspan="2">18-22</th>
        <th>День в день</th>
        <th>Доставка в указанный час</th>
    </tr>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th>%</th>
        <th>Активно</th>
        <th>%</th>
        <th>Активно</th>
        <th>%</th>
        <th>Активно</th>
        <th></th>
        <th></th>
        <th>Пн</th>
        <th>Вт</th>
        <th>Ср</th>
        <th>Чт</th>
        <th>Пт</th>
        <th>Сб</th>
        <th>Вс</th>
        <th>Ограничение по
            времени оформления	</th>
        <th>Часы доставки</th>
    </tr>
    </thead>
    <tbody>
    @foreach($quotes as $quote)
        <tr>
            <td>{{ $quote->region->warehouse->warehouse_name }}</td>
            <td>{{ $quote->region->name }}</td>
            <td>{{ $quote->quote }}</td>
            <td>{{ $quote->tmp_quote }}</td>
            <td>{{ $quote->available_from_date ? $quote->available_from_date . ' - ' . $quote->available_until_date : ''}}</td>
            @if (count($quote->intervals))
                @foreach($quote->intervals as $interval)
                    @if($interval->percent)
                        <td>{{ $interval->percent }}</td>
                        <td>{{ $interval->active ? 'Да' : 'Нет' }}</td>
                    @elseif($interval->period == 'inHour' || $interval->period == 'inDay')
{{--                        <td></td>--}}
                        <td>{{ $interval->active ? 'Да' : '' }}</td>
                    @else
                        <td></td>
                        <td></td>
                    @endif
                @endforeach
            @else
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            @endif
            @if($quote->days)
                @foreach($quote->days as $number => $day)
                    <td>
                        @if($day)
                            вкл.
                        @else
                            выкл.
                        @endif
                    </td>
                @endforeach
            @else
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            @endif
            @if($quote->time_last)
                <td>{{ $quote->time_last }}</td>
            @endif
            @if($quote->delivery_hours)
                <td>с: {{ $quote->delivery_hours['from'] }} по: {{ $quote->delivery_hours['till'] }}</td>
                <td></td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
