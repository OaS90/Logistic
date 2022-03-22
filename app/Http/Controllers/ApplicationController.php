<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Application\CsvExportService;
use App\Infrastructure\Exports\ApplicationExport;
use App\Infrastructure\DadataAdapter;

class ApplicationController extends Controller
{
    protected $repo;
    protected $exportService;
    protected $dadataAdapter;

    public function __construct(ApplicationRepository $applicationRepository,
                                CsvExportService $exportService,
                                DadataAdapter $dadataAdapter
    )
    {
        $this->repo = $applicationRepository;
        $this->exportService = $exportService;
        $this->dadataAdapter = $dadataAdapter;
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
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['delivery_time'] = $data['delivery_from'] . '-' . $data['delivery_till'];
        unset($data['delivery_from']);
        unset($data['delivery_till']);
        unset($data['_token']);
        $newApplication = $this->repo->create($data);

        if ($newApplication)
            $this->makeCsvAndStore($newApplication);

        return response($request->all(), 200);
    }

    public function makeCsvAndStore($newApp)
    {
        $this->exportService->store(new ApplicationExport($newApp));
    }

    public function getAddress(Request $request)
    {

        return $this->dadataAdapter->getAddress($request->get('data'));
    }

    public function delete($id)
    {
        $this->repo->getById($id)->destroy();
    }
}
