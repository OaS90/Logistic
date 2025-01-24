<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use App\Infrastructure\Repositories\Admin\QuoteRepository;
use App\Infrastructure\Repositories\Admin\IntervalQuoteRepository;
use App\Application\ExcelExportService;
use App\Infrastructure\Exports\Admin\QuoteExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuotesChange;
use App\Infrastructure\Repositories\Admin\EmailQuoteRepository;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Infrastructure\Admin\Services\Quote\QuoteService;
use App\Infrastructure\Services\Kraken\Api as KrakenApi;

class QuotesController extends Controller
{
    public function __construct(private readonly QuoteRepository $repo,
                                private readonly IntervalQuoteRepository $intervalRepo,
                                private readonly ExcelExportService $exportService,
                                private readonly EmailQuoteRepository $emailQuoteRepo,
                                private readonly QuoteService $quoteService,
                                private readonly KrakenApi $krakenApi,
    )
    {}

    public function show(): View
    {
        $quotes = $this->quoteService->prepareForVue();
        $isGuest = (bool) backpack_user()->hasRole('guest');

        return view('vendor.backpack.quotes', ['quotes' => collect($quotes), 'guest' => $isGuest]);
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    public function save(Request $request): Response
    {
        $isGuest = (bool) backpack_user()->hasRole('guest');

        if ($isGuest) {
            return response(['message' => 'У вас недостаточно прав.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->intervalRepo->update($request->all());
        $updatedQuotes = $this->repo->update($request->all(), backpack_user()->id);
        $message = 'Данные сохранены.';
        $quotes = $this->quoteService->prepareForMonolith();

        $responseFromMonolithIsSuccess = $this->krakenApi
            ->monolithRequest($this->krakenApi::MONOLITH_UPDATE_QUOTES_URI, $quotes, 'POST');

        if ($responseFromMonolithIsSuccess && $responseFromMonolithIsSuccess['status']) {
            $message .= ' Квоты отправлены на сайт HRU.';

            if (env('APP_ENV') === 'production') {
                foreach ($updatedQuotes as $quote) {
                    Mail::to($this->emailQuoteRepo->getAllActiveEmails())->send(new QuotesChange($quote));
                }
            }
        } else {
            $message .= ' Ошибка отправки на сайт HRU!';
        }

        if (config('app.enable_config_service_api_for_quotes')) {
            $sentToConfigService = $this->krakenApi
                ->configServiceRequest($this->krakenApi::CONFIG_UPDATE_QUOTES_URI, $quotes, 'PATCH');

            if ($sentToConfigService) {
                $message .= ' Квоты отправлены в сервис Config';
            } else {
                $message .= ' Ошибка отправки квот в сервис Config!';
            }
        }

        return response(['message' => $message]);
    }

    public function download(): BinaryFileResponse
    {
        return $this->exportService->download(new QuoteExport());
    }
}
