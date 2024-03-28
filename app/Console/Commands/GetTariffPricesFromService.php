<?php

namespace App\Console\Commands;

use App\Infrastructure\Admin\Services\Tariff\TariffService;
use Illuminate\Console\Command;

class GetTariffPricesFromService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tariff-prices:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получение цена по тарифам из сервиса delivery holodilnik';

    protected TariffService $service;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TariffService $service)
    {
        parent::__construct();
        $this->service = $service;

    }

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle(): void
    {
        if ($this->service->getFromDeliveryService()) {
            echo 'done';
        }
    }
}
