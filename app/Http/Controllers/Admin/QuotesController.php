<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Infrastructure\Admin\Services\Api\HruApi;
use App\Models\Quote;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use App\Domain\Admin\QuoteDTO;
use App\Infrastructure\Repositories\Admin\QuoteRepository;
use App\Infrastructure\Repositories\Admin\IntervalQuoteRepository;
use App\Application\ExcelExportService;
use App\Infrastructure\Exports\Admin\QuoteExport;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuotesChange;
use App\Infrastructure\Repositories\Admin\EmailQuoteRepository;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use App\Infrastructure\Services\Monolith\Api;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class QuotesController extends Controller
{
    public function __construct(private readonly QuoteRepository $repo,
                                private readonly IntervalQuoteRepository $intervalRepo,
                                private readonly ExcelExportService $exportService,
                                private readonly EmailQuoteRepository $emailQuoteRepo,
                                private readonly Api $monolithApi
    )
    {}

    public function show(): View
    {
        // TODO аписать адаптер
        $quotes = (new QuoteDTO())->toArrayForVue(Quote::with(['intervals', 'region'])->get());
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

        // TODO переписать на нормальное создание (не в дто)
        $json = (new QuoteDTO())->makeDataForApiHru($this->repo->getAll());
        $responseFromMonolithIsSuccess = $this->monolithApi->sendQuotes($json);

        if ($responseFromMonolithIsSuccess) {
            $message .= ' Квоты отправлены на сайт HRU';

            foreach ($updatedQuotes as $quote) {
                Mail::to($this->emailQuoteRepo->getAllActiveEmails())->send(new QuotesChange($quote));
            }
        } else {
            $message .= ' Ошибка отправки квот на сайт!';
        }

        return response(['message' => $message]);
    }

    public function download(): BinaryFileResponse
    {
        return $this->exportService->download(new QuoteExport());
    }
}
