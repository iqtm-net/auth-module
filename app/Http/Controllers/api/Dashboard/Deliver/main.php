<?php

namespace App\Http\Controllers\api\Dashboard\Deliver;

use App\Deliver;
use App\User_order;
use App\Orders_group;
use App\Forward_order;
use App\Member_stack;
use App\Forward_cart;
use App\Receiving_deliver;
use App\Gd_warehouse;
use App\Txt_service;
use App\Gd;
use App\Orders_black_list;
use Illuminate\Support\Facades\Hash;
use App\Order_status_changes_history;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
Use \Carbon\Carbon;
use App\Events\UpdateDeliverLocation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;


class main extends Controller
{

    public function check_deliver_code(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unique_code' => 'required|exists:delivers,code',
        ]);

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 400); }

        $get_deliver = Deliver::where('Code', $request->get('unique_code'))->first();

        $token = JWTAuth::claims(['Account_Role' => 'delivers', 'unique' => $get_deliver->Code])->fromUser($get_deliver);

        return response()->json([
            "token" => $token,
            "table_type" => 'delivers',
            "account" => $get_deliver,
            "value" => 4,
        ], 200);
    }

    public function add_delivers(Request $request)
    {

        // return response()->json($request->get('confirmed'), 400);

        $requestData = $request->all();
        $requestData['phone_number'] = "9647".explode("7", AR_TO_EN($request->get('phone_number')), 2)[1];


        $validator = Validator::make($requestData, [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'address_region' => 'required|string|max:50',
            'phone_number' => 'required|numeric|digits_between:10,13|unique:delivers|unique:users|unique:stores|unique:receivers',
            'password' => 'required|string|min:6|confirmed',
            'confirmed' => 'required',
        ],  CostumVal());

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 400); }

        $requestData['address_country'] = 'iraq';
        $requestData['address_state'] = 'Baghdad';
        $requestData['transport_type'] = 'Car';
        $requestData['shipping_type'] = 'both';
        $requestData['size'] = 100;
        $requestData['available'] = 1;
        $requestData['credit'] = 0;
        $requestData['password'] = Hash::make($request->get('password'));
        $requestData['Code'] = Str::random(7);
        $requestData['confirmed'] = ($request->get('confirmed') == "true") ? 1 : 0;

        Deliver::create($requestData);

        return response()->json("Seccuss", 200);
    }

}
