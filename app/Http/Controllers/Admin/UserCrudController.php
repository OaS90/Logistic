<?php

namespace App\Http\Controllers\Admin;

use App\Infrastructure\Repositories\Admin\AdminUserRepository;
use App\Infrastructure\Repositories\Admin\TariffRepository;
use App\Infrastructure\Repositories\Admin\UserTariffPermissionRepository;
use Backpack\PermissionManager\app\Http\Controllers\UserCrudController as CrudController;
use Backpack\PermissionManager\app\Http\Requests\UserStoreCrudRequest as StoreRequest;
use Backpack\PermissionManager\app\Http\Requests\UserUpdateCrudRequest as UpdateRequest;

class UserCrudController extends CrudController
{
    protected UserTariffPermissionRepository $permissionRepo;
    protected TariffRepository $tariffRepo;

    public function __construct(UserTariffPermissionRepository $permissionRepo,
                                TariffRepository               $tariffRepo
    )
    {
        parent::__construct();
        $this->permissionRepo = $permissionRepo;
        $this->tariffRepo = $tariffRepo;
    }

    public function setupCreateOperation()
    {
        $this->addUserFields();
        $this->crud->setValidation(StoreRequest::class);
    }

    public function setupUpdateOperation()
    {
        $this->addUserFields();
        $this->crud->setValidation(UpdateRequest::class);
    }

    protected function addUserFields()
    {
        $this->crud->addFields([
            [
                'name'  => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type'  => 'text',
                'tab' => 'Пользователь'
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
                'tab' => 'Пользователь'
            ],
            [
                'name'  => 'password',
                'label' => trans('backpack::permissionmanager.password'),
                'type'  => 'password',
                'tab' => 'Пользователь'
            ],
            [
                'name'  => 'password_confirmation',
                'label' => trans('backpack::permissionmanager.password_confirmation'),
                'type'  => 'password',
                'tab' => 'Пользователь'
            ],
            [
                // two interconnected entities
                'label'             => trans('backpack::permissionmanager.user_role_permission'),
                'tab' => 'Пользователь',
                'field_unique_name' => 'user_role_permission',
                'type'              => 'checklist_dependency',
                'name'              => ['roles', 'permissions'],
                'subfields'         => [
                    'primary' => [
                        'label'            => trans('backpack::permissionmanager.roles'),
                        'name'             => 'roles', // the method that defines the relationship in your Model
                        'entity'           => 'roles', // the method that defines the relationship in your Model
                        'entity_secondary' => 'permissions', // the method that defines the relationship in your Model
                        'attribute'        => 'name', // foreign key attribute that is shown to user
                        'model'            => config('permission.models.role'), // foreign key model
                        'pivot'            => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns'   => 3, //can be 1,2,3,4,6
                    ],
                    'secondary' => [
                        'label'          => mb_ucfirst(trans('backpack::permissionmanager.permission_plural')),
                        'name'           => 'permissions', // the method that defines the relationship in your Model
                        'entity'         => 'permissions', // the method that defines the relationship in your Model
                        'entity_primary' => 'roles', // the method that defines the relationship in your Model
                        'attribute'      => 'name', // foreign key attribute that is shown to user
                        'model'          => config('permission.models.permission'), // foreign key model
                        'pivot'          => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns' => 3, //can be 1,2,3,4,6
                    ],
                ],
            ],
        ]);

        if (backpack_user()->hasRole('admin') && $this->crud->getCurrentEntryId()) {
            $this->crud->addField([
                'name'  => 'tariffPermissions',
                'type'  => 'tariff_permissions',
                'tab' => 'Настройки Тарифов',
                'value' => [
                    'tariffs' => $this->tariffRepo->getAll(),
                    'allowedTariffs' => $this->crud->getCurrentEntry()->tariffsPermissions,
                    'userId' => $this->crud->getCurrentEntryId()
                ]
            ]);
        }
    }
}