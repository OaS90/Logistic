<?php

namespace App\Http\Controllers\Admin;

use App\Infrastructure\Repositories\Admin\AdminUserRepository;
use App\Infrastructure\Repositories\Admin\TariffRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminUserTariffsController
{
    public function addTariff(Request $request,
                              AdminUserRepository $userRepo,
                              TariffRepository $tariffRepo
    ): Response
    {
        $tariffId = $request->get('tariffId');
        $userId = $request->get('userId');
        $user = $userRepo->getById($userId);
        $tariff = $tariffRepo->findById($tariffId);
        $user->tariffsPermissions()->attach($tariff);

        return response(['message' => 'Тариф добавлен для редактирования'], Response::HTTP_OK);
    }

    public function removeTariff(Request $request,
                                 AdminUserRepository $userRepo,
                                 TariffRepository $tariffRepo
    ): Response
    {
        $tariffId = $request->get('tariffId');
        $userId = $request->get('userId');
        $user = $userRepo->getById($userId);
        $tariff = $tariffRepo->findById($tariffId);
        $user->tariffsPermissions()->detach($tariff);

        return response(['message' => 'Тариф удлён'], Response::HTTP_OK);
    }
}
