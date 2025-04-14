<?php

namespace App\Infrastructure\Exports\Admin;

use App\Domain\ExcelEntity;
use App\Infrastructure\Repositories\Admin\HruWarehouseRepository;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TCSettingsExport implements FromView, ExcelEntity
{
    private HruWarehouseRepository $repo;

    public function __construct(HruWarehouseRepository $repo)
    {
        $this->repo = $repo;
    }

    public function view(): View
    {
        $warehouses = $this->repo->getAllWithSetting();

        return view('vendor.backpack.Exports.tc-settings', [
            'warehouses' => $warehouses
        ]);
    }
}
