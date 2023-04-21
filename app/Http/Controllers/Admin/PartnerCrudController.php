<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\User;
use App\Http\Requests\PartnerRequest;

/**
 * Class PartnerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PartnerCrudController extends CrudController
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
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/partners');
        CRUD::setEntityNameStrings('partners', 'Партнёры');
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
        //$this->crud->removeAllButtons();

        $this->crud->denyAccess(['delete', 'show']);

        $this->crud->addColumn([
            'name' => 'id',
            'label' => 'Идентификатор'
        ]);

        $this->crud->addColumn([
            'name' => 'id_1c',
            'label' => 'Идентификатор 1с'
        ]);

        $this->crud->addColumn([
            'name' => 'firstname',
            'label' => 'Имя'
        ]);

        $this->crud->addColumn([
            'name' => 'patronymic',
            'label' => 'Отчество'
        ]);

        $this->crud->addColumn([
            'name' => 'lastname',
            'label' => 'Фамилия'
        ]);

        $this->crud->addColumn([
            'name' => 'lastname',
            'label' => 'Фамилия'
        ]);

        $this->crud->addColumn([
            'name' => 'mobile_phone',
            'label' => 'Мобильный телефон'
        ]);

        $this->crud->addColumn([
            'name' => 'position',
            'label' => 'Должность'
        ]);

        $this->crud->addColumn([
            'name' => 'work_phone',
            'label' => 'Рабочий телефон'
        ]);

        $this->crud->addColumn([
            'name' => 'additional_number',
            'label' => 'Добавочный номер'
        ]);

        $this->crud->addColumn([
            'name' => 'company',
            'label' => 'Компания'
        ]);

        $this->crud->addColumn([
            'name' => 'inn',
            'label' => 'ИНН'
        ]);

        $this->crud->addColumn([
            'name' => 'kpp',
            'label' => 'КПП'
        ]);

        $this->crud->addColumn([
            'name' => 'okpo',
            'label' => 'ОКПО'
        ]);

        $this->crud->addColumn([
            'name' => 'email',
            'label' => 'Email'
        ]);

        $this->crud->addColumn([
            'name' => 'legal_address',
            'label' => 'Юридический адрес'
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
        CRUD::setValidation(PartnerRequest::class);

//        CRUD::setFromDb(); // fields

        $this->crud->addField([
            'name' => 'id_1c',
            'label' => 'Идентификатор 1с'
        ]);

        $this->crud->addField([
            'name' => 'firstname',
            'label' => 'Имя'
        ]);

        $this->crud->addField([
            'name' => 'patronymic',
            'label' => 'Отчество'
        ]);

        $this->crud->addField([
            'name' => 'lastname',
            'label' => 'Фамилия'
        ]);

        $this->crud->addField([
            'name' => 'lastname',
            'label' => 'Фамилия'
        ]);

        $this->crud->addField([
            'name' => 'mobile_phone',
            'label' => 'Мобильный телефон'
        ]);

        $this->crud->addField([
            'name' => 'position',
            'label' => 'Должность'
        ]);

        $this->crud->addField([
            'name' => 'work_phone',
            'label' => 'Рабочий телефон'
        ]);

        $this->crud->addField([
            'name' => 'additional_number',
            'label' => 'Добавочный номер'
        ]);

        $this->crud->addField([
            'name' => 'company',
            'label' => 'Компания'
        ]);

        $this->crud->addField([
            'name' => 'inn',
            'label' => 'ИНН'
        ]);

        $this->crud->addField([
            'name' => 'kpp',
            'label' => 'КПП'
        ]);

        $this->crud->addField([
            'name' => 'okpo',
            'label' => 'ОКПО'
        ]);

        $this->crud->addField([
            'name' => 'email',
            'label' => 'Email'
        ]);

        $this->crud->addField([
            'name' => 'legal_address',
            'label' => 'Юридический адрес'
        ]);

//        $this->crud->addField([
//            'name' => 'password',
//            'label' => 'Пароль'
//        ]);


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
