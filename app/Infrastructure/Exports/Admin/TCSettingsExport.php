<?php

namespace App\Infrastructure\Exports\Admin;

use App\Domain\ExcelEntity;
use App\Infrastructure\Repositories\Admin\TransportCompanyWarehouseRepository;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TCSettingsExport implements FromView, ExcelEntity
{
    private TransportCompanyWarehouseRepository $repo;

    public function __construct(TransportCompanyWarehouseRepository $repo)
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