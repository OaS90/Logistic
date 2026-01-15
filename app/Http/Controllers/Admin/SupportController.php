<?php

namespace App\Http\Controllers\Admin;

use App\Application\ApplicationServiceInterface;
use App\Infrastructure\Services\Import\CsvImportService;
use App\Http\Controllers\Controller;
use App\Infrastructure\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SupportController extends Controller
{
    private ApplicationServiceInterface $applicationService;
    private CsvImportService $importService;
    private UserRepository $partnerRepository;
    private int $obiUser;

    public function __construct(ApplicationServiceInterface $applicationService,
                                CsvImportService $importService,
                                UserRepository $partnerRepository
    )
    {
        $this->applicationService = $applicationService;
        $this->importService = $importService;
        $this->partnerRepository = $partnerRepository;
        $this->obiUser = config('app.obi_user_id');
    }

    public function showAppsStatuses(): View
    {
       return view('vendor.backpack.support-applications');
    }

    public function getApps(): JsonResponse
    {
        $commonApplications = $this->applicationService->getAllApplications();

        return response()->json($commonApplications);
    }

    public function showUploadPage(): View
    {
        $partners = $this->partnerRepository->getAll();

        return view('vendor.backpack.support-application-upload', ['partners' => $partners]);
    }

    public function upload(Request $request): Response
    {
        $userId = $request->get('user_id');
        $storeId = (int) $request->get('store_id');
        $fileExtension = $request->file('document')->getClientOriginalExtension();

        try {
            if ($userId == $this->obiUser) {
                $this->importService
                    ->importObi($request->file('document'), $this->importService->extensionHandler($fileExtension, true), $userId);
            } else {
                $this->importService
                    ->import($request->file('document'), $this->importService->extensionHandler($fileExtension, false), $userId, $storeId);
            }
        } catch (\Throwable $e) {
            Log::error('Support upload file error ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'Файл успешно загружен!'], Response::HTTP_OK);
    }

    public function getPartnerWarehouses(int $partnerId): Response
    {
        return response(['id' => $partnerId], Response::HTTP_OK);
    }
}
