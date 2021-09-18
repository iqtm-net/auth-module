<?php

    namespace App\Http\Controllers\api\Dashboard\Admin;

    use App\Discount_code;
    use App\Store;
    use App\User;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Tymon\JWTAuth\JWTAuth;

    class Discounts extends Controller
    {
        
        public function get()
        {
            $get = Discount_code::paginate(50);
            return response()->json($get, 200);
        }

        public function Add(Request $request)
        {   
            Discount_code::create($request->all());
            return response()->json(Null, 200);
        }

        public function Delete(Request $request)
        {   
            Discount_code::find($request->get('id'))->delete();
            return response()->json(Null, 200);
        }
        
        public function add_clients_discounts_code($keyword)
        {   

            $stores = Store::
            where('phone_number', "LIKE", "%".$keyword."%")
            ->orWhere('first_name', "LIKE", "%".urldecode($keyword)."%")
            ->orWhere('store_name', "LIKE", "%".urldecode($keyword)."%")
            ->orWhere('last_name', "LIKE", "%".urldecode($keyword)."%")
            ->orWhere('code', "LIKE", "%".$keyword."%")
            ->paginate(3)
            ->map(function ($account){
                $account['account_type'] = 3;
                return $account;
            })
            ->toArray();

            $users = User::where('phone_number', "LIKE", "%".$keyword."%")
            ->orWhere('first_name', "LIKE", "%".urldecode($keyword)."%")
            ->orWhere('last_name', "LIKE", "%".urldecode($keyword)."%")
            ->orWhere('code', "LIKE", "%".$keyword."%")
            ->paginate(3)
            ->map(function ($account){
                $account['account_type'] = 2;
                return $account;
            })
            ->toArray();
            
            $resp = array_merge($stores, $users);
            
            return response()->json($resp, 200);
            
        }

        public function pin_discounts_code(Request $request){

            Discount_code::where('pin', 1)->update([ "pin" => 0 ]);
            Discount_code::find($request->get('id'))->update([ "pin" => 1 ]);

            // find($request->get('id'))->
            return response()->json(Null, 200);
        } 

        public function pinned_discounts(){

            $get = Discount_code::where('pin', 1)->first();

            return Result($get, 200);
        } 
    }
