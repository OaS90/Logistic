<?php

namespace App\Http\Controllers\Admin;

use App\Domain\DTO\WarehouseShipmentSettingsDTO;
use App\Http\Requests\TransportCompanyShipmentWarehouseRequest;
use App\Infrastructure\Events\EventDispatcher;
use App\Infrastructure\Repositories\Admin\TransportCompanyRepository;
use App\Infrastructure\Repositories\Hru\FilialRepository;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TransportCompanyShipmentWarehouseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TransportCompanyShipmentWarehouseCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }

    public function __construct(private readonly EventDispatcher $eventDispatcher,
                                private readonly FilialRepository $filialRepository,
                                private readonly TransportCompanyRepository $transportCompanyRepository,
    )
    {
        parent::__construct();

    }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\TransportCompanyShipmentWarehouse::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/transport-company-shipment-warehouse');
        CRUD::setEntityNameStrings('Склад отгрузки', 'Склады отгрузки');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation(): void
    {
        $this->crud->addColumn([
            'name' => 'id',
            'label' => 'ID'
        ]);

        $this->crud->addColumn([
            'name' => 'tc_id',
            'label' => 'Транспортная компания',
            'type' => 'model_function',
            'function_name' => 'getTcName'
        ]);

        $this->crud->addColumn([
            'name' => 'filial_id',
            'label' => 'Филиал',
            'type' => 'model_function',
            'function_name' => 'getFilialName'
        ]);

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(TransportCompanyShipmentWarehouseRequest::class);

        $this->crud->addField([  // Select
            'label' => "Транспортная компания",
            'type' => 'select',
            'name' => 'tc_id', // the db column for the foreign key
            'entity' => 'transport_company', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
        ]);

        $this->crud->addField([  // Select
            'label' => "Филиал",
            'type' => 'select',
            'name' => 'filial_id', // the db column for the foreign key
            'entity' => 'filial', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
                // optional
            'model' => "App\Models\Hru\Filial",
            'options'   => (function ($query) {
                $result = $query->with('warehouses')->get();

                return $result->filter(function ($filial) {
                    foreach ($filial->warehouses as $warehouse) {
                        if (!$warehouse->pivot->is_virtual && $filial->default_warehouse_code == $warehouse->code) {
                            return $filial;
                        }
                    }
                });
            }),
        ]);

        $this->crud->addField([  // Select
            'label' => "Код ПВЗ",
            'type' => 'text',
            'name' => 'departure_id', // the db column for the foreign key
        ]);

//        if ($entity = $this->crud->getCurrentEntry()) {
//            if ($entity->data) {
//                foreach ($entity->data as $param) {
//                    $this->crud->addField([
//                        'label' => $param->name,
//                        'type' => 'text',
//                        'name' => $param->name,
//                        'value' => $param->value,
//                    ]);
//                }
//            }
//        }

//        $this->crud->addField([
//            'name' => 'h3-label',
//            'label' => 'Доп. параметры', // human-readable label for the input
//            'type' => 'custom_html',
//            'value' => '<h3>Доп. параметры</h3>'
//        ]);
        $currentEntry = $this->crud->getCurrentEntry();

        $this->crud->addField([
            'label' => 'Доп. параметры',
            'type' => 'shipment-warehouse-settings',
            'name' => 'data',
            'value' => $currentEntry ? $currentEntry->data : [],
        ]);

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function store(): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $request = $this->crud->getRequest();
        $params = $request->all();
//        dd($params);


        return $this->traitStore();
    }

    public function update(): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $request = $this->crud->getRequest();
        $params = $request->all();
        $filial = $this->filialRepository->getById($params['filial_id']);
        $transportCompany = $this->transportCompanyRepository->getById($params['tc_id']);

        $dto = new WarehouseShipmentSettingsDTO(
            filialCode: $filial->filial_code,
            transportCompanyCode: $transportCompany->code,
            departurePointId: $params['departure_id'],
            settings: json_decode($params['data'], true)
        );

        $this->eventDispatcher->warehouseShipmentSettings($dto);

        return $this->traitUpdate();
    }
}
