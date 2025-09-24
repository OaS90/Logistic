<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Infrastructure\Admin\Services\BranchOffice\BranchOfficeService;
use Illuminate\Http\Request;

class HruFilialController extends Controller
{
    public function setActiveInQuotes(int $id, BranchOfficeService $service, Request $request)
    {
        $service->setActiveInQuotes($id, $request->get('is_active'));
    }

    public function setActiveInTkQuotes(int $id, BranchOfficeService $service, Request $request)
    {
        $service->setActiveInTkQuotes($id, $request->get('is_active'));
    }
}
