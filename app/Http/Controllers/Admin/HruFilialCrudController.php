<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FilialRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FilialCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class HruFilialCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Hru\Filial::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/filial');
        CRUD::setEntityNameStrings('филиал', 'филиалы');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::column('filial_id')->label('Код');
        CRUD::addColumn([
            'name' => 'name',
            'label' => 'Наименование',
            'type' => 'text',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhere('name', 'ilike', "%$searchTerm%");
            }
        ]);
        CRUD::addColumn([
            'name' => 'warehouse_code',
            'label' => 'Код склада',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->warehouses->first() ? $entry->warehouses->first()->code : '-';
            },
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('warehouses', function ($query) use ($searchTerm) {
                    $query->where('code', 'ilike', "%$searchTerm%");
                });
            }
        ]);
        CRUD::addColumn([
            'name' => 'warehouse_id',
            'label' => 'Наименование склада',
            'type' => 'closure',
            'function' => function($entry) {
                return $entry->warehouses->first() ? $entry->warehouses->first()->name : '-';
            },
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('warehouses', function ($query) use ($searchTerm) {
                    $query->where('code', 'ilike', "%$searchTerm%");
                });
            }
        ]);
        CRUD::column('is_active_for_quotes')->label('Вкл/Выкл в квотах')->type('check');
        CRUD::column('is_active_for_tk')->label('Вкл/Выкл для ТК')->type('check');
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
        CRUD::setValidation(FilialRequest::class);
        CRUD::field('filial_id')->label('Код филиала');
        CRUD::field('name');
        $this->crud->addField([  // Select
            'label' => "Регион",
            'type' => 'select',
            'name' => 'region', // the db column for the foreign key
            'entity' => 'region',
            // optional - manually specify the related model and attribute
            'model' => "App\Models\Region", // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
        ]);
        $this->crud->addField([
            'name' => 'is_active_for_quotes',
            'label' => 'Вкл/Выкл в квотах',
            'type' => 'checkbox',
            'wrapper' => [
                'class' => 'form-group col-md-3'
            ]
        ]);
        $this->crud->addField([
            'name' => 'is_active_for_tk',
            'label' => 'Вкл/Выкл для ТК',
            'type' => 'checkbox',
            'wrapper' => [
                'class' => 'form-group col-md-3'
            ]
        ]);
        $this->crud->addField([  // Select
            'label'  => "Склад",
            'type' => 'select_multiple',
            'name' => 'warehouses', // the db column for the foreign key
            'entity'=> 'warehouses',
            // optional - manually specify the related model and attribute
            'model' => "App\Models\Hru\Warehouse", // related model
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
