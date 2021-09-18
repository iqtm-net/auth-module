<?php

namespace App\Http\Controllers\api\Authintication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Config;
use Session;
use App\Deliver;
use App\User;
use App\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;


class MainAuth extends Controller
{ 
    public function authenticate(Request $request)
    {

        $type = array('admins', 'receivers', 'users', 'stores', 'delivers', 'taxis', 'companies');

        if(!PhoneFormat($request->get('phone_number'))) { return Result(Null, 400, 'invalid phone number'); }
        $phone_number = "9647".explode("7", AR_TO_EN($request->get('phone_number')), 2)[1];


        $credentials = $request->only('phone_number', 'password');
        $credentials['phone_number'] = $phone_number; // convert the inserted phone number to the standard syntax

        foreach ($type as $value =>  $key) {

            $check = DB::table($key)->where('phone_number', $credentials['phone_number'])->first();
            
            if ($check) {

                auth()->shouldUse($key);

                //Try to login
                try {
                    if (! $token = Auth($key)->claims(['Account_Role' => $key, 'unique' => $check->Code])->attempt($credentials)) {
                        return response()->json(['error' => 'invalid_credentials'], 400);
                    }
                }
                
                catch (JWTException $e) {  return response()->json(['error' => 'could_not_create_token'], 500); }
               
                if ($request->has('web')) {  return response()->json([ "token" => $token, "table_type" => $key, "value" => $value ], 200); }
                return Result(compact('token','value'), 200);


            }

        }

        return response()->json(['error' => 'invalid_credentials'], 400);

    }

    public function register(Request $request)
    {   
        
        $code = mt_rand(100000, 999999);

        $requestData = $request->all();
        
        $requestData['phone_number'] = PhoneFilter($requestData['phone_number']);
        $requestData['address_country'] = ar_english_country($request->get('address_country'));
        $requestData['address_state'] = ar_english_country($request->get('address_state'));
        $requestData['confirmed'] = 1;
        $requestData['balance'] = 0;
        $requestData['Code'] = Str::random(7);        
        $requestData['hashed_vefirication_code'] = Hash::make($code);
        $requestData['sms_code'] = $code;

        $validator = Validator::make($requestData, [
            //common
                'type' => ['required' => Rule::in(['admins', 'receivers', 'users', 'stores', 'delivers', 'taxis'])],
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone_number' => 'required|numeric|digits_between:10,13|unique:delivers|unique:admins|unique:users|unique:stores|unique:receivers|unique:taxis',
                'address_country' => 'required_unless:type,taxis|string|max:255',
                'address_state' => 'required_unless:type,taxis|string|max:255',
                'address_region' => 'required_unless:type,taxis|string|max:255',
                'password' => 'required|string|min:6|confirmed',
    
            //delivers
                'transport_type' => 'required_if:type,delivers|string|max:255',
                'size' => 'required_if:type,delivers|string|max:255',
                'shipping_type' => 'required_if:type,delivers|string|max:255',
                'IsCompany' => 'required_if:type,delivers|string|max:255',
    
            //stores
                'store_name' => 'required_if:type,stores|string|max:255',
                
            //taxis
                'car_type' => ['required_if:type,taxis' => Rule::in(['vip', 'normal'])],
                'car_color' => 'required_if:type,taxis|string|max:255',
                'car_docs_image' => 'required_if:type,taxis|string|max:255',
                'profile_picture' => 'required_if:type,taxis|string|max:255',
                'car_license_number' => 'required_if:type,taxis|string|max:255',
                'current_location' => 'required_if:type,taxis|string|max:255',
        ], CostumVal());

        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

        send_message($requestData['phone_number'], $code);

        $requestData['password'] = Hash::make($request->get('password'));

        $request->session()->put('Registration_Data', $requestData);

        return Result("Success");

    }

    public function confirm_code(Request $request)
    {

        if(!$request->session()->has('Registration_Data')){ return Result(Null, 400, "Not Allowed"); } 

        $requestData = $request->session()->get('Registration_Data');

        auth()->shouldUse($requestData['type']);

        if (Hash::check($request->get('enteredcode'), $requestData['hashed_vefirication_code'])) {

            //Create Account
            $creat = craet($requestData['type'], $requestData);

            //login & token
            $token = JWTAuth::claims(['Account_Role' => $requestData['type'], 'unique' => $creat->Code])->fromUser($creat);
            
            EventingPublic(
                $requestData['type'],
                $creat->id,
                "New ".$requestData['type']." Registered ".$requestData['first_name']." ".$requestData['last_name'],
                "/Admin_Accounts_".$requestData['type']."/1"
            );
            
            $request->session()->forget('Registration_Data');

            return Result([
                "message" => "Account has been created successfully",
                "token" => $token
            ], 201);

        }

        else{
            return Result("Wrong Code", 403);
        }

    }

    public function resetpass(Request $request)
    {
        $type = array('admins', 'receivers', 'users', 'stores', 'delivers', 'taxis', 'companies');

        foreach ($type as $key) {

            $phone_number = "9647".explode("7", AR_TO_EN($request->get('phone_number')), 2)[1];

            $check = DB::table($key)->where('phone_number', $phone_number)->first();

            if ($check) {

                //if step 2 submited
                if($request->has('enteredcode') and $request->has('sentcode')){

                    if (Hash::check($request->get('enteredcode'), $request->get('sentcode'))) {

                        if($request->has('password')){
                            //reset now !
                            $requestData = $request->only(['password']);
                            $requestData['password'] = Hash::make($request->get('password'));
                            DB::table($key)->where('phone_number', $phone_number)->update($requestData);
                            return Result("seccuss", 200);
                        }

                        return Result("seccuss", 200);
                    }
                    else{ return Result("wrong code", 400); }
                }

                $code = mt_rand(100000, 999999);

                send_message($phone_number, $code);

                $seccuss = "seccuss";

                $secret = Hash::make($code);
                
                return Result(compact('seccuss','secret'), 200);

            }
        }

        return Result("phone number wasn't found in our database", 400);

    }

    public function updateaccount(Request $request)
    {
        //validation
        $validator = Validator::make($request->all(), [ 
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'store_name' => 'string|max:255',
            'address_country' => 'string|max:255',
            'address_state' => 'string|max:255',
            'address_region' => 'string|max:255',
            'transport_type' => 'string|max:255',
            'shipping_type' => 'string|max:255',
            'size' => 'Numeric', 
            'IsCompany' => 'boolean',
            'available' => 'boolean',
            'Code' => 'unique:delivers|unique:admins|unique:users|unique:stores|unique:receivers|unique:taxis',
            'password' => 'string|min:6|confirmed',
        ]);

        if($validator->fails()){ return Result(Null, 202, $validator->errors()); }

        $AllowedFields = array_merge(array_diff(GetTBCol('delivers'), ['id' ,'Deliver_Fee', 'Deliver_Fee_Global', 'IsCompany', 'created_at', 'phone_number', 'remember_token', 'confirmed', 'sells','GD','credit','discounts', 'balance', 'sells','confirmed', 'debts']));

        $requestData = $request->only($AllowedFields); 

        if(count($requestData) > 0){
            if($request->has('password')){ $requestData['password'] = Hash::make($request->get('password'));  } //for password reset

            DB::table(user_role())->where('id', user()->id)->update($requestData);

            return Result("seccuss", 200);
        }


    }

    public function get_client_infos($cliend_id)
    {   
        
        $find_user = User::where('Code', $cliend_id)->first();
        $find_store = Store::where('Code', $cliend_id)->first();

        if($find_user){ 
            $client = $find_user->makeHidden(['id','confirmed','firebase_token','email','balance','created_at','updated_at','remember_token']);
            return Result($client, 200); 
        }
        if($find_store){ 
            $client = $find_store->makeHidden(['id','confirmed','firebase_token','email','balance','created_at','updated_at','remember_token']);
            return Result($client, 200); 
        }

        return Result('No client was found.', 400);
    }
    
    public function vue_session_store(Request $request)
    {   

        $validator = Validator::make($request->all(), [
            'Token' => 'required',
            'table_type' => 'required',
            'value' => 'required',
        ]);

        if($validator->fails()){ Result(Null, 400, $validator->errors()); }

        $request->session()->put('vue_session', $request->only('Token', 'table_type', 'value'));

        return Result();
    }

    public function vue_session_get(Request $request)
    {   
        return ($request->session()->exists('vue_session')) 
        ? $request->session()->get('vue_session')
        : Result(Null, 400, "No Session Found") ;

    }

    public function vue_session_forget(Request $request)
    {   
        $request->session()->forget('vue_session');

        return Result();

    }
}