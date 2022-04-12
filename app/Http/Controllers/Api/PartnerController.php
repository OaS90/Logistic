<?php

namespace App\Http\Controllers\Api;

use App\Domain\PartnerOrderDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Infrastructure\Repositories\ApplicationRepository;

class PartnerController extends Controller
{
    private $repo;

    public function __construct(ApplicationRepository $repository)
    {
        $this->repo = $repository;
    }

    public function getOrders(Request $request): \Illuminate\Http\JsonResponse
    {
        $partnerId = $request->get('partnerId');
        $applications = $this->repo->getListByUserId($partnerId);

        if ($applications->count() == 0)
            return response()->json(['message' => 'Не найдено заявок для клиента с id=' . $partnerId],200);

        $apps = [];

        foreach ($applications as $app) {
            $apps[] = (new PartnerOrderDTO($app->toArray()))->make();
        }

        return response()->json($apps, 200);
    }
}
