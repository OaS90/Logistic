<?php

namespace App\Http\Controllers\Admin;

use App\Application\ApplicationService;
use App\Application\CsvImportService;
use App\Http\Controllers\Controller;
use App\Infrastructure\Imports\ApplicationImportCsv;
use App\Infrastructure\Imports\ApplicationImportXlsx;
use App\Infrastructure\Imports\ApplicationObiImport;
use App\Infrastructure\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    private ApplicationService $applicationService;
    private CsvImportService $importService;
    private UserRepository $partnerRepository;
    private $obiUser;

    public function __construct(ApplicationService $applicationService,
                                CsvImportService $importService,
                                UserRepository $partnerRepository
    )
    {
        $this->applicationService = $applicationService;
        $this->importService = $importService;
        $this->partnerRepository = $partnerRepository;
        $this->obiUser = config('app.obi_user_id');
    }

    public function showAppsStatuses()
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
        return view('vendor.backpack.support-application-upload');
    }

    public function getPartners()
    {
        return $this->partnerRepository->getAll();
    }

    public function upload(Request $request)
    {
        $userId = $request->get('user_id');
        $storeId = $request->get('store_id');
        $fileExtension = $request->file('document')->extension();
        dd($storeId);
        try {
            if ($userId == $this->obiUser) {
                $this->importService
                    ->importObi($request->file('file'), $this->extensionHandler($fileExtension), $userId);
            } else {
                $this->importService
                    ->import($request->file('document'), $this->extensionHandler($fileExtension), $userId, $storeId);
            }
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getPartnerWarehouses(int $partnerId)
    {
        return response(['id' => $partnerId], 200);
    }

    /**
     * @param string $extension
     * @return ApplicationImportCsv|ApplicationImportXlsx
     */
    private function extensionHandler(string $extension)
    {
        switch ($extension) {
            case 'xlsx':
                return new ApplicationImportXlsx();
            default:
                return new ApplicationImportCsv();
        }
    }
}
