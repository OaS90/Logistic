<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Infrastructure\Admin\Services\TCSettingsService;
use App\Infrastructure\Admin\Services\TCWarehouseService;
use App\Infrastructure\Exports\Admin\TCSettingsExport;
use App\Infrastructure\Repositories\Admin\TransportCompanyWarehouseRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use App\Application\ExcelExportService;
use Illuminate\Http\Request;

class TransportCompanySettingsController extends Controller
{
    private TransportCompanyWarehouseRepository $repo;
    private TCWarehouseService $service;
    private ExcelExportService $exportService;

    public function __construct(TransportCompanyWarehouseRepository $repo,
                                TCWarehouseService $service,
                                ExcelExportService $exportService
    )
    {
        $this->repo = $repo;
        $this->service = $service;
        $this->exportService = $exportService;
    }

    public function show(): View
    {
        try {
            $warehouses = $this->repo->getAllWithSetting();
            $isGuest = (bool) backpack_user()->hasRole('guest');
            $warehousesArray = $this->service->prepareForVue($warehouses);

            return view('vendor.backpack.transport_company_settings', [
                'warehouses' => collect($warehousesArray)->values(), 'guest' => $isGuest
            ]);
        } catch (\Throwable $e) {
            Log::error('Settings view error ' . $e->getMessage());

            return view('errors.500');
        }
    }

    public function save(Request $request, TCSettingsService $service): Response
    {
        $message = 'Данные сохранены. ';

        try {
            $result = $service->save($request->get('settings'));
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
