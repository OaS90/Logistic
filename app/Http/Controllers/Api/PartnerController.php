<?php

namespace App\Http\Controllers\Api;

use App\Domain\PartnerOrderDTO;
use App\Http\Controllers\Controller;
use App\Infrastructure\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Infrastructure\Repositories\ApplicationRepository;

class PartnerController extends Controller
{
    private $repo;
    private $userRepo;

    public function __construct(ApplicationRepository $repository, UserRepository $userRepo)
    {
        $this->repo = $repository;
        $this->userRepo = $userRepo;
    }

    public function getOrders(Request $request)
    {
        $partnerId = $request->get('partnerId');
        $user = $this->userRepo->getBy1cId($partnerId);

        if ($user) {
            $applications = $this->repo->getListByUserId($user->id);

            if ($applications->count() == 0)
                return response()->json(['message' => 'Не найдено заявок для клиента с id=' . $partnerId], 200);

            $apps = [];

            foreach ($applications as $app) {
                $appDTO = (new PartnerOrderDTO($app))->make();
                $apps[] = $appDTO;
            }

            return response()->json($apps, 200);
        }

        return response(['message' => 'Не найден партнёр с id ' . $partnerId], 200);
    }
}
