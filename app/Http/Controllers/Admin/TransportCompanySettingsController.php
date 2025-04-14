<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilialTCSaveRequest;
use App\Infrastructure\Admin\Services\TCSettingsService;
use App\Infrastructure\Exports\Admin\TCSettingsExport;
use App\Infrastructure\Repositories\Admin\HruWarehouseRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use App\Application\ExcelExportService;
use Illuminate\Http\Request;

class TransportCompanySettingsController extends Controller
{

    public function __construct(private readonly HruWarehouseRepository $repo,
                                private readonly TCSettingsService $service,
                                private readonly ExcelExportService $exportService
    )
    {
    }

    public function show(): View
    {
        try {
//            $warehouses = $this->repo->getAllWithSetting();
            $isGuest = (bool) backpack_user()->hasRole('guest');
//            $warehousesArray = $this->service->prepareForVue($warehouses);
            $data = $this->service->prepareForVue();

            return view('vendor.backpack.transport_company_settings', [
                'warehouses' => collect($data)->values(), 'guest' => $isGuest
            ]);
        } catch (\Throwable $e) {
            Log::error('Settings view error ' . $e->getMessage());

            return view('errors.500');
        }
    }

    public function save(FilialTCSaveRequest $request, TCSettingsService $service): Response
    {
        $message = 'Данные сохранены. ';

        try {
            $filials = $request->getDTOsArray();
            $result = $service->save($filials);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response(['message' => 'Ошибка сохранения данных.' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (isset($result['status']) && $result['status']) {
            $message .= 'Настройки отправлены на сайт HRU';
        } elseif (isset($result['message'])) {
            $message .= $result['message'];
        } else {
            $message .= 'Ошибка отправки настроек на сайт!';
        }

        return response(['message' => $message], Response::HTTP_OK);
    }

    public function export()
    {
        return $this->exportService->download(new TCSettingsExport($this->repo));
    }
}
