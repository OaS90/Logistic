<?php

namespace Database\Seeders;

use App\Domain\Admin\TCSettingDTO;
use App\Infrastructure\Repositories\Admin\TransportCompanyRepository;
use App\Infrastructure\Repositories\Admin\TransportCompanySettingsRepository;
use App\Infrastructure\Repositories\Hru\FilialRepository;
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
                    $dto = new TCSettingDTO(
                        tcId: $tc->id,
                        warehouseId: $filial->warehouses->first()->id,
                        filialId: $filial->id,
                    );

                    $this->tcSettingsRepository->create($dto);
                }
            }
        }

    }
}
