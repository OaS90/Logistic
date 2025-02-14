<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Exceptions\ApplicationNotFoundException;
use App\Http\Controllers\Api\Exceptions\PartnerApplicationsNotFoundException;
use App\Http\Controllers\Api\Exceptions\PartnerNotFoundException;
use App\Http\Controllers\Api\Exceptions\UserNotFoundException;
use App\Http\Controllers\Api\Exceptions\WarehouseNotFoundException;
use App\Http\Requests\Application\ApplicationApiCreateRequest;
use App\Http\Requests\Application\StatusesFrom1cRequest;
use App\Http\Requests\GetApplicationStatusRequest;
use App\Http\Requests\Partner\PartnerGetOrdersRequest;
use App\Http\Resources\ApplicationApiCreateResource;
use App\Http\Resources\ApplicationApiUpdatedStatusesResource;
use App\Http\Resources\ApplicationStatusHistoryResource;
use App\Http\Resources\GetOrdersFor1cResource;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\AppStatusHistoryRepository;
use App\Infrastructure\Repositories\UserRepository;
use App\Infrastructure\Services\Application\ApplicationService;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Контроллер для обработки api запросов из 1с и партнёров
 */
class ApplicationController
{
    public function __construct(private readonly UserRepository $userRepo,
                                private readonly ApplicationService $appService,
                                private readonly AppStatusHistoryRepository $appStatusHistoryRepo,
                                private readonly ApplicationRepository $repo
    )
    {}

    public function setStatus(StatusesFrom1cRequest $request): Response|ApplicationApiUpdatedStatusesResource
    {
        try {
            $DTOs = $request->getDTOs();
            $statuses = $this->appService->updateStatuses($DTOs);
        } catch (\Throwable $e) {
            Log::error('Updating statuses error ' . $e->getMessage());

            return response(['success' => false, 'message' => 'Ошибка обновления статусов'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return new ApplicationApiUpdatedStatusesResource($statuses);
    }

    public function create(ApplicationApiCreateRequest $request): Response|ApplicationApiCreateResource
    {
        try {
            $DTOs = $request->getDTOs();
            $createdApps = $this->appService->createByApi($DTOs);
        } catch (UserNotFoundException $e) {
            return $e->render();
        } catch (WarehouseNotFoundException $e) {
            return $e->render();
        } catch (\Throwable $e) {
            Log::error('Creating apps error: ' . $e->getMessage());

            return response(['success' => false, 'message' => 'Ошибка создания заявки(ок)'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new ApplicationApiCreateResource($createdApps);
    }

    public function getSticker(string $partnerOrderId): PDF|Response
    {
        try {
            $app = $this->repo->getByOrderNumber($partnerOrderId);

            if (!$app) {
                throw new ApplicationNotFoundException($partnerOrderId);
            }

            $stickers = $this->appService->makeStickers($app);
        } catch (ApplicationNotFoundException $e) {
            return $e->render();
        } catch (\Throwable $e) {
            Log::error('Get sticker error ' . $e->getMessage());

            return response(['success' => false, 'message' => 'Ошибка получения штрих-кода'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $stickers instanceof PDF ? $stickers->download() : $stickers;
    }

    public function getOrderStatus(GetApplicationStatusRequest $request): Response
    {
        $orderNumber = $request->get('orderId');
        $partnerId = $request->get('partnerId');

        try {
            $partner = $this->userRepo->getBy1cId($partnerId);

            if (!$partner) {
                throw new PartnerNotFoundException($partnerId);
            }

            $app = $this->userRepo->getOrderByNumberAndUser1cId($partnerId, $orderNumber);

            if (!$app) {
                throw new ApplicationNotFoundException($orderNumber);
            }

        } catch (PartnerNotFoundException $e) {
            return $e->render();
        } catch (ApplicationNotFoundException $e) {
            return $e->render();
        } catch (\Throwable $e) {
            Log::error('Get order status error: ' . $e->getMessage());

            return response(['success' => false, 'message' => 'Ошибка получения статуса'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json([
            'message' => 'success',
            'orderNumber' => $app->order_number,
            'orderStatus' => $app->status
        ]);
    }

    public function getAppStatusHistory(Request $request): Response|ApplicationStatusHistoryResource
    {
        try {
            $statuses = $this->appStatusHistoryRepo->getByFewOrders($request->get('ids'));
        } catch (\Throwable $e) {
            Log::error('Get statuses history error ' . $e->getMessage());

            return response(['message' => 'Ошибка получения истории'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new ApplicationStatusHistoryResource($statuses);
    }

    public function getOrders(PartnerGetOrdersRequest $request): Response|GetOrdersFor1cResource
    {
        try {
            $result = $this->appService->getOrdersBy1c($request->get('partnerId'));
        } catch (PartnerNotFoundException $e) {
            return $e->render();
        } catch (PartnerApplicationsNotFoundException $e) {
            return $e->render();
        } catch (\Throwable $e) {
            dd($e->getTraceAsString());
            Log::error('Get partner order error ' . $e->getMessage());

            return response(['success' => false, 'message' => 'Ошибка получения заказов']);
        }

        return new GetOrdersFor1cResource($result);
    }
}
