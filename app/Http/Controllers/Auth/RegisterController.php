<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Type;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'address' => ['required', 'string', 'max:255'],
            'net_number' => ['required', 'numeric', 'digits:11', 'unique:users' ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'cover' => ['nullable'],
            'types' => ['required']
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        // Aggiungo immagine se è presente
        if (isset($data['cover-image'])) {
            $img_path = Storage::put('cover', $data['cover-image']);

            if ($img_path) {
                $data['cover'] = $img_path;
            }
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'address' => $data['address'],
            'net_number' => $data['net_number'],
            'password' => Hash::make($data['password']),
            'cover' => isset($data['cover']) ? $data['cover'] : ''
        ]);

        $newUser = User::orderBy('id', 'desc')->first();
        $newUser->types()->attach($data['types']);

        return $newUser;
    }

    public function showRegistrationForm()
    {
        $types = Type::all();
        return view('auth.register', compact('types'));
    }

    
}
