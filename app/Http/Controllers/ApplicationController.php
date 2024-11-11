<?php

namespace App\Http\Controllers;

use App\Domain\Enum\ApplicationStatus;
use App\Http\Controllers\Api\Exceptions\WarehouseNotFoundException;
use App\Http\Requests\Application\ApplicationUICreateRequest;
use App\Infrastructure\Exceptions\PartnerWarehouseNotFoundException;
use App\Infrastructure\Repositories\ApplicationObiRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\DadataAdapter;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Infrastructure\Services\Import\CsvImportService;
use App\Application\ApplicationServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Response as FResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ApplicationController extends Controller
{
    private int $obiUser;

    public function __construct(private readonly ApplicationRepository $repo,
                                private readonly DadataAdapter $dadataAdapter,
                                private readonly CsvImportService $importService,
                                private readonly ApplicationServiceInterface $appService,
                                private readonly ApplicationObiRepository $applicationObiRepo,
    )
    {
        $this->obiUser = config('app.obi_user_id');
    }

    public function getList(): View
    {
        $userId = Auth::id();

        if ($userId == $this->obiUser) {
            $list = $this->applicationObiRepo->getListByUserId($userId);
            $view = 'obi-application-list';
        } else {
            $list = $this->repo->getListByUserId($userId);
            $view = 'application-list';
        }

        $statuses = [
            'created' => count($list->where('status', ApplicationStatus::CREATED)),
            'new' => count($list->where('status', ApplicationStatus::NEW)),
            'inProgress' => count($list->where('status', ApplicationStatus::IN_PROGRESS)),
            'loaded' => count($list->where('status', ApplicationStatus::LOADED)),
            'postponed' => count($list->where('status', ApplicationStatus::POSTPONED)),
            'refusal' => count($list->where('status', ApplicationStatus::REFUSAL)),
            'completed' => count($list->where('status', ApplicationStatus::COMPLETED)),
            'defect' => count($list->where('status', ApplicationStatus::DEFECT)),
        ];

        return view($view, ['list' => $list, 'statuses' => $statuses]);
    }

    public function current(int $id): View
    {
        $userId = Auth::id();

        if ($userId == $this->obiUser) {
            $app = $this->applicationObiRepo->getById($userId);
            $view = 'obi-application';
        } else {
            $app = $this->repo->getById($id);
            $view = 'application';
        }

        return view($view, ['application' => $app]);
    }

    public function show(): View
    {
        $user = Auth::user();

        return view('application-create', [
            'userId' => $user->id,
            'warehouses' => $user->warehouses
        ]);
    }

    public function create(ApplicationUICreateRequest $request, ApplicationServiceInterface $service): Response
    {
        try {
            $service->createFromUI($request->getDTO());
        } catch (\Throwable $e) {
            Log::error('Ошибка создания заявки через форму в лк: ' . $e->getMessage());

            return response(['success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(['success' => true], Response::HTTP_OK);
    }

    public function getAddress(Request $request)
    {
        return $this->dadataAdapter->getAddress($request->get('input'));
    }

    public function delete(int $id): void
    {
        $this->repo->getById($id)->destroy();
    }

    public function makeSticker(int $applicationId): \Illuminate\Http\Response
    {
        $application = $this->repo->getById($applicationId);
        $pdf = $this->appService->makeStickers($application);

        return $pdf->download('sticker_' . $application->order_number .'.pdf');
    }

    public function import(Request $request): Response
    {
        $userId = Auth::id();
        $storeId = $request->get('store_id') ? (int) $request->get('store_id') : null;
        $fileExtension = $request->file('document')->getClientOriginalExtension();

        try {
            if ($userId == $this->obiUser) {
                $this->importService
                    ->importObi(
                        $request->file('document'),
                        $this->importService->extensionHandler($fileExtension, true),
                        $userId
                    );
            } else {
                $this->importService
                    ->import($request->file('document'),
                        $this->importService->extensionHandler($fileExtension, false),
                        $userId,
                        $storeId
                    );
            }
        } catch (\Throwable $e) {
            $msg = 'Ошибка загрузки!';
            Log::error('Import error: ' . $e->getMessage());

            if ($e instanceof PartnerWarehouseNotFoundException) {
                $msg .= $e->getMessage();
            }

            // TODO отдавать ошибку в vue
            return response(['message' => $msg], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(['message' => 'Файл успешно загружен!'], Response::HTTP_OK);
    }

    public function downloadFileExample(): BinaryFileResponse
    {
        if (Auth::id() == $this->obiUser) {
            return FResponse::download(storage_path('app/public/example-obi.xlsx'));
        } else {
            return FResponse::download(storage_path('app/public/orders_example.csv'));
        }
    }
}
