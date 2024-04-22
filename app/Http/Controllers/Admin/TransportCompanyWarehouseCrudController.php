<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TransportCompanyWarehouseRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TransportCompanyWarehouseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TransportCompanyWarehouseCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\TransportCompanyWarehouse::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/transport-company-warehouse');
        CRUD::setEntityNameStrings('склад для транспортной компании', 'склады для транспортных компаний');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumn([  // Select
            'name'  => 'region_name',
            'label' => 'Регион', // Table column heading
            'type'  => 'model_function',
            'function_name' => 'getRegionName', // the method in your Model
        ]);
        CRUD::column('name')->type('text')->label('Наименование склада');
        CRUD::column('code')->type('text')->label('Код склада');
        CRUD::column('quote')->type('number')->label('Квота на сутки');
        CRUD::column('delay_days')->type('number')->label('Задержка дней отгрузки');

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
        CRUD::setValidation(TransportCompanyWarehouseRequest::class);
        CRUD::field('name')->type('text')->label('Наименование склада');
        CRUD::field('code')->type('number')->label('Код склада');
        CRUD::field('quote')->type('number')->label('Квота на сутки');
        CRUD::field('delay_days')->type('number')->label('Задержка дней отгрузки');
        CRUD::addField([  // Select
            'label'     => "Регион",
            'type'      => 'select',
            'name'      => 'region_id', // the db column for the foreign key
            // optional - manually specify the related model and attribute
            'model'     => "App\Models\Region", // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
        ]);

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
