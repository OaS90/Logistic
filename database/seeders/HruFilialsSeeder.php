<?php

namespace Database\Seeders;

use App\Domain\DTO\HruFilialDTO;
use App\Infrastructure\Imports\ApplicationImportCsv;
use App\Infrastructure\Repositories\Hru\FilialRepository;
use App\Infrastructure\Repositories\Hru\WarehouseRepository;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class HruFilialsSeeder extends Seeder
{
    public function __construct(private readonly WarehouseRepository $warehouseRepo,
                                private readonly FilialRepository $filialRepo,
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
        $dataFromFile = Excel::toArray(new ApplicationImportCsv(), storage_path('app/public/filials.csv'))[0];
        unset($dataFromFile[0]);
        foreach ($dataFromFile as $filial) {
            $warehouse = $this->warehouseRepo->getByCode($filial[2]);

            if ($warehouse) {
                $dto = new HruFilialDto(
                    code: $filial[0],
                    name: $filial[1],
                    warehouseId: $warehouse->id
                );

                $this->filialRepo->create($dto);
            } else {
                echo "Filial $filial[1] code: $filial[0] not found\n";
            }
        }
    }
}
