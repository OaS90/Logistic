<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Http\Request;
use App\Domain\Admin\QuoteDTO;
use App\Infrastructure\Repositories\Admin\QuoteRepository;
use App\Infrastructure\Repositories\Admin\IntervalQuoteRepository;
use App\Application\ExcelExportService;
use App\Infrastructure\Exports\Admin\QuoteExport;
use Maatwebsite\Excel\Facades\Excel;

class QuotesController extends Controller
{
    protected $repo;
    protected $intervalRepo;
    protected $exportService;

    public function __construct(QuoteRepository $repo, IntervalQuoteRepository $intervalRepo, ExcelExportService $exportService)
    {
        $this->repo = $repo;
        $this->intervalRepo = $intervalRepo;
        $this->exportService = $exportService;
    }

    public function show()
    {
        $quotes = (new QuoteDTO())->toArrayForVue(Quote::with(['intervals', 'division'])->get());

        return view('vendor.backpack.quotes', ['quotes' => collect($quotes)]);
    }

    public function save(Request $request)
    {
        $this->intervalRepo->update($request->all());
        $updatedQuotes = $this->repo->update($request->all());

        return response($updatedQuotes);
    }

    public function download(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return $this->exportService->download(new QuoteExport());
    }
}
