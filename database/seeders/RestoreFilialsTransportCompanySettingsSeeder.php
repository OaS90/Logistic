<?php

namespace Database\Seeders;

use App\Infrastructure\Repositories\Admin\TransportCompanyRepository;
use App\Infrastructure\Repositories\Admin\TransportCompanySettingsRepository;
use App\Infrastructure\Repositories\Hru\FilialRepository;
use App\Models\Hru\Warehouse;
use App\Models\TransportCompany;
use App\Models\TransportCompanySettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Одноразовый сидер.
 * Делался для того, чтобы восстановить настройки для транспортных компаний
 * по филиалу, используя сохраннённый конфиг на проде quote_tk.json.
 */
class RestoreFilialsTransportCompanySettingsSeeder extends Seeder
{
    public function __construct(private readonly TransportCompanyRepository $tcRepo,
                                private readonly FilialRepository $filialRepository,
    )
    {}

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Очищаем таблицу
        TransportCompanySettings::query()->truncate();
        $filials = $this->filialRepository->getAll();
        $tcs = $this->tcRepo->getAll();

        // Записываем пустые конфиги для филиалов и каждой тк.
        foreach ($tcs as $tc) {
            foreach ($filials as $filial) {
                foreach ($filial->warehouses as $warehouse) {
                    $existsSettings = TransportCompanySettings::where('filial_id', $filial->id)
                        ->where('tc_id', $tc->id)
                        ->first();

                    if (!$existsSettings) {
                        TransportCompanySettings::create([
                            'tc_id' => $tc->id,
                            'tc_warehouse_id' => $warehouse->id,
                            'filial_id' => $filial->id,
                            'quote' => null,
                            'delay_days' => null,
                            'last_time' => null,
                            'days' => null,
                        ]);
                    }
                }
            }
        }

        // Берём конфиг с прода и создаём/обновляем данные
        $prodData = Storage::disk('public')->get('quote_tk.json');
        $content = json_decode($prodData, true);

        foreach ($content as $warehouseCode => $settings) {
            $filialId = $settings['branch_office_id'] ?? null;

            if ($filialId) {
                $filial = $this->filialRepository->getByFilialId($filialId);
                $warehouse = Warehouse::where('code', $warehouseCode)->first();

                foreach ($settings['shipment'] as $shipmentSettings) {
                    $tc = TransportCompany::where('code', $shipmentSettings['tk'])->first();

                    if ($tc) {
                        $existsSettings = TransportCompanySettings::where('filial_id', $filial->id)
                            ->where('tc_id', $tc->id)
                            ->first();

                        if (!$existsSettings) {
                            TransportCompanySettings::create([
                                'enabled' => true,
                                'tc_id' => $tc->id,
                                'tc_warehouse_id' => $warehouse->id,
                                'filial_id' => $filial->id,
                                'quote' => $settings['quote'],
                                'departure_terminal_id' => $shipmentSettings['departure_terminal_id'],
                                'last_time' => $shipmentSettings['last_time'],
                                'days' => $shipmentSettings['weekdays'],
                            ]);
                        } else {
                            $existsSettings->update([
                                'enabled' => true,
                                'quote' => $settings['quote'],
                                'departure_terminal_id' => $shipmentSettings['departure_terminal_id'],
                                'last_time' => $shipmentSettings['last_time'],
                                'days' => $shipmentSettings['weekdays'],
                            ]);
                        }
                    }
                }
            }
        }
    }
}
