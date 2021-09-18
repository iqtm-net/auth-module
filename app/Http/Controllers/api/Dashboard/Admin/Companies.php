<?php

namespace App\Http\Controllers\api\Dashboard\Admin;

use App\Deliver;
use App\User;
use App\Store;
use App\Receiver;
use App\Company;
use App\User_order;
use App\Option;
use App\Orders_group;
use App\Notification;
use App\Member_stack;
use App\Credits_order;
use App\Withdraw_order;
use App\Order_status_changes_history;
use App\Exports\Withdraw;
Use \Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use File;
use Illuminate\Validation\Rule;


class Companies extends Controller
{
     
    public function change_receiving_company(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:companies,id',
        ],  CostumVal());

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 400); }

        $get = Option::where('option', 'receiving_company_id')->first();
        $get->value = $request->get('id');
        $get->save();

        return response()->json($get);
    }

    public function Add_Companie(Request $request)
    {

        $requestData = $request->all();
        $requestData['phone_number'] = "9647".explode("7", AR_TO_EN($request->get('phone_number')), 2)[1];


        $validator = Validator::make($requestData, [
            'name' => 'required|string|max:50',
            'phone_number' => 'required|numeric|digits_between:10,13|unique:delivers|unique:users|unique:stores|unique:receivers|unique:companies',
            'password' => 'required|string|min:6|confirmed',
            'confirmed' => 'required',
            'label_counter_start' => 'required|numeric',
        ],  CostumVal());

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 400); }

        $requestData['password'] = Hash::make($request->get('password'));
        $requestData['Code'] = Str::random(7);
        $requestData['confirmed'] = ($request->get('confirmed') == "true") ? 1 : 0;

        Company::create($requestData);

        return response()->json("Seccuss", 200);
    }

    public function get_companies()
    {
        $get = Company::get();
        return response()->json($get);
    }

    public function current_receiving_company()
    {   
        $get = Option::where('option', 'receiving_company_id')->first()->company;
        return response()->json($get);
    }
    
    public function Transfer_order(Request $request)
    {   

            $validator = Validator::make($request->all(), [
                'company_id' => 'required|exists:companies,id',
            ],  CostumVal());
            
            if($validator->fails()){ return Result($validator->errors(), 400); }
            
            $company = Company::find($request->get('company_id'));

            foreach(json_decode($request->get('order_ids')) as $order_id){

                $update = User_order::where('id', $order_id->id)->first();
                $update->handeled_by = $request->get('company_id');
                $update->save();
                
                //Set in history
                $Order_status_changes_history = new Order_status_changes_history;
                $Order_status_changes_history->order_id = $update->id;
                $Order_status_changes_history->deliver_id = null;
                $Order_status_changes_history->user_id = $update->user_id;
                $Order_status_changes_history->account_type = $update->account_type;
                $Order_status_changes_history->status = 'transfered to'.$company->name;
                $Order_status_changes_history->save();

            }
            
            Record_action("changed orders status of ".$request->get('order_ids')." to ".$request->get('status'));

            return Result();

    }

    public function search_for_companies($keyword)
    {

        $check = Company::where('phone_number', "LIKE", "%".$keyword."%")
            ->orWhere('name', "LIKE", "%".urldecode($keyword)."%")
            ->orWhere('Code', "LIKE", "%".$keyword."%")
            ->get();

        return response()->json($check, 200);
    }
    
    public function status_tracking_history($user_id, $user_role){

        $get_his_orders = Order_status_changes_history::orderBy('id','DESC')
        ->when($user_role == 'delivers' && $user_id !== 'All' , function ($q) use($user_id) { return $q->where('deliver_id', $user_id); })
        ->when($user_role !== 'delivers' && $user_id !== 'All', function ($q) use($user_id,$user_role) { return $q->where('user_id', $user_id)->where('account_type', $user_role); })
        ->paginate(50);

        $get_his_orders->map(function ($item) {
            $item['order_data'] = $item->order;
            return $item;
        });

        return response()->json($get_his_orders, 200);
    }

    public function search_status_history_by_id($order_id)
        {
            $keywordF = urldecode($order_id);

            $validator = Validator::make([
                "track_code" => $keywordF
            ], [
                'track_code' => 'required|exists:user_orders,track_code',
            ]);

            if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 400); }
            
            $order = User_order::where('track_code', $keywordF)->first();

            $get_his_orders = Order_status_changes_history::where('order_id', $order->id)->get();

            $get_his_orders->map(function ($item) {
                $item['order_data'] = $item->order;
                return $item;
            });

            return response()->json($get_his_orders, 200);
        }
}
