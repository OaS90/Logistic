<style>
    .sticker { font-family: DejaVu Sans, sans-serif; }
</style>

@for($i = 1; $i <= $application->count; $i++)
<div style="border: 1px solid black; width: 80mm; height: 120mm; padding: 10px; text-align: center" class="sticker">
    <p><b>Холодильник.Ру</b></p>
    <p>{{ $application->order_number }}</p>
    <p class="item-name">{{ $application->product_name }}</p>
    <p class="client">{{ $application->client_name }}</p>
    <p class="address">{{ $application->delivery_address }}</p>
    <p class="warehouse-code">{{ $application->warehouse_address }}</p>
    <p class="offers">{{ '1/' . $i }}</p>
    <div style="height: 90px">
        {!! $code !!}
    </div>

</div>
@endfor
