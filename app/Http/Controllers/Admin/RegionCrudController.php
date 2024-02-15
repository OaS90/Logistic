<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RegionRequest;
use App\Models\IntervalQuote;
use App\Models\Quote;
use App\Models\Warehouse;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\QuoteWarehouse;
use Illuminate\Support\Facades\DB;

/**
 * Class RegionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RegionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation { destroy as traitDestroy; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Region::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/regions');
        CRUD::setEntityNameStrings('region', 'regions');
        if (backpack_user()->hasRole('guest')) {
            $this->crud->denyAccess(['update', 'delete', 'show', 'create']);
        }
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
        CRUD::column('name')->label('Название региона');
        CRUD::column('region_id')->label('Регион Hru');
        $this->crud->addColumn([
            'name'  => 'warehouse',
            'label' => 'Склад', // Table column heading
            'type'  => 'model_function',
            'function_name' => 'getWarehouseNameAttribute'
        ]);
        CRUD::column('is_active_for_quotes')->label('Вкл/Выкл в квотах')->type('check');

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
        CRUD::setValidation(RegionRequest::class);

        $this->crud->addField([
            'name' => 'name',
            'label' => 'Название'
        ]);

        $this->crud->addField([
            'name' => 'region_id',
            'label' => 'Id региона Hru'
        ]);

        $this->crud->addField([
            'name' => 'is_active_for_quotes',
            'label' => 'Вкл/Выкл в квотах',
            'type' => 'checkbox'
        ]);

        $this->crud->addField([  // Select
            'label'     => "Склад",
            'type'      => 'select_multiple',
            'name'      => 'warehouse', // the db column for the foreign key
            'entity'    => 'warehouse',
            // optional - manually specify the related model and attribute
            'model'     => "App\Models\QuoteWarehouse", // related model
            'attribute' => 'warehouse_name', // foreign key attribute that is shown to user
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

    public function store()
    {
        // do something before validation, before save, before everything; for example:
        // $this->crud->addField(['type' => 'hidden', 'name' => 'author_id']);
        // $this->crud->removeField('password_confirmation');

        // Note: By default Backpack ONLY saves the inputs that were added on page using Backpack fields.
        // This is done by stripping the request of all inputs that do NOT match Backpack fields for this
        // particular operation. This is an added security layer, to protect your database from malicious
        // users who could theoretically add inputs using DeveloperTools or JavaScript. If you're not properly
        // using $guarded or $fillable on your model, malicious inputs could get you into trouble.

        // However, if you know you have proper $guarded or $fillable on your model, and you want to manipulate
        // the request directly to add or remove request parameters, you can also do that.
        // We have a config value you can set, either inside your operation in `config/backpack/crud.php` if
        // you want it to apply to all CRUDs, or inside a particular CrudController:
        // $this->crud->setOperationSetting('saveAllInputsExcept', ['_token', '_method', 'http_referrer', 'current_tab', 'save_action']);
        // The above will make Backpack store all inputs EXCEPT for the ones it uses for various features.
        // So you can manipulate the request and add any request variable you'd like.
        // $this->crud->getRequest()->request->add(['author_id'=> backpack_user()->id]);
        // $this->crud->getRequest()->request->remove('password_confirmation');
//        $this->crud->getRequest()->request->add(['warehouse_id'=> $this->crud->getRequest()->get('warehouse')]);
        $response = $this->traitStore();
        $newRegionId = $this->crud->getCurrentEntryId();

        $intervals = ['10-14', '14-18', '18-22', 'inDay', 'inHour'];
        if (!Quote::where('division_id', $newRegionId)->first()) {
            $quote = Quote::create(['division_id' => $newRegionId]);

            foreach ($intervals as $period) {
                IntervalQuote::create(['quote_id' => $quote->id, 'period' => $period]);
            }
        }

        // do something after save
        return $response;
    }

    public function destroy($id)
    {
        // при удаолении региона удаляем интервалы и квоты.
        // TODO переписать через репу
        $quote = Quote::where('division_id', $id)->first();
        $intervals = IntervalQuote::where('quote_id', $quote->id)->get();

        foreach ($intervals as $interval) {
            $interval->delete();
        }

        $quote->delete();

        return $this->crud->delete($id);
    }
}
