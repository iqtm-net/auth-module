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

class Accounts extends Controller
{
    
    public function AddCreditToGd(Request $request)
    {   
        $validator = Validator::make($request->all(), [ 'deliver_id' => 'required|Numeric'], CostumVal());
            if($validator->fails()){  return Result(Null, 400, $validator->errors()); }

        $update_Car_Credit = Deliver::find($request->get('deliver_id'));
        $update_Car_Credit->credit = $update_Car_Credit->credit + $request->get('credit');
        $update_Car_Credit->save();

        return response()->json(null, 200);
    }

    public function MemberUpgrade(Request $request)
    {
        $current_role = $request->get('current_role');
        $account_id = $request->get('account_id');
        $upgrade_to = $request->get('upgrade_to');

        if ($upgrade_to == $current_role) { return response()->json(null, 203); }

        if ($current_role == 'users') { $info = User::find($account_id)->makeVisible(['password'])->toArray(); }
        elseif ($current_role == 'stores') { $info = Store::find($account_id)->makeVisible(['password'])->toArray(); }
        elseif ($current_role == 'delivers') { $info = Deliver::find($account_id)->makeVisible(['password'])->toArray(); }

        $info['id'] = null;
        $info['store_name'] = 'change name';
        $info['IsCompany'] = 0;
        $info['debts'] = 0;
        $info['Deliver_Fee'] = 0;
        $info['Deliver_Fee_Global'] = 0;
        $info['sells'] = 0;
        $info['credit'] = 0;
        $info['discounts'] = 0;
        $info['delivery_type'] = 'توصيل خلال ٢٤-٧٢ ساعة - عادي';
        $info['transport_type'] = 'car';
        $info['shipping_type'] = 'local';
        $info['available_weight'] = 50;
        $info['available_length'] = 50;
        $info['GD'] = 0;

        if ($upgrade_to == 'users') {
            $up = User::create($info);
            if ($current_role == 'stores') {
                User_order::where('user_id', $account_id)->where('account_type', 3)->update(["user_id" => $up->id, "account_type" => 2]);
                Orders_group::where('member_id', $account_id)->where('account_type', 3)->update(["member_id" => $up->id, "account_type" => 2]);
            }

        }
        elseif ($upgrade_to == 'stores') {
            $up = Store::create($info);
            if ($current_role == 'users') {
                User_order::where('user_id', $account_id)->where('account_type', 2)->update(["user_id" => $up->id, "account_type" => 3]);
                Orders_group::where('member_id', $account_id)->where('account_type', 3)->update(["member_id" => $up->id, "account_type" => 3]);
            }
        }
        elseif ($upgrade_to == 'delivers') {
            Deliver::create($info);
            $account_type = ($current_role == 'users') ? 2 : 3;
            User_order::where('user_id', $account_id)->where('account_type', $account_type)->delete();
        }

        $info = DB::table($current_role)->where('id',$account_id)->delete();

        return response()->json(null, 200);
    }

    ///=================================================== Withdrawing system
    public function Order_withdraw(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'withdraw_method' => ['required', Rule::in(['CreditCard','ZainCash','QiCard','Cash'])],
            'card_or_phone_number' => 'required|Numeric'
        ],  CostumVal());

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }

        if (user()->balance < 80) { return Result("Can not Withdraw with a balance less than 80", 400); }
        if (Withdraw_order::where('memebr_id', user()->id)->where('status', 0)->where('memebr_role', user_role())->first()) 
            { return Result("You have already requested a withdraw", 400); }

        $new = new Withdraw_order;
        $new->withdraw_method = $request->get('withdraw_method');
        $new->memebr_id = user()->id;
        $new->memebr_role = user_role();
        $new->status = 0;
        $new->balance = 0;
        // $new->type = $request->get('type');
        $new->card_or_phone_number = $request->get('card_or_phone_number');
        $new->save();

        Eventing(
            "New ".user_role()." Withdraw Order From (".user()->first_name." ".user()->last_name.")" ,
            "withdraw_orders_".user_role()."/1", );

        return Result("seccus", 200, Null);
    }

    public function DownloadWithdrawOrders(Request $request){
        $validator = Validator::make($request->all(), [
            'status' => 'required|Boolean',
            'names' => 'required'
        ],  CostumVal());

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); } 

        return Excel::download(new Withdraw($request->get('status'), json_decode($request->get('names'))), Carbon::now().'.xlsx');
    }

    public function withdraw_orders($id, $role, $status)
    {
        $Get = Withdraw_order::latest("created_at")
        ->when($id !== "All" && $role !== "All", function ($q) use($id, $role) { $q->where('memebr_id', $id)->where('memebr_role', $role); })
        ->where('status', $status)
        ->paginate(50)
        ->map(function ($Order) use ($status,$id,$role){

            //$Member = DB::table($role)->where('id', $id)->first();
            $Member = table_byAccountType($Order->memebr_role, $Order->memebr_id);

            $Order['Member'] = $Member;
            $Order['Member_Role'] = $Order->memebr_role;
            $Order['Member_Role_Number'] = ($Order->memebr_role == 'users') ? 2 : 3;
            $Order['balance'] = ($status == 1) ? $Order->balance : $Member->balance;

            return $Order;
            
        });

        return response()->json($Get, 200);

    }

    public function Accept_withdraw(Request $request)
    {
        foreach ($request->all() as $order_id) {

            $Order = Withdraw_order::find($order_id);

            if ($Order->memebr_role == "users") {
                $update = User::find($Order->memebr_id);
            }
            elseif($Order->memebr_role == "stores"){
                $update = Store::find($Order->memebr_id);
            }
            
            //update order
            $Order->status = 1;
            $Order->balance = $update->balance;
            $Order->save();

            //update member
            $update->balance = 0;
            $update->save();

            Record_action("withdrawn ".$Order->balance." from ".$update->first_name." ".$update->last_name);

            Notification([$update->firebase_token], 'تمت عملية التحويل', 'تم ايداع المبلغ المالي الخاص بك في محفظتك في زين كاش', $update->phone_number);

        }

        return response()->json(null, 200);
    }


    public function Add_Support(Request $request)
    {

        // return response()->json($request->get('confirmed'), 400);

        $requestData = $request->all();
        $requestData['phone_number'] = "9647".explode("7", AR_TO_EN($request->get('phone_number')), 2)[1];


        $validator = Validator::make($requestData, [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'address' => 'required|string|max:50',
            'phone_number' => 'required|numeric|digits_between:10,13|unique:delivers|unique:users|unique:stores|unique:receivers',
            'password' => 'required|string|min:6|confirmed',
            'confirmed' => 'required',
            'premissions' => 'required',
        ],  CostumVal());

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 400); }

        $requestData['password'] = Hash::make($request->get('password'));
        $requestData['Code'] = Str::random(7);
        $requestData['confirmed'] = ($request->get('confirmed') == "true") ? 1 : 0;

        Receiver::create($requestData);

        return response()->json("Seccuss", 200);
    }

    public function block_account(Request $request)
    {
        $id = $request->only('id')['id'];
        $posts = Receiver::find($id)->delete();
        return response()->json(null, 200);
    }

    public function get_premissions()
    {
        return config('constants.Support_Premissions');
    }
    
    public function get_table_accounts($type)
    {
        $user = DB::table($type)->latest()->paginate(50);
        return response()->json($user);
    }

    // public function get_account($type, $id)
    // {
    //     $user = DB::table($type)->where('id', $id)->first();
    //     if (!$user) { return response()->json(null, 203); }
        
    //     $user->waiting_ords = OrdersCounter($id, $type, 'waiting');
    //     $user->pending_ords = OrdersCounter($id, $type, 'pending');
    //     $user->delivered_ords = OrdersCounter($id, $type, 'delivered');
    //     $user->issued_ords = OrdersCounter($id, $type, 'issued');

    //     return response()->json([ "customer" => $user ], 200);
    // }

    public function get_account_by_phone($type, $phone_number)
    {
        $user = DB::table($type)->where('phone_number', $phone_number)->first();

        if (!$user) {
            return response()->json(null, 203);
        }

        return response()->json([ "customer" => $user ], 200);
    }

    public function search_for_account($keyword, $Role)
    {

        $check = DB::table($Role) 
            ->where('phone_number', "LIKE", "%".$keyword."%")
            ->when($Role == "stores", function ($q) use($keyword) { $q->orWhere('store_name', "LIKE", "%".urldecode($keyword)."%"); })
            ->orWhere('first_name', "LIKE", "%".urldecode($keyword)."%")
            ->orWhere('last_name', "LIKE", "%".urldecode($keyword)."%")
            ->orWhere('Code', "LIKE", "%".$keyword."%")
            ->get();

        return response()->json([ "customers" => $check, "type" => $Role], 200);
    }

    public function updateaccount(Request $request, $type, $id)
    {   

        $Columns = ValidatedColumnsExcept($type, ['subdomain_name', 'store_type', 'theme_logo', 'Deliver_Fee', 'id', 'store_theme_id', 'specialties', 'email', 'password', 'remember_token', 'created_at', 'updated_at', 'firebase_token', 'stripe_id', 'card_brand', 'card_last_four','trial_ends_at']);

        $validator = Validator::make($request->all(), $Columns, CostumVal());
        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 400); }

        $requestData = $request->only(array_keys($Columns));

        $user = DB::table($type)->where('id', $id);
        $user->update($requestData);

        Record_action("updated ".$user->first()->first_name." ".$user->first()->last_name." informations");

        return response()->json(null, 200);

    }

    public function block($type, $id)
    {
        DB::table($type)->where('id', $id)->update(['confirmed' => 0]);

        return Result("seccuss", 201);
    }
    
    public function notifications()
    {
        $get = Notification::orderBy('id', 'DESC')->where('MemberPhoneNumber', '!=', '*')->paginate(10);

        $get->map(function ($get){

            $get['user_infos'] = GetAccountByPH($get->MemberPhoneNumber);

            return $get;
        });

        return response()->json($get, 200);
    }

    public function SendNorifyAll(Request $request)
    {   

        $validator = Validator::make($request->all(), [
            'MSG' => 'required',
            'title' => 'required' 
        ]);

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }

        $roles = config('constants.notfications');

        foreach ($roles as $key) {

            $fb_tokens = DB::table($key)->select('firebase_token')->get()->pluck('firebase_token')->chunk(1000)->toArray();
            
            foreach ($fb_tokens as $tokens){
                Notification($tokens, $request->get('title'), $request->get('MSG'), 0);
            }
            
        }

    }

    public function SendNorify(Request $request)
    { 

        $validator = Validator::make($request->all(), [ 
            'member_role' => 'required',
            'member_id' => 'numeric|required',
            'MSG' => 'required',
            'title' => 'required' 
        ]);

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }

        //Notify Specific Account
        $check = DB::table($request->get('member_role'))->where('id', $request->get('member_id'))->first();

        if($check){

            Notification([$check->firebase_token], $request->get('title'), $request->get('MSG'), $check->phone_number);

        }

        Record_action("send message to  ".$check->first_name." ".$check->last_name."<br> Title : ".$request->get('title')."<br> MSG : ".$request->get('MSG'));

        return response()->json(null, 200);
    }
 
}
