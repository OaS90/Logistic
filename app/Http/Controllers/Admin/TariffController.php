<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tariff\TariffRegionCategoriesPrices;
use App\Infrastructure\Repositories\Admin\TariffRepository;
use App\Infrastructure\Repositories\RegionRepository;
use App\Infrastructure\Admin\Services\Tariff\TariffService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class TariffController extends Controller
{
    protected TariffRepository $tariffRepo;
    protected RegionRepository $regionRepo;
    protected TariffService $tariffService;

    public function __construct(TariffRepository $tariffRepo, RegionRepository $regionRepo, TariffService $tariffService)
    {
        $this->tariffRepo = $tariffRepo;
        $this->regionRepo = $regionRepo;
        $this->tariffService = $tariffService;
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

    public function create(Request $request): string
    {
        try {
            $this->tariffRepo->create($request->all());
        } catch (\Throwable $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return route('tariff-list');
    }

    public function edit(int $tariffId): View
    {
        $tariff = $this->tariffRepo->findByIdWithRelationships($tariffId, ['regions']);
        $regions = $this->regionRepo->getAll();

        return view(backpack_view('tariff.tariff-edit'), ['tariff' => $tariff, 'regions' => $regions]);
    }

    public function delete(int $tariffId): Response
    {
        try {
            $this->tariffService->deleteTariff($tariffId);
        } catch (\Throwable $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return back();
    }

    public function regionEditShow(int $tariffId, int $regionId): View
    {
        $data = $this->tariffService->prepareForVue($tariffId, $regionId);

        return view(backpack_view('tariff.tariff-region-edit'), [
                'regionId' => $regionId,
                'tariffId' => $tariffId,
                'categories' => $data['categories'],
                'zones' => $data['zones']
            ]
        );
    }

    public function addRegions(int $tariffId, Request $request): Response
    {
        $regions = $request->all();

        try {
            $this->tariffService->addRegions($tariffId, $regions);
        } catch (\Throwable $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(['message' => 'Регион успешно добавлен'], Response::HTTP_OK);
    }

    public function addAllRegions(int $tariffId): Response
    {
        try {
            $this->tariffService->addAllRegions($tariffId);
        } catch (\Throwable $e) {
            return response(['message' => 'Ошибка добавления регионов'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(['message' => 'Регионы успешно добавлены'], Response::HTTP_OK);
    }

    public function deleteRegion(int $tariffId, int $regionId): Response
    {
        try {
            $this->tariffService->deleteRegion($tariffId, $regionId);
        } catch (\Throwable $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(['message' => 'Регион успешно удалён'], Response::HTTP_OK);
    }

    public function regionSave(int $tariffId, int $regionId, TariffRegionCategoriesPrices $request): Response
    {
        try {
            $this->tariffService->saveRegionCategoriesPrices($tariffId, $regionId, $request->getDTO());
        } catch (\Throwable $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(['message' => 'Настройки сохранены'], Response::HTTP_OK);
    }

    public function zoneDelete(int $tariffId, int $regionId, Request $request): Response
    {
        try {
            $this->tariffService->deleteZone($tariffId, $regionId, $request->all());
        } catch (\Throwable $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(['message' => 'Зона успешно удалена'], Response::HTTP_OK);
    }

    public function cloneTariff(int $tariffId): RedirectResponse
    {
        $this->tariffService->cloneTariff($tariffId);

        return back();
    }
}
