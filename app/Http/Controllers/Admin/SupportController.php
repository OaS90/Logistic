<?php

namespace App\Http\Controllers\Admin;

use App\Application\ApplicationService;
use Backpack\CRUD\app\Http\Controllers\CrudController;


class SupportController
{
    private ApplicationService $appService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->appService = $applicationService;
    }

    public function showOrders()
    {
        $commonApplications = $this->appService->getAllApplications();

        return view('vendor.backpack.support-applications', $commonApplications);
    }
}
