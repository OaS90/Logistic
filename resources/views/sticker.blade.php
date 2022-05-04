<style>
    .sticker { font-family: DejaVu Sans, sans-serif; }
</style>
<div style="position:relative;">
    @for($i = 1; $i <= count($products); $i++)
        <div style="border: 1px solid black; width: 80mm; height: 85mm; padding: 8px; position: relative" class="sticker">
            <div><b>Холодильник.Ру</b></div>
            <div style="font-size: 12px">{{ $application->order_number . '-LG-'  . $i}}</div>
            <p class="address" style="font-size: 12px">{{ $application->full_address }}</p>
            <div class="client"><b>{{ $application->client_name }}</b></div>
            <div class="warehouse-code">{{ $application->warehouse->address }}</div>
            <div style="position: absolute; bottom: 10px; width: 95%; text-align: center; font-size: 12px">
                <p>{{ $application->full_address . ',  Тел. ' . $application->client_phone }}</p>
                    <hr style="margin-top: 10px;">
                    {{
                        $products[$i -1]->width . ' x ' . $products[$i -1]->height . ' x ' . $products[$i -1]->depth . ' мм | ' .
                        $products[$i -1]->count . ' экз.' . $products[$i -1]->weight . 'г'
                    }}

            </div>
        </div>
        <div style="width: 85mm; height: 110px; position: absolute; bottom: -80px;">
            {!! $codes[$i - 1] !!}
        </div>
    </div>
@endfor
