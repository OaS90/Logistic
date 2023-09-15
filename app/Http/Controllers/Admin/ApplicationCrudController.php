<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ApplicationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ApplicationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ApplicationCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Application::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/applications');
        CRUD::setEntityNameStrings('applications', 'Заявки');
        if (backpack_user()->hasRole('guest')) {
            $this->crud->denyAccess(['update', 'delete', 'show', 'create']);
        } else {
            $this->crud->denyAccess(['update']);
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
//        CRUD::setFromDb(); // columns
//        $this->crud->removeAllButtons();

        $this->crud->addColumn([
            'label' => "Партнёр", // Table column heading
            'type' => 'text',
            'name' => 'user_id',
            'priority' => 2,
        ]);

        $this->crud->addColumn([
            'label' => "Номер заказа", // Table column heading
            'name' => 'order_number'
        ]);

        $this->crud->addColumn([
            'label' => "Тип оплаты", // Table column heading
            'name' => 'payment_type',
            'priority' => 2,
        ]);

        $this->crud->addColumn([
            'label' => "Дата доставки", // Table column heading
            'name' => 'delivery_date',
            'priority' => 1,
        ]);

        $this->crud->addColumn([
            'label' => "Время доставки", // Table column heading
            'name' => 'delivery_time',
            'priority' => 2,
        ]);

        $this->crud->addColumn([
            'label' => "Адрес доставки", // Table column heading
            'name' => 'delivery_address',
            'type' => 'closure',
            'function' => function ($entry) {
                return $entry->full_address;
            },
            'priority' => 2,
        ]);

        $this->crud->addColumn([
            'label' => "Адрес склада", // Table column heading
            'name' => 'warehouse_id',
            'type'  => 'closure',
            'function' => function ($entry) {
                return $entry->warehouse->address;
            },
            'priority' => 2,
        ]);

        $this->crud->addColumn([
            'label' => "Комментарий", // Table column heading
            'name' => 'comment',
            'priority' => 4,
        ]);

        $this->crud->addColumn([
            'label' => "Статус", // Table column heading
            'name' => 'status',
            'type'  => 'closure',
            'function' => function ($entry) {
                return $entry->getStatus($entry->status);
            },
            'priority' => 1,
        ]);

        $this->crud->addColumn([
            'label' => "Дата создания", // Table column heading
            'type' => 'datetime',
            'name' => 'created_at',
            'format' => 'DD.MM.Y H:mm:s',
            'priority' => 1,
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
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ApplicationRequest::class);

        CRUD::setFromDb(); // fields

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
