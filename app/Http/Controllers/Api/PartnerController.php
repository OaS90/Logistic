<?php

namespace App\Http\Controllers\Api;

use App\Domain\PartnerOrderDTO;
use App\Http\Controllers\Controller;
use App\Infrastructure\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Infrastructure\Repositories\ApplicationRepository;

class PartnerController extends Controller
{
    private ApplicationRepository $repo;
    private UserRepository $userRepo;

    public function __construct(ApplicationRepository $repository, UserRepository $userRepo)
    {
        $this->repo = $repository;
        $this->userRepo = $userRepo;
    }

    public function getOrders(Request $request): JsonResponse
    {
        $partnerId = $request->get('partnerId');
        $user = $this->userRepo->getBy1cId($partnerId);

        if ($user) {
            $applications = $this->repo->getListByUserId($user->id);

            if ($applications->count() == 0)
                return response()
                    ->json(
                        ['message' => 'Не найдено заявок для клиента с id=' . $partnerId],
                        200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE
                    );

            $apps = [];

            foreach ($applications as $app) {
                if ($app->status == 'created' || $app->doc_ver > $app->old_doc_ver) {
                    $appDTO = (new PartnerOrderDTO($app))->make();
                    $apps[] = $appDTO;
                    $this->repo->updateByFields($app->order_number, ['old_doc_ver' => $app->doc_ver]);
                }
            }

            return response()->json($apps, 200,
                ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE
            );
        }

        return response()
            ->json(
                ['message' => 'Не найден партнёр с id ' . $partnerId],
                200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE
            );
    }
}
