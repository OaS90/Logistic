<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TariffCategoriesRequest;
use App\Infrastructure\Repositories\Admin\TariffCategorySettingsRepository;
use App\Infrastructure\Repositories\Admin\TariffRepository;
use App\Infrastructure\Repositories\RegionRepository;
use App\Models\TariffCategoryPrices;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TariffCategoriesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TariffCategoriesCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation { destroy as traitDestroy; }

    protected RegionRepository $regionRepo;
    protected TariffRepository $tariffRepo;
    protected TariffCategorySettingsRepository $tariffCategorySettingsRepo;

    public function __construct(RegionRepository $regionRepo,
                                TariffRepository $tariffRepo,
                                TariffCategorySettingsRepository $tariffCategorySettingsRepo
    )
    {
        parent::__construct();
        $this->regionRepo = $regionRepo;
        $this->tariffRepo = $tariffRepo;
        $this->tariffCategorySettingsRepo = $tariffCategorySettingsRepo;
    }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\TariffCategories::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/tariff-categories');
        CRUD::setEntityNameStrings('Категорию', 'Категории');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation(): void
    {
        CRUD::column('id')->type('number')->label('ID');
        CRUD::column('name')->type('text')->label('Наименование');
        CRUD::column('category_id')->type('number')->label('ID категории');
        CRUD::column('description')->type('textarea')->label('Описание');

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
        CRUD::setValidation(TariffCategoriesRequest::class);
        CRUD::addField([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Наименование',
            'attributes' => ['class' => 'form-control'],
            'wrapper' => ['class' => 'form-group col-md-5']
        ]);
        CRUD::addField([
            'name' => 'category_id',
            'type' => 'number',
            'label' => 'ID категории',
            'attributes' => ['class' => 'form-control'],
            'wrapper' => ['class' => 'form-group col-md-3']
        ]);

        CRUD::addField([
            'name' => 'description',
            'type' => 'textarea',
            'label' => 'Описание',
            'attributes' => ['class' => 'form-control'],
            'wrapper' => ['class' => 'form-group col-md-3']
        ]);

//        CRUD::addField([
//            'label' => 'Регионы',
//            'type' => 'select_multiple',
//            'name' => 'regions', // the method that defines the relationship in your Model
//
//            // optional
//            'entity' => 'regions', // the method that defines the relationship in your Model
//            'model'=> "App\Models\Region", // foreign key model
//            'attribute' => 'name', // foreign key attribute that is shown to user
//            'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
//        ]);
//        CRUD::addField(['name' => 'price', 'type' => 'number']);
//        CRUD::addField(['name' => 'price', 'type' => 'number']);
//        CRUD::addField(['name' => 'price', 'type' => 'number']);
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

    public function store(): \Illuminate\Http\RedirectResponse
    {
        $response = $this->traitStore();
        $categoryId = $this->crud->getCurrentEntryId();

        foreach ($this->tariffRepo->getAll() as $tariff) {
            foreach ($this->regionRepo->getAll() as $region) {
                if (!$this->regionRepo->getTariffCategoryById($region, $categoryId)) {
                    $region->tariffCategories()->attach($categoryId);
                }

                $this->tariffCategorySettingsRepo->create($tariff->id, $region->id, $categoryId);
            }
        }

        return $response;
    }

    public function destroy($id): bool|string
    {
        foreach ($this->tariffRepo->getAll() as $tariff) {
            foreach ($this->regionRepo->getAll() as $region) {
                $this->regionRepo->deleteTariffCategoryById($region, $id);
                $this->tariffCategorySettingsRepo->delete($tariff->id, $region->id, $id);
            }
        }

        // TODO переписать под репозиторий
        TariffCategoryPrices::where('category_id', $id)->delete();

        return CRUD::delete($id);
    }
}
