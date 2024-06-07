<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TransportCompanyRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Infrastructure\Repositories\Admin\TransportCompanyWarehouseRepository;
use App\Infrastructure\Repositories\Admin\TransportCompanySettingsRepository;
use App\Domain\Admin\TCSettingDTO;

/**
 * Class TransportCompanyCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TransportCompanyCrudController extends CrudController
{
    protected TransportCompanyWarehouseRepository $tcWarehouseRepo;
    protected TransportCompanySettingsRepository $tcSettingsRepo;

    public function __construct(TransportCompanyWarehouseRepository $tcWarehouseRepo,
                                TransportCompanySettingsRepository $tcSettingsRepo
    )
    {
        $this->tcWarehouseRepo = $tcWarehouseRepo;
        $this->tcSettingsRepo = $tcSettingsRepo;
        parent::__construct();
    }

    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation { destroy as traitDestroy; }
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\TransportCompany::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/transport-company');
        CRUD::setEntityNameStrings('транспортную компанию', 'транспортные компании');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name')->label('Наименование')->type('text');
        CRUD::column('code')->label('Код')->type('text');
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(TransportCompanyRequest::class);
        CRUD::field('name')->label('Наименование')->type('text');
        CRUD::field('code')->label('Код')->type('text');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
    }

    /**
     * При сохранении новой тк, создаём настройки и привязываем к складам
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    public function store(): \Illuminate\Http\RedirectResponse
    {
        $response = $this->traitStore();
        $tcId = $this->crud->getCurrentEntryId();
        $warehouses = $this->tcWarehouseRepo->getAll();

        foreach ($warehouses as $warehouse) {
            $dto = new TCSettingDTO($tcId, $warehouse->id);
            $this->tcSettingsRepo->create($dto);
        }

        return $response;
    }
}
