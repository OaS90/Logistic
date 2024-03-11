<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tariff\TariffRegionCategoriesPrices;
use App\Infrastructure\Repositories\Admin\TariffRepository;
use App\Infrastructure\Repositories\RegionRepository;
use App\Infrastructure\Admin\Services\Tariff\TariffService;
use App\Mail\TariffPermissionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class TariffController extends Controller
{
    protected TariffRepository $tariffRepo;
    protected RegionRepository $regionRepo;

    public function __construct(TariffRepository $tariffRepo, RegionRepository $regionRepo)
    {
        $this->tariffRepo = $tariffRepo;
        $this->regionRepo = $regionRepo;
    }

    public function list(): View
    {
        $tariffs = $this->tariffRepo->getAll();

        return view(backpack_view('tariff.tariffs-list'), ['tariffs' => $tariffs]);
    }

    public function show(): View
    {
        return view(backpack_view('tariff.tariff-create'));
    }

    public function create(Request $request, TariffService $service): string
    {
        try {
            $data = [
                'name' => $request->get('name'),
                'alias' => $request->get('alias'),
                'author_id' => backpack_user()->id
            ];
            $service->create($data);
        } catch (\Throwable $e) {
            Log::error('Tariff creatign error:' . $e->getMessage());
//            return response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return route('tariff-list');
    }

    public function edit(int $tariffId): View
    {
        $tariff = $this->tariffRepo->findByIdWithRelationships($tariffId, ['regions']);
        $regions = $this->regionRepo->getAll();

        return view(backpack_view('tariff.tariff-edit'), [
            'tariff' => $tariff,
            'regions' => $regions,
            'userId' => backpack_user()->id
        ]);
    }

    public function delete(int $tariffId, TariffService $service): Response
    {
        try {
            $service->deleteTariff($tariffId);
        } catch (\Throwable $e) {
            Log::error('Deleting tariff error: ' . $e->getMessage());
        }

        return back();
    }

    public function regionEditShow(int $tariffId, int $regionId, TariffService $service): View
    {
        $data = $service->prepareForVue($tariffId, $regionId);

        return view(backpack_view('tariff.tariff-region-edit'), [
                'regionId' => $regionId,
                'tariffId' => $tariffId,
                'categories' => $data['categories'],
                'zones' => $data['zones'],
                'authorId' => $data['authorId'],
                'userId' => backpack_user()->id
            ]
        );
    }

    public function addRegions(int $tariffId, Request $request, TariffService $service): Response
    {
        $regions = $request->all();

        try {
            $service->addRegions($tariffId, $regions);
        } catch (\Throwable $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(['message' => 'Регион успешно добавлен'], Response::HTTP_OK);
    }

    public function addAllRegions(int $tariffId, TariffService $service): Response
    {
        try {
            $service->addAllRegions($tariffId);
        } catch (\Throwable $e) {
            return response(['message' => 'Ошибка добавления регионов'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(['message' => 'Регионы успешно добавлены'], Response::HTTP_OK);
    }

    public function deleteRegion(int $tariffId, int $regionId, TariffService $service): Response
    {
        try {
            $service->deleteRegion($tariffId, $regionId);
        } catch (\Throwable $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(['message' => 'Регион успешно удалён'], Response::HTTP_OK);
    }

    public function regionSave(int $tariffId,
                               int $regionId,
                               TariffRegionCategoriesPrices $request,
                               TariffService $service
    ): Response
    {
        try {
            $service->saveRegionCategoriesPrices($tariffId, $regionId, $request->getDTO());
        } catch (\Throwable $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(['message' => 'Настройки сохранены'], Response::HTTP_OK);
    }

    public function zoneDelete(int $tariffId, int $regionId, Request $request, TariffService $service): Response
    {
        try {
            $service->deleteZone($tariffId, $regionId, $request->all());
        } catch (\Throwable $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(['message' => 'Зона успешно удалена'], Response::HTTP_OK);
    }

    public function cloneTariff(int $tariffId, TariffService $service): RedirectResponse
    {
        $service->cloneTariff($tariffId);

        return back();
    }

    public function getFromService(TariffService $service): Response
    {
        try {
            $service->getFromDeliveryService();
        } catch (\Throwable $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(['message' => 'Тарифы получены'], Response::HTTP_OK);
    }

    public function permissionsRequest(int $tariffId)
    {
        $tariff = $this->tariffRepo->findById($tariffId);
        $user = backpack_user();
        Mail::to(config('tariff_emails'))->send(new TariffPermissionRequest($tariff, $user));
    }
}
