<?php

namespace App\Http\Controllers;

use App\Infrastructure\Imports\ApplicationImport;
use App\Infrastructure\Repositories\DeliveryAddressRepository;
use App\Infrastructure\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Application\CsvExportService;
use App\Infrastructure\Exports\ApplicationExport;
use App\Infrastructure\DadataAdapter;
use Barryvdh\DomPDF\Facade\Pdf;
use Picqer\Barcode\BarcodeGeneratorDynamicHTML;
use App\Application\CsvImportService;
use App\Application\ApplicationService;
use Illuminate\Support\Facades\Response;

class ApplicationController extends Controller
{
    protected $repo;
    protected $exportService;
    protected $dadataAdapter;
    protected $codeGenerator;
    protected $addressRepository;
    protected $productRepository;
    protected $importService;
    protected $appService;

    public function __construct(ApplicationRepository $applicationRepository,
                                CsvExportService $exportService,
                                DadataAdapter $dadataAdapter,
                                BarcodeGeneratorDynamicHTML $codeGenerator,
                                DeliveryAddressRepository $addressRepository,
                                CsvImportService $importService,
                                ProductRepository $productRepository,
                                ApplicationService $appService
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
    }

    public function getList()
    {
        $list = $this->repo->getListByUserId(Auth::id());
        $statuses = [
            'created' => count($list->where('status', null)),
            'new' => count($list->where('status', 'new')),
            'inProgress' => count($list->where('status', 'inProgress')),
            'loaded' => count($list->where('status', 'loaded')),
            'postponed' => count($list->where('status', 'postponed')),
            'refusal' => count($list->where('status', 'refusal')),
            'completed' => count($list->where('status', 'completed')),
            'defect' => count($list->where('status', 'defect')),
        ];

        return view('application-list', ['list' => $list, 'statuses' => $statuses]);
    }

    public function current($id)
    {
        return view('application', ['application' => $this->repo->getById($id)]);
    }

    public function show()
    {
        return view('application-create', ['userId' => Auth::id(), 'warehouses' => Auth::user()->warehouses]);
    }

    public function create(Request $request)
    {
        $data = $request->get('fields');
        $address = $request->get('address');
        $addressExtra = $request->get('addressExtraInfo');
        // todo переделать, когда появится возможность добавлять несколько товаров в заявку, при ручном создании
        $products = $request->get('products');
        $addressData = array_merge($address, $addressExtra);
        $data['user_id'] = Auth::id();
        // todo refactoring
        $data['delivery_time'] = $data['delivery_from'] . '-' . $data['delivery_till'];
        unset($data['delivery_from']);
        unset($data['delivery_till']);
        unset($data['_token']);
        $data['client_phone'] = parse_phone($data['client_phone']);
        $newAddress = $this->addressRepository->create($addressData);
        $data['delivery_address'] = $newAddress->id;
        $existApp = $this->repo->getByOrderNumber($data['order_number']);

        if (!$existApp) {
            $newApp = $this->repo->create($data);
            $products['app_id'] = $newApp->id;
            $products[] = $this->productRepository->create($products);
        } else {
           $this->appService->checkAppChanges($existApp, $data);
           $this->appService->checkAppProducts($existApp, $products);
        }

//        if ($newApplication)
//            $this->makeCsvAndStore($newApplication);

        return response($request->all(), 200);
    }

    public function makeCsvAndStore($newApp)
    {
        $this->exportService->store(new ApplicationExport($newApp));
    }

    public function getAddress(Request $request)
    {
        return $this->dadataAdapter->getAddress($request->get('input'));
    }

    public function delete($id)
    {
        $this->repo->getById($id)->destroy();
    }

    public function makeSticker($applicationId): \Illuminate\Http\Response
    {
        $application = $this->repo->getById($applicationId);
        $pdf = $this->appService->makeStickers($application);

        return $pdf->download('sticker_' . $application->order_number .'.pdf');
    }

    public function import(Request $request)
    {
        $this->importService->import($request->file('file'), new ApplicationImport(), Auth::id());
    }

    public function downloadFileExample(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Response::download(storage_path('app/public/orders_example.csv'));
    }
}
