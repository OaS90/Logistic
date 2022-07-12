<p>Регион: {{ $quote->region->name }}</p>
<p>Общая квота: {{ $quote->quote }}</p>
@if($quote->tmp_quote)
    <p>Временная квота: {{ $quote->tmp_quote }} с
        {{ date_format(date_create($quote->available_from_date), 'd.m.Y') }} по
        {{ date_format(date_create($quote->available_until_date), 'd.m.Y') }}</p>
@endif
@foreach($quote->intervals as $interval)
    @if(in_array($interval->period, ['inDay', 'inHour']))
        <p>{{ $interval->period == 'inDay' ? 'День в день' : 'Доставка в указанный час' }}
            - {{ $interval->active ? 'вкл.' : 'выкл.' }}</p>
    @else
        <p>Интервал {{ $interval->period }} - {{ $interval->percent ?? 0 }}
            % {{ $interval->active ? 'вкл.' : 'выкл.' }}</p>
    @endif
@endforeach
