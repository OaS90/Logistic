<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tariff\TariffRegionCategoriesPrices;
use App\Infrastructure\Repositories\Admin\TariffRepository;
use App\Infrastructure\Repositories\Admin\UserTariffPermissionRepo;
use App\Infrastructure\Repositories\RegionRepository;
use App\Infrastructure\Admin\Services\Tariff\TariffService;
use App\Mail\TariffPermissionRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class TariffController extends Controller
{
    protected TariffRepository $tariffRepo;
    protected RegionRepository $regionRepo;
    protected UserTariffPermissionRepo $permissionRepo;

    public function __construct(TariffRepository $tariffRepo,
                                RegionRepository $regionRepo,
                                UserTariffPermissionRepo $permissionRepo
    )
    {
        $this->tariffRepo = $tariffRepo;
        $this->regionRepo = $regionRepo;
        $this->permissionRepo = $permissionRepo;
    }

    public function list(): View
    {
        $tariffs = $this->tariffRepo->getAll();
        $isAdmin = backpack_user()->hasRole('admin');
        $data = [];

        foreach ($tariffs as $tariff) {
            $userId = backpack_user()->id;
            $isEditable = $this->permissionRepo->getByUserIdAndTariffId($userId, $tariff->id);

            $data[] = [
                'id' => $tariff->id,
                'name' => $tariff->name,
                'alias' => $tariff->alias,
                'isEditable' => $isEditable,
                'isAuthor' => $userId == $tariff->author_id
            ];
        }

        return view(backpack_view('tariff.tariffs-list'), ['tariffs' => $data, 'isAdmin' => $isAdmin]);
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
        $userId = backpack_user()->id;
        $isEditable = $this->permissionRepo->getByUserIdAndTariffId($userId, $tariffId);

        return view(backpack_view('tariff.tariff-edit'), [
            'tariff' => $tariff,
            'regions' => $regions,
            'userId' => $userId,
            'isAdmin' => backpack_user()->hasRole('admin'),
            'isEditable' => (bool) $isEditable
        ]);
    }

    public function delete(int $tariffId, TariffService $service): Response
    {
        try {
            $service->deleteTariff($tariffId);
        } catch (\Throwable $e) {
            Log::error('Deleting tariff error: ' . $e->getMessage());
        }

        return response(['message' => 'Тариф успешно удалён!'], Response::HTTP_OK);
    }

    public function regionEditShow(int $tariffId, int $regionId, TariffService $service): View
    {
        $data = $service->prepareForVue($tariffId, $regionId);
        $userId = backpack_user()->id;
        $isEditable = $this->permissionRepo->getByUserIdAndTariffId($userId, $tariffId);

        return view(backpack_view('tariff.tariff-region-edit'), [
                'regionId' => $regionId,
                'tariffId' => $tariffId,
                'categories' => $data['categories'],
                'zones' => $data['zones'],
                'authorId' => $data['authorId'],
                'userId' => $userId,
                'isAdmin' => backpack_user()->hasRole('admin'),
                'isEditable' => (bool) $isEditable
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

    /**
     * @throws \Exception
     */
    public function cloneTariff(int $tariffId, TariffService $service): Response
    {
        try {
            $service->cloneTariff($tariffId);
        } catch (\Throwable $e) {
            Log::error('Clone error ' . $e->getMessage());

            return response(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(['uri' => 'tariffs'], 200);
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

    public function getAll(): Collection
    {
        return $this->tariffRepo->getAll();
    }
}
