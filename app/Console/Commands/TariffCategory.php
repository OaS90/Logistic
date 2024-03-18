<?php

namespace App\Console\Commands;

use App\Infrastructure\Imports\ApplicationImportCsv;
use App\Infrastructure\Repositories\Admin\TariffCategoryRepository;
use App\Shared\Eloquent\ConvertsToUtfTrait;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Infrastructure\Repositories\RegionRepository;

class TariffCategory extends Command
{
    use ConvertsToUtfTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tariffs:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected TariffCategoryRepository $categoryRepo;
    protected RegionRepository $regionRepo;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TariffCategoryRepository $categoryRepo, RegionRepository $regionRepo)
    {
        parent::__construct();
        $this->categoryRepo = $categoryRepo;
        $this->regionRepo = $regionRepo;
    }

    /**
     * Execute the console command.
     *
     *
     */
    public function handle(): void
    {
        $dataFromFile = Excel::toArray(new ApplicationImportCsv(), storage_path('app/public/tariff-categories.csv'))[0];

        foreach ($this->regionRepo->getAll() as $region) {
            foreach ($dataFromFile as $item) {
                $category = $this->categoryRepo->getByCategoryServiceIdAndProductCategoryId($item[3]);

                if (!$category) {
                    $category = $this->categoryRepo->create([
                        'name' => $this->toCp1251($item[4]),
                        'result_category_id' => $item[3],
                    ]);
                }

                if ($region->region_id == $item[1]) {
                    $existsPivot = $region->tariffCategories()
                        ->where('tariff_category_region.region_id', $region->id)
                        ->where('tariff_category_region.category_id', $category->id)
                        ->first();

                    if (!$existsPivot) {
                        $region->tariffCategories()->attach($category);
                    }
                }
            }
        }

    }
}
