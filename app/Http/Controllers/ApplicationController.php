<?php

namespace App\Http\Controllers;

use App\Http\Requests\Application\ApplicationUICreateRequest;
use App\Infrastructure\Admin\Exceptions\ProductWithoutSkuException;
use App\Infrastructure\Repositories\ApplicationObiRepository;
use App\Infrastructure\Repositories\DeliveryAddressRepository;
use App\Infrastructure\Repositories\ProductRepository;
use App\Infrastructure\Services\Application\ApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Application\ExcelExportService;
use App\Infrastructure\Exports\ApplicationExport;
use App\Infrastructure\DadataAdapter;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Picqer\Barcode\BarcodeGeneratorDynamicHTML;
use App\Application\CsvImportService;
use App\Application\ApplicationServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Response as FResponse;

class ApplicationController extends Controller
{
    protected ApplicationRepository $repo;
    protected ExcelExportService $exportService;
    protected DadataAdapter $dadataAdapter;
    protected BarcodeGeneratorDynamicHTML $codeGenerator;
    protected DeliveryAddressRepository $addressRepository;
    protected ProductRepository $productRepository;
    protected CsvImportService $importService;
    protected ApplicationServiceInterface $appService;
    protected ApplicationObiRepository $applicationObiRepo;
    private int $obiUser;

    public function __construct(ApplicationRepository $applicationRepository,
                                ExcelExportService $exportService,
                                DadataAdapter $dadataAdapter,
                                BarcodeGeneratorDynamicHTML $codeGenerator,
                                DeliveryAddressRepository $addressRepository,
                                CsvImportService $importService,
                                ProductRepository $productRepository,
                                ApplicationServiceInterface $appService,
                                ApplicationObiRepository $applicationObiRepo
    )
    {
        $this->repo = $applicationRepository;
        $this->exportService = $exportService;
        $this->dadataAdapter = $dadataAdapter;
        $this->codeGenerator = $codeGenerator;
        $this->addressRepository = $addressRepository;
        $this->productRepository = $productRepository;
        $this->importService = $importService;
        $this->appService = $appService;
        $this->applicationObiRepo = $applicationObiRepo;
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
            'created' => count($list->where('status', 'created')),
            'new' => count($list->where('status', 'new')),
            'inProgress' => count($list->where('status', 'inProgress')),
            'loaded' => count($list->where('status', 'loaded')),
            'postponed' => count($list->where('status', 'postponed')),
            'refusal' => count($list->where('status', 'refusal')),
            'completed' => count($list->where('status', 'completed')),
            'defect' => count($list->where('status', 'defect')),
        ];

        return view($view, ['list' => $list, 'statuses' => $statuses]);
    }

    public function current($id): View
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
//        dd('frfre');
//        $data = $request->get('fields');
//        $address = $request->get('address');
//        $addressExtra = $request->get('addressExtraInfo');
//        // todo переделать, когда появится возможность добавлять несколько товаров в заявку, при ручном создании
//        $products = $request->get('products');
//        $addressData = array_merge($address, $addressExtra);
//        $data['user_id'] = Auth::id();
//        // todo refactoring
//        $data['delivery_time'] = $data['delivery_from'] . '-' . $data['delivery_till'];
//        unset($data['delivery_from']);
//        unset($data['delivery_till']);
//        unset($data['_token']);
//        $data['client_phone'] = parse_phone($data['client_phone']);
//        $newAddress = $this->addressRepository->create($addressData);
//        $data['delivery_address'] = $newAddress->id;
//
//        if ($this->obiUser == Auth::id()) {
//            $existApp = $this->applicationObiRepo->getById($data['order_number']);
//        } else {
//            $existApp = $this->repo->getByOrderNumber($data['order_number']);
//        }
//
//        if (!$existApp) {
//            if ($this->obiUser == Auth::id()) {
//                $existApp = $this->applicationObiRepo->create($data);
//            } else {
//                $existApp = $this->repo->create($data);
//            }
//
//            $products['app_id'] = $existApp->id;
//            $products[] = $this->productRepository->create($products);
//        } else {
//           $this->appService->checkAppChanges($existApp, $data);
//        }
//
//        $this->appService->getDeliveryDateFromHru($existApp->order_number, $newAddress);
////        if ($newApplication)
////            $this->makeCsvAndStore($newApplication);
//
//        return response($request->all(), Response::HTTP_OK);
    }

    public function makeCsvAndStore($newApp)
    {
        $this->exportService->store(new ApplicationExport($newApp));
    }

    public function getAddress(Request $request)
    {
//        return $this->dadataAdapter->getCleanAddress($request->get('input'));
        return $this->dadataAdapter->getAddress($request->get('input'));
    }

    public function delete($id): void
    {
        $this->repo->getById($id)->destroy();
    }

    public function makeSticker($applicationId): \Illuminate\Http\Response
    {
        $application = $this->repo->getById($applicationId);
        $pdf = $this->appService->makeStickers($application);

        return $pdf->download('sticker_' . $application->order_number .'.pdf');
    }

    public function import(Request $request): Response
    {
        $userId = Auth::id();
        $storeId = $request->get('store_id') ? (int)  $request->get('store_id') : null;
        $fileExtension = $request->file('document')->getClientOriginalExtension();

        try {
            if ($userId == $this->obiUser) {
                $this->importService
                    ->importObi($request->file('document'), $this->appService->extensionHandler($fileExtension, true), $userId);
            } else {
                $this->importService
                    ->import($request->file('document'), $this->appService->extensionHandler($fileExtension, false), $userId, $storeId);
            }
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'Файл успешно загружен!'], Response::HTTP_OK);
    }

    public function downloadFileExample(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        if (Auth::id() == $this->obiUser) {
            return FResponse::download(storage_path('app/public/example-obi.xlsx'));
        } else {
            return FResponse::download(storage_path('app/public/orders_example.csv'));
        }
    }
}
