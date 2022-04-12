<?php

namespace App\Http\Controllers;

use App\Infrastructure\Repositories\DeliveryAddressRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Application\CsvExportService;
use App\Infrastructure\Exports\ApplicationExport;
use App\Infrastructure\DadataAdapter;
use Barryvdh\DomPDF\Facade\Pdf;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorDynamicHTML;

class ApplicationController extends Controller
{
    protected $repo;
    protected $exportService;
    protected $dadataAdapter;
    protected $codeGenerator;
    protected $addressRepository;

    public function __construct(ApplicationRepository $applicationRepository,
                                CsvExportService $exportService,
                                DadataAdapter $dadataAdapter,
                                BarcodeGeneratorDynamicHTML $codeGenerator,
                                DeliveryAddressRepository $addressRepository
    )
    {
        $this->repo = $applicationRepository;
        $this->exportService = $exportService;
        $this->dadataAdapter = $dadataAdapter;
        $this->codeGenerator = $codeGenerator;
        $this->addressRepository = $addressRepository;
    }

    public function getList()
    {
        $list = $this->repo->getListByUserId(Auth::id());

        return view('application-list', ['list' => $list]);
    }

    public function current($id)
    {
        return view('application', ['application' => $this->repo->getById($id)]);
    }

    public function show()
    {
        return view('application-create', ['userId' => Auth::id()]);
    }

    public function create(Request $request)
    {
        $data = $request->get('fields');
        $address = $request->get('address');
        $data['user_id'] = Auth::id();
        $data['delivery_time'] = $data['delivery_from'] . '-' . $data['delivery_till'];
        unset($data['delivery_from']);
        unset($data['delivery_till']);
        unset($data['_token']);
        $address['flat'] = $data['flat'];
        $address['floor'] = $data['floor'];
        $address['entrance'] = $data['entrance'];
        $address['postcode'] = $data['postcode'];
        $address['elevator'] = $data['elevator'];
        $newAddress = $this->addressRepository->create($address);
        $data['delivery_address'] = $newAddress->id;
        $newApplication = $this->repo->create($data);

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

    public function makeSticker($applicationId)
    {
        // todo перенести в сервис
        $application = $this->repo->getById($applicationId);
        $barcode = $this->codeGenerator->getBarcode($application->order_number, $this->codeGenerator::TYPE_EAN_13);
        $pdf = PDF::loadView('sticker', ['code' => $barcode, 'application' => $application])
            ->setPaper([30, -30, 280.77, 400.16]);

        return $pdf->download('sticker_' . $application->order_number .'.pdf');
    }
}
