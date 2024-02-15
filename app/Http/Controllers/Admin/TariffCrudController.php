<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TariffRequest;
use App\Infrastructure\Repositories\RegionRepository;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TariffCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TariffCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function __construct(private readonly RegionRepository $regionRepository)
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
        CRUD::setModel(\App\Models\Tariff::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/tariff');
        CRUD::setEntityNameStrings('Тариф', 'Тарифы');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id')->label('ID')->type('text');
        CRUD::column('alias')->label('Алиас')->type('text');
        CRUD::column('name')->label('Наименование')->type('text');

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
    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(TariffRequest::class);
        CRUD::field('id')->type('text')->label('ID')
            ->attributes(['class' => 'form-control', 'readonly' => 'readonly'])
            ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('alias')->type('text')->label('Алиас')
            ->attributes(['class' => 'form-control'])
            ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('name')
            ->type('text')
            ->label('Наименование')
            ->attributes(['class' => 'form-control'])
            ->wrapper(['class' => 'form-group col-md-3']);

        $regions = $this->regionRepository->getAll();

        if ($this->crud->getCurrentEntry()) {
            $entityId = $this->crud->getCurrentEntryId();
            foreach ($regions as $region) {
                CRUD::addField([
                    'name' => 'region_' . $region->id,
                    'value' => '<a href="' . backpack_url('tariff/' . $entityId . '/edit/regions/' . $region->id . '/edit')  .'">' . $region->name . '</a>',
                    'type' => 'custom_html',
                ]);
            }
        }
//        'attributes' => [
//        'class' => 'form-control',
//        'readonly' => 'readonly',
//    ],

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
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
