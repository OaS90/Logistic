<?php
/*
 * Класс для получения заявок партнёров из 1с
 * со статусом "новый" или с разными док. версиями, что означает, что заявку также надо забрать в 1с и обработать
 */
namespace App\Http\Controllers\Api;

use App\Domain\PartnerOrderDTO;
use App\Http\Controllers\Api\Exceptions\PartnerApplicationsNotFoundException;
use App\Http\Controllers\Api\Exceptions\PartnerNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\PartnerGetOrdersRequest;
use App\Http\Resources\GetOrdersFor1cResource;
use App\Infrastructure\Repositories\ApplicationObiRepository;
use App\Infrastructure\Repositories\UserRepository;
use App\Infrastructure\Services\Application\ApplicationService;
use App\Infrastructure\Services\Application\Factories\ApplicationFactory;
use App\Infrastructure\Services\Application\Factories\ProductFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Infrastructure\Repositories\ApplicationRepository;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PartnerController extends Controller
{
    private int $obiUser;

    public function __construct(private readonly ApplicationService $appService
    )
    {
        $this->obiUser = config('app.obi_user_id');
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
            Log::error('Get partner order error ' . $e->getMessage());
            return response(['success' => false, 'message' => 'Ошибка получения заказов']);
        }

        return new GetOrdersFor1cResource($result);
    }
}
