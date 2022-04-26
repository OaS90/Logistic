<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Infrastructure\Repositories\WarehouseRepository;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    protected $warehouseRepository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(WarehouseRepository $warehouseRepository)
    {
        $this->middleware('guest');
        $this->warehouseRepository = $warehouseRepository;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'firstname' => ['required', 'string'],
            'patronymic' => ['required', 'string'],
            'lastname' => ['required', 'string'],
            'position' => ['required', 'string'],
            'work_phone' => ['required', 'string'],
            'mobile_phone' => ['required'],
            'company' => ['required', 'string'],
            'inn' => ['required', 'integer', 'digits:10'],
            'kpp' => ['required', 'integer', 'digits:9'],
            'okpo' => ['required', 'integer', 'digits_between:8,10'],
            'legal_address' => ['required', 'string'],
            'warehouses' => ['required', 'min:1', 'array']
        ], [
            'required' => 'Обязательное поле',
            'password.confirmed' => 'Пароли должны совпадать',
            'password.min' => 'Пароль должен быть не короче 8 символов',
            'digits' => 'Поле должно содержать только цифры',
            'inn.digits' => 'ИНН должен содержать 10 символов',
            'kpp.digits' => 'КПП должен содержать 9 символов',
//            'mobile_phone.digits' => 'Полу должно содержать 11 символов',
            'okpo.digits_between' => 'ОКПО должен содержать от 8 до 10 символов',
            'integer' => 'Поле должно содержать только цифры',
            'email.unique' => 'Пользователь с таким email уже существует',
            'warehouses.min' => 'Обязательно указать хотя бы один склад'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'firstname' => $data['firstname'],
            'patronymic' => $data['patronymic'],
            'lastname' => $data['lastname'],
            'position' => $data['position'],
            'work_phone' => $data['work_phone'],
            'company' => $data['company'],
            'mobile_phone' => parse_phone($data['mobile_phone']),
            'inn' => $data['inn'],
            'kpp' => $data['kpp'],
            'okpo' => $data['okpo'],
            'legal_address' => $data['legal_address']
        ]);

        foreach ($data['warehouses'] as $warehouse) {
            $warehouse['user_id'] = $user->id;
            $this->warehouseRepository->create($warehouse);
        }

        return $user;
    }
}
