<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Model\Company;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest');
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
            // 'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // 'companyName' => ['string', 'max:255'],
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
        $companyId = null;

        $user = User::create([
            // 'name' => $data['name'],
            'email' => $data['email'],
            'user_type' => $data['userType'],
            'password' => Hash::make($data['password']),
            'company_id' => $companyId
        ]);

        if($data['userType'] == 'C'){
            $companyName = $data['companyName'];
            // $companyImage = $data['companyImage'];
            $companyImage = null;
            $companyId = $this->registerCompany($companyName, $companyImage, $user);

            $user->company_id = $companyId;
            $user->save();
        }

        \Log::info($companyId);
        return $user;
    }
    protected function registered(Request $request, $user)
    {
        return $user;
    }
    private function registerCompany($companyName, $companyImage,$user)
    {
        \Log::info($user);
        // \Log::info($companyImage);


        $company = new Company();
        $company->user_id = $user->id;
        $company->company_name = $companyName;
        $company->company_image = $companyImage;

        $company->save();
        return $company->id;
    
    }
}