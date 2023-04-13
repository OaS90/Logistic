<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Domain\Admin\QuoteDTO;
use App\Infrastructure\Repositories\Admin\QuoteRepository;
use App\Infrastructure\Repositories\Admin\IntervalQuoteRepository;
use App\Application\ExcelExportService;
use App\Infrastructure\Exports\Admin\QuoteExport;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuotesChange;
use App\Infrastructure\Repositories\Admin\EmailQuoteRepository;

class QuotesController extends Controller
{
    protected $repo;
    protected $intervalRepo;
    protected $exportService;
    protected $emailQuoteRepo;

    public function __construct(QuoteRepository $repo,
                                IntervalQuoteRepository $intervalRepo,
                                ExcelExportService $exportService,
                                EmailQuoteRepository $emailQuoteRepository
    )
    {
        $this->repo = $repo;
        $this->intervalRepo = $intervalRepo;
        $this->exportService = $exportService;
        $this->emailQuoteRepo = $emailQuoteRepository;
    }

    public function show()
    {
        $quotes = (new QuoteDTO())->toArrayForVue(Quote::with(['intervals', 'region'])->get());

        return view('vendor.backpack.quotes', ['quotes' => collect($quotes)]);
    }

    public function save(Request $request)
    {
        $this->intervalRepo->update($request->all());
        $updatedQuotes = $this->repo->update($request->all());
        $client = new Client();
        $message = 'Данные сохранены.';
        $json = (new QuoteDTO())->makeDataForApiHru($this->repo->getAll());

        try {
            $response = $client->post(config('app.api_hru'), [
                'headers' => [
                    'Content-Type' => 'application/json', 'Accept' => 'application/json',
                    'Authorization' => config('app.api_hru_token')
                ],
                //'auth' => config('app.api_auth'), для теста раскоментить
                'json' => $json
            ]);

            $responseContents = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() == 200 && (isset($responseContents['success']) && $responseContents['success']))
                $message .= ' Квоты отправлены на сайт HRU';
            else
                $message .= ' Ошибка отправки квот на сайт!';

            foreach ($updatedQuotes as $quote) {
                Mail::to($this->emailQuoteRepo->getAllActiveEmails())->send(new QuotesChange($quote));
            }

            Log::info('Response(): ' . json_encode($responseContents) . ', code:' . $response->getStatusCode());
        } catch (BadResponseException $e) {
            Log::info($e->getMessage());
        }

        return response(['message' => $message]);
    }

    public function download(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return $this->exportService->download(new QuoteExport());
    }
}
