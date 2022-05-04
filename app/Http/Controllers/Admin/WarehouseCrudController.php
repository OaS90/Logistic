<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\WarehouseRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class WarehouseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class WarehouseCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Warehouse::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/warehouses');
        CRUD::setEntityNameStrings('warehouse', 'Склады');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
//        CRUD::setFromDb();
        $this->crud->addColumn([
            'name' => 'user_id',
            'label' => 'Идентификатор партнёра'
        ]);

        $this->crud->addColumn([
            'name' => 'address',
            'label' => 'Адрес склада'
        ]);

        $this->crud->addColumn([
            'name' => 'store_id',
            'label' => 'Идентификатор склада'
        ]);

        $this->crud->denyAccess(['delete', 'show']);
        $this->crud->removeButton('create');
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
        CRUD::setValidation(WarehouseRequest::class);

        $this->crud->addField([
            'label' => 'Id партнёра',
            'name' => 'user_id',
            'attributes' => [
                'disabled'    => 'disabled',
            ],
        ]);

        $this->crud->addField([
            'label' => 'Адрес',
            'name' => 'address',
            'type' => 'textarea',
            'attributes' => [
                'disabled'    => 'disabled',
            ],
        ]);

        $this->crud->addField([
            'label' => 'Идентификатор склада',
            'name' => 'store_id',
        ]);

//        CRUD::setFromDb();
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
