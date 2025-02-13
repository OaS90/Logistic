<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ApplicationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Infrastructure\Repositories\ProductRepository;
use App\Infrastructure\Repositories\ApplicationRepository;

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

    private ProductRepository $productRepo;
    private ApplicationRepository $appRepo;

    public function __construct(ProductRepository $productRepo, ApplicationRepository $appRepo)
    {
        $this->productRepo = $productRepo;
        $this->appRepo = $appRepo;
        parent::__construct();
    }

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
        if (backpack_user()->hasRole('guest')) {
            $this->crud->denyAccess(['update', 'delete', 'show', 'create']);
        } else {
            $this->crud->denyAccess(['show']);
        }

        $this->crud->addColumn([
            'label' => "Партнёр", // Table column heading
            'type' => 'closure',
            'name' => 'user_id',
            'function' => function ($entry) {
                return $entry->user->company;
            },
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

        CRUD::addField([
            'tab' => 'Заявка',
            'fake' => true,
            'name' => 'id',
            'type' => 'number',
            'label' => 'Id',
            'attributes' => [
                'class' => 'form-control',
                'readonly' => 'readonly',
            ],
            'wrapper' => [
                'class' => 'form-group col-md-3'
            ]
        ]);

        CRUD::addField([
            'tab' => 'Заявка',
            'fake' => true,
            'name' => 'order_number',
            'type' => 'text',
            'label' => 'Номер заказа',
            'attributes' => [
                'class' => 'form-control',
                'readonly' => 'readonly',
            ],
            'wrapper' => [
                'class' => 'form-group col-md-3'
            ]
        ]);

        CRUD::addField([
            'tab' => 'Заявка',
            'fake' => true,
            'name' => 'payment_type',
            'type' => 'text',
            'label' => 'Тип оплаты',
            'attributes' => [
                'class' => 'form-control',
                'readonly' => 'readonly',
            ],
            'wrapper' => [
                'class' => 'form-group col-md-3'
            ]
        ]);

        CRUD::addField([
            'tab' => 'Заявка',
            'fake' => true,
            'name' => 'delivery_date',
            'type' => 'text',
            'label' => 'Дата доставки',
            'attributes' => [
                'class' => 'form-control',
                'readonly' => 'readonly',
            ],
            'wrapper' => [
                'class' => 'form-group col-md-3'
            ]
        ]);

        CRUD::addField([
            'tab' => 'Заявка',
            'fake' => true,
            'name' => 'delivery_time',
            'type' => 'text',
            'label' => 'Время доставки',
            'attributes' => [
                'class' => 'form-control',
                'readonly' => 'readonly',
            ],
            'wrapper' => [
                'class' => 'form-group col-md-3'
            ]
        ]);

        CRUD::addField([
            'tab' => 'Заявка',
            'fake' => true,
            'name' => 'delivery_time',
            'type' => 'text',
            'label' => 'Время доставки',
            'attributes' => [
                'class' => 'form-control',
                'readonly' => 'readonly',
            ],
            'wrapper' => [
                'class' => 'form-group col-md-3'
            ]
        ]);

        CRUD::addField([   // CustomHTML
            'tab' => 'Заявка',
            'fake' => true,
            'name'  => 'status',
            'type'  => 'text',
            'value' => $this->crud->getCurrentEntry()->getStatus($this->crud->getCurrentEntry()->status),
            'label' => 'Статус',
            'attributes' => [
                'class' => 'form-control',
                'readonly' => 'readonly',
            ],
            'wrapper' => [
                'class' => 'form-group col-md-3'
            ]
        ]);

        CRUD::addField([   // CustomHTML
            'tab' => 'Заявка',
            'fake' => true,
            'name'  => 'delivery_address',
            'type'  => 'text',
            'value' => $this->crud->getCurrentEntry()->address->full_address,
            'label' => 'Адрес',
            'attributes' => [
                'class' => 'form-control',
                'readonly' => 'readonly',
            ],
        ]);

        CRUD::addField([   // CustomHTML
            'tab' => 'Заявка',
            'fake' => true,
            'name'  => 'warehouse_address',
            'type'  => 'text',
            'value' => $this->crud->getCurrentEntry()->warehouse->address,
            'label' => 'Склад',
            'attributes' => [
                'class' => 'form-control',
                'readonly' => 'readonly',
            ],
        ]);

        CRUD::addField([   // CustomHTML
            'tab' => 'Заявка',
            'fake' => true,
            'name'  => 'separator_1',
            'type'  => 'custom_html',
            'value' => '<hr>'
        ]);

        CRUD::addField([   // CustomHTML
            'tab' => 'Заявка',
            'fake' => true,
            'name'  => 'client_title',
            'type'  => 'custom_html',
            'value' => '<h4>Клиент</h4>'
        ]);

        CRUD::addField([
            'tab' => 'Заявка',
            'fake' => true,
            'name' => 'client_name',
            'type' => 'text',
            'label' => 'ФИО',
            'attributes' => [
                'class' => 'form-control',
                'readonly' => 'readonly',
            ],
            'wrapper' => [
                'class' => 'form-group col-md-4'
            ]
        ]);

        CRUD::addField([
            'tab' => 'Заявка',
            'fake' => true,
            'name' => 'client_phone',
            'type' => 'text',
            'label' => 'Телефон',
            'attributes' => [
                'class' => 'form-control',
                'readonly' => 'readonly',
            ],
            'wrapper' => [
                'class' => 'form-group col-md-3'
            ]
        ]);

        CRUD::addField([
            'tab' => 'Заявка',
            'fake' => true,
            'name' => 'comment',
            'type' => 'textarea',
            'label' => 'Комментарий',
            'attributes' => [
                'class' => 'form-control',
                'readonly' => 'readonly',
            ],
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ]
        ]);

        foreach ($this->crud->getCurrentEntry()->products as $id => $product) {
            $num = $id + 1;

            CRUD::addField([   // CustomHTML
                'tab' => 'Товары',
                'name'  => 'title_' . $product->id,
                'type'  => 'custom_html',
                'value' => '<h2>Товар № ' . $num . '</h2>'
            ]);

            CRUD::addField([   // CustomHTML
                'tab' => 'Товары',
                'name'  => 'product_name_' . $product->id,
                'type'  => 'text',
                'value' => $product->name,
                'label' => 'Наименование',
                'attributes' => [
                    'class' => 'form-control',
                    'readonly' => 'readonly',
                ],
            ]);

            CRUD::addField([
                'tab' => 'Товары',
                'name'  => 'sku_' . $product->id,
                'type'  => 'text',
                'value' => $product->sku,
                'label' => 'Артикул',
                'wrapper' => [
                    'class' => 'form-group col-md-3'
                ]
            ]);

            CRUD::addField([
                'tab' => 'Товары',
                'name'  => 'barcode_' . $product->id,
                'type'  => 'number',
                'value' => $product->barcode,
                'label' => 'Баркод',
                'wrapper' => [
                    'class' => 'form-group col-md-3'
                ]
            ]);

            CRUD::addField([
                'tab' => 'Товары',
                'name'  => 'tnved_' . $product->id,
                'type'  => 'number',
                'value' => $product->tnved,
                'label' => 'ТНВЭД',
                'wrapper' => [
                    'class' => 'form-group col-md-3'
                ]
            ]);

            CRUD::addField([
                'tab' => 'Товары',
                'name'  => 'cost_' . $product->id,
                'type'  => 'number',
                'value' => $product->cost,
                'label' => 'Цена',
                'wrapper' => [
                    'class' => 'form-group col-md-3'
                ]
            ]);

            CRUD::addField([   // CustomHTML
                'tab' => 'Товары',
                'name'  => 'separator',
                'type'  => 'custom_html',
                'value' => '<hr>'
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
    protected function setupUpdateOperation()
    {
        $request = $this->crud->getRequest()->all();
        $currentApp = $this->crud->getCurrentEntry();

        foreach ($request as $field => $param) {
            if (str_contains($field, 'sku_')) {
                $exploded = explode('_', $field);
                $productId = $exploded[1];
                $tnved = $request['tnved_' . $productId];
                $barcode = $request['barcode_' . $productId];
                $cost = $request['cost_' . $productId];
                $product = $this->productRepo->getById($productId);

                if ($product->tnved != $tnved || $product->barcode != $barcode || $product->cost != $cost) {
                    $this->appRepo->updateByFields($currentApp->order_number, ['doc_ver' => $currentApp->doc_ver + 1]);
                    $this->productRepo->updateByFields($productId, [
                        'tnved' => $tnved,
                        'barcode' => $barcode,
                        'cost' => $cost
                    ]);
                }
            }
        }

        $this->setupCreateOperation();
    }
}
