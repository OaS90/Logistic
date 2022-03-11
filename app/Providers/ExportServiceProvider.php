<?php

namespace App\Providers;

use App\Domain\CsvEntity;
use App\Infrastructure\Exports\ApplicationExport;
use Illuminate\Support\ServiceProvider;

class ExportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CsvEntity::class, ApplicationExport::class);
    }
}
