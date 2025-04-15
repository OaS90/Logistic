<?php

namespace Database\Seeders;

use App\Domain\Admin\TCSettingDTO;
use App\Infrastructure\Repositories\Admin\TransportCompanyRepository;
use App\Infrastructure\Repositories\Admin\TransportCompanySettingsRepository;
use App\Infrastructure\Repositories\Hru\FilialRepository;
use App\Models\TransportCompanyWarehouse;
use Illuminate\Database\Seeder;

class TCSettingsFilialSettingsSeeder extends Seeder
{
    public function __construct(private readonly TransportCompanyRepository $tcRepo,
                                private readonly FilialRepository $filialRepository,
                                private readonly TransportCompanySettingsRepository $tcSettingsRepository
    )
    {
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $filials = $this->filialRepository->getAll();
        $tcs = $this->tcRepo->getAll();

        foreach ($tcs as $tc) {
            foreach ($filials as $filial) {
                if ($filial->warehouses->first()) {
                    $warehouse = $filial->warehouses->first();
                    $tcWarehouse = TransportCompanyWarehouse::where('code', $warehouse->code)->first();
                    $dto = new TCSettingDTO(
                        tcId: $tc->id,
                        warehouseId: $filial->warehouses->first()->id,
                        filialId: $filial->id,
                        quote: $tcWarehouse ? $tcWarehouse->quote : null,
                        delayDays: $tcWarehouse ? $tcWarehouse->delay_days : null,
                    );

                    $this->tcSettingsRepository->create($dto);
                }
            }
        }

    }
}
