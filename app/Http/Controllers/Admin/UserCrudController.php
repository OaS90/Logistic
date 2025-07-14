<?php

namespace App\Http\Controllers\Admin;

use App\Infrastructure\Repositories\Admin\AdminUserRepository;
use App\Infrastructure\Repositories\Admin\TariffRepository;
use App\Infrastructure\Repositories\Admin\UserTariffPermissionRepository;
use Backpack\PermissionManager\app\Http\Controllers\UserCrudController as CrudController;
use Backpack\PermissionManager\app\Http\Requests\UserStoreCrudRequest as StoreRequest;
use Backpack\PermissionManager\app\Http\Requests\UserUpdateCrudRequest as UpdateRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

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

    protected function addUserFields(): void
    {
        $this->crud->addFields([
            [
                'name'  => 'name',
                'tab' => 'Пользователи',
                'label' => trans('backpack::permissionmanager.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'tab' => 'Настройки Тарифов',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [
                'name'  => 'password',
                'tab' => 'Пользователи',
                'label' => trans('backpack::permissionmanager.password'),
                'type'  => 'password',
            ],
            [
                'name'  => 'password_confirmation',
                'tab' => 'Пользователи',
                'label' => trans('backpack::permissionmanager.password_confirmation'),
                'type'  => 'password',
            ],
            [
                // two interconnected entities
                'label'             => trans('backpack::permissionmanager.user_role_permission'),
                'field_unique_name' => 'user_role_permission',
                'type'              => 'checklist_dependency',
                'tab' => 'Пользователи',
                'name'              => 'roles,permissions',
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
