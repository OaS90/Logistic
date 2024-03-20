<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TariffZonesRequest;
use App\Models\TariffCategoryPrices;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TariffZonesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TariffZonesCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation { destroy as traitDestroy; }

    public function __construct()
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
        CRUD::setModel(\App\Models\TariffZones::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/tariff-zones');
        CRUD::setEntityNameStrings('Зону', 'Зоны');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        if (backpack_user()->hasRole('admin')) {
            $this->crud->allowAccess(['update', 'delete', 'show', 'create']);
        } else {
            $this->crud->removeAllButtons();
        }


        CRUD::column('id')->type('number')->label('ID');
        CRUD::column('name')->type('string')->label('Наименование');
        CRUD::column('code')->type('string')->label('Код');

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
        CRUD::setValidation(TariffZonesRequest::class);

        CRUD::addField([
            'name' => 'id',
            'type' => 'number',
            'label' => 'ID',
            'attributes' => ['class' => 'form-control', 'readonly' => 'readonly'],
            'wrapper' => ['class' => 'form-group col-md-4']
        ]);
        CRUD::addField([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Наименование',
            'attributes' => ['class' => 'form-control'],
            'wrapper' => ['class' => 'form-group col-md-4']
        ]);
        CRUD::addField([
            'name' => 'code',
            'type' => 'text',
            'label' => 'Код',
            'attributes' => ['class' => 'form-control'],
            'wrapper' => ['class' => 'form-group col-md-4']
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
    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
    }

    public function destroy($id): bool|string
    {
        // TODO переписать под репозиторий
        TariffCategoryPrices::where('zone_id', $id)->delete();

        return CRUD::delete($id);
    }
}
