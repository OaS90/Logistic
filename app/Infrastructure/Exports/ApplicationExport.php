<?php

namespace App\Infrastructure\Exports;

use App\Models\Application;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Domain\ExcelEntity;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;

class ApplicationExport implements FromQuery, WithHeadings, WithCustomCsvSettings, ExcelEntity, WithMapping
{
    use Exportable;

    protected $appId;
    protected $app;

    public function __construct($app)
    {
        $this->appId = $app->id;
        $this->app = $app;
    }

    public function query()
    {
        return Application::where('id', $this->appId);
    }

    public function getCsvSettings(): array
    {
        return [
            'output_encoding' => 'windows-1251',
            'delimiter' => ';'
        ];
    }

    public function map($row): array
    {
        return [
            $row->order_number,
            $row->product_name,
            $row->product_art,
            $row->product_brand,
            $row->payment_type,
            $row->vat,
            $row->cost,
            $row->width,
            $row->height,
            $row->depth,
            $row->count,
            $row->volume,
            $row->weight,
            $row->warehouse_address,
            $row->delivery_date,
            $row->delivery_time,
            $row->delivery_address,
            $row->flat,
            $row->floor,
            $row->entrance,
            $row->postcode,
            $row->elevator,
            $row->comment,
            $row->client_name,
            $row->client_phone,
        ];
    }

    public function headings(): array
    {
        return [
            'order_number',
            'product_name',
            'product_art',
            'product_brand',
            'payment_type',
            'vat',
            'cost',
            'width',
            'height',
            'depth',
            'count',
            'volume',
            'weight',
            'warehouse_address',
            'delivery_date',
            'delivery_time',
            'delivery_address',
            'flat',
            'floor',
            'entrance',
            'postcode',
            'elevator',
            'comment',
            'client_name',
            'client_phone',
        ];
    }

    public function fileName(): string
    {
        return 'application_' . $this->appId . '_' . $this->app->order_number . '.csv';
    }
}
