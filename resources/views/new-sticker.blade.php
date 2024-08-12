<style>
    .sticker {
        font-family: DejaVu Sans, sans-serif;
    }
</style>
<div>
    @for($i = 1; $i <= count($barcodes); $i++)
        <div class="sticker">
            {!! $barcodes[$i - 1]['barcode'] !!}
            <div style="height: 1px; width: 100%; text-align: center; margin-top: -10px; position:absolute;">
                <p>{{ $barcodes[$i - 1]['code'] }}</p>
            </div>
        </div>
        <div style="height: 1px"></div>
    @endfor

</div>