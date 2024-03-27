<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ApplicationObiRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ApplicationObiCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ApplicationObiCrudController extends CrudController
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
        CRUD::setModel(\App\Models\ApplicationObi::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/application-obi');
        CRUD::setEntityNameStrings('application obi', 'application obis');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('order_number')->label('Номер заказа');
        CRUD::column('order_type')->label('Тип заказа');
        CRUD::column('client_name')->label('ФИО');
        CRUD::column('phone')->label('Телефон');
        CRUD::column('delivery_date')->label('Дата доставки');
        CRUD::column('delivery_time')->label('Время доставки');
        CRUD::column('delivery_address')->label('Адрес доставки');
        CRUD::column('delivery_type')->label('Тип доставки');
        CRUD::column('delivery_zone')->label('Зона доставки');
        CRUD::column('over_delivery_zone_km')->label('Превышение зоны доставки');
        CRUD::column('order_weight')->label('Вес заказа');
        CRUD::column('lift_type')->label('Тип подъема');
        CRUD::column('lift_floor')->label('Этаж подъёма');
        CRUD::column('lift_weight_kg')->label('Вес подъем');
        CRUD::column('hand_lift_floor')->label('Этаж подъёма вручную');
        CRUD::column('hand_lift_weight_kg')->label('Вес подъёма вручную');
        CRUD::column('transfer_distance')->label('Расстояние переноса');
        CRUD::column('transfer_weight')->label('Вес переноса');
        CRUD::column('products_cost')->label('Стоимость доставляемого товара');
        CRUD::column('cost_of_transportation')->label('Стоимость услуг транспортировки');
        CRUD::column('lift_cost')->label('Стоимость услуг подъёма');
        CRUD::column('transfer_cost')->label('Стоимость услуг переноса');
        CRUD::column('total_delivery_cost')->label('Общая стоимость услуг доставки');
        CRUD::column('comment')->label('Комментарий к заявкеи');
        CRUD::column('status')->label('Статус');

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
        CRUD::setValidation(ApplicationObiRequest::class);

        CRUD::field('order_number')->label('Номер заказа')->wrapper([
            'class' => 'form-group col-md-3'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
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

        CRUD::field('order_type')->label('Тип заказа')->wrapper([
            'class' => 'form-group col-md-3'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('client_name')->label('ФИО')->wrapper([
            'class' => 'form-group col-md-5'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('phone')->label('Телефон')->wrapper([
            'class' => 'form-group col-md-3'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('delivery_date')->label('Дата доставки')->wrapper([
            'class' => 'form-group col-md-3'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('delivery_time')->label('Время доставки')->wrapper([
            'class' => 'form-group col-md-3'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('delivery_address')->label('Адрес доставки')->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('delivery_type')->label('Тип доставки')->wrapper([
            'class' => 'form-group col-md-3'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('delivery_zone')->label('Зона доставки')->wrapper([
            'class' => 'form-group col-md-3'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('over_delivery_zone_km')->label('Превышение зоны доставки')->wrapper([
            'class' => 'form-group col-md-4'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('order_weight')->label('Вес заказа')->wrapper([
            'class' => 'form-group col-md-2'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('lift_type')->label('Тип подъема')->wrapper([
            'class' => 'form-group col-md-3'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('lift_floor')->label('Этаж подъёма')->wrapper([
            'class' => 'form-group col-md-2'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('lift_weight_kg')->label('Вес подъем')->wrapper([
            'class' => 'form-group col-md-2'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('hand_lift_floor')->label('Этаж подъёма вручную')->wrapper([
            'class' => 'form-group col-md-3'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('hand_lift_weight_kg')->label('Вес подъёма вручную')->wrapper([
            'class' => 'form-group col-md-3'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('transfer_distance')->label('Расстояние переноса')->wrapper([
            'class' => 'form-group col-md-3'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('transfer_weight')->label('Вес переноса')->wrapper([
            'class' => 'form-group col-md-2'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('products_cost')->label('Стоимость доставляемого товара')->wrapper([
            'class' => 'form-group col-md-5'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('cost_of_transportation')->label('Стоимость услуг транспортировки')->wrapper([
            'class' => 'form-group col-md-5'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('lift_cost')->label('Стоимость услуг подъёма')->wrapper([
            'class' => 'form-group col-md-3'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('transfer_cost')->label('Стоимость услуг переноса')->wrapper([
            'class' => 'form-group col-md-3'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('total_delivery_cost')->label('Общая стоимость услуг доставки')->wrapper([
            'class' => 'form-group col-md-3'
        ])->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        CRUD::field('comment')->label('Комментарий к заявки')->tab('Заявка')->attributes([
            'class' => 'form-control',
            'readonly' => 'readonly',
        ]);
        foreach ($this->crud->getCurrentEntry()->products as $index => $product) {
            $number = $index + 1;
            CRUD::addField(
                [
                    'tab' => 'Товары',
                    'name' => 'product_' . $number,
                    'label' => 'Товар ' . $number,
                    'type' => 'text',
                    'value' => $product->name
                ]
            );
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
        $this->setupCreateOperation();
    }
}
