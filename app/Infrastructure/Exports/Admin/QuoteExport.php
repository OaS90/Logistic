<?php

namespace App\Infrastructure\Exports\Admin;

use App\Domain\ExcelEntity;
use App\Models\Quote;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class QuoteExport implements FromView, ExcelEntity
{
    public function view(): View
    {
        return view('vendor.backpack.Exports.quotes', [
            'quotes' => Quote::all()
        ]);
    }
}
