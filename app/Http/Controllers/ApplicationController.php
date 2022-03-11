<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Application\CsvExportService;
use App\Infrastructure\Exports\ApplicationExport;

class ApplicationController extends Controller
{
    protected $repo;
    protected $exportService;

    public function __construct(ApplicationRepository $applicationRepository, CsvExportService $exportService)
    {
        $this->repo = $applicationRepository;
        $this->exportService = $exportService;
    }

    public function getList()
    {
        $list = $this->repo->getListByUserId(Auth::id());

        return view('application-list', ['list' => $list]);
    }

    public function show()
    {
        return view('application-create', ['userId' => Auth::id()]);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $data['delivery_time'] = $data['delivery_from'] . '-' . $data['delivery_till'];
        $data['elevator'] = false;
        unset($data['delivery_from']);
        unset($data['delivery_till']);
        unset($data['_token']);
        $newApplication = $this->repo->create($data);

        if ($newApplication)
            $this->makeCsvAndStore($newApplication);

        return redirect()->back();
    }

    public function makeCsvAndStore($newApp)
    {
        $this->exportService->store(new ApplicationExport($newApp));
    }
}
