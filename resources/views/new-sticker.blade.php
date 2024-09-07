<style>
    .sticker {
        font-family: DejaVu Sans, sans-serif;
    }
</style>
<div>
    @for($i = 0; $i < count($barcodes); $i++)
        <div style="height: 1px"></div>
        <div style="height: 1px; width: 100%; text-align: center; top: -60px; position:absolute; font-size: 24px">
            <p>{{ $barcodes[$i]['code'] }}</p>
        </div>
        <div class="sticker">
            {!! $barcodes[$i]['barcode'] !!}
            <div style="height: 1px; width: 100%; text-align: center; margin-top: -10px; position:absolute;">
                <p>{!! $barcodes[$i]['productName'] !!}</p>
            </div>
        </div>
    @endfor

</div>