<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TransportCompanyWarehouseRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Hru\Warehouse;

/**
 * Class TransportCompanyWarehouseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class HruWarehouseCrudController extends CrudController
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
    public function setup(): void
    {
        CRUD::setModel(Warehouse::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/hru-warehouses');
        CRUD::setEntityNameStrings('склад', 'склады');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation(): void
    {
        $this->crud->removeAllButtons();
        CRUD::column('id');
        CRUD::addColumn([  // Select
            'name'  => 'region_name',
            'label' => 'Регион', // Table column heading
            'type'  => 'model_function',
            'function_name' => 'getRegionName', // the method in your Model
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('region', function ($query) use ($searchTerm) {
                    $query->where('name', 'ilike', "%$searchTerm%");
                });
            }
        ]);
        CRUD::addColumn([
            'name' => 'name',
            'label' => 'Наименование склада',
            'type' => 'text',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $searchTerm = ucfirst($searchTerm);
                $query->orWhere('name', 'like', "%$searchTerm%");
            }
        ]);
        CRUD::column('code')->type('text')->label('Код склада');
        CRUD::addColumn([
            'name' => 'filials',
            'label' => 'Филиалы',
            'type'     => 'closure',
            'function' => function($entry) {
                $strFilial = '';

                if ($entry->filials->count() > 0) {
                    $strFilial = implode(', ', array_column($entry->filials->toArray(), 'name'));
                }

                return $strFilial;
            }
        ]);
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
        CRUD::setValidation(TransportCompanyWarehouseRequest::class);
        CRUD::field('name')->type('text')->label('Наименование склада');
        CRUD::field('code')->type('number')->label('Код склада');
        CRUD::addField([  // Select
            'label' => "Регион",
            'type' => 'select',
            'name' => 'region_id', // the db column for the foreign key
            // optional - manually specify the related model and attribute
            'model' => "App\Models\Region", // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
        ]);
        $this->crud->addField([
            'name' => 'separator',
            'type' => 'custom_html',
            'value' => '<hr> <b>Филиалы: </b>'
        ]);

        foreach ($this->crud->getCurrentEntry()->filials as $filial) {
            $this->crud->addField([
                'type' => 'custom_html',
                'name' => 'filial_' . $filial->id,
                'value' => '- ' . $filial->name . ' (' . sprintf('%05d', $filial->filial_id) . ')'
            ]);
        }

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
}
