<?php

namespace App\Http\Controllers\api;

use App\Event;
use App\Events\AbuRabee3;

use App\Store;
use App\User;
use App\User_order;
use App\Deliver;
use App\prod_price;
use App\Prod_price_local;
use App\Orders_group;
use App\Orders_black_list;
use App\Withdraw_order;
use App\Discount_code;
use App\Member_stack;
use App\Discount_code_usage;
use App\Txt_service;
use App\Store_item_rate;
use App\Stores_theme;
use App\Stores_branche;
Use \Carbon\Carbon;
use App\Store_item;
use Illuminate\Support\Str;
use App\Exports\DownloadOrdersExcel;
use App\Exports\StoreSubmitCartInvoice;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BooksExport;
use Illuminate\Support\Facades\Hash;
use App\Exports\Member_Stack_orders;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\JWTAuth;
use Session;
use Illuminate\Support\Facades\Storage;
use Phpml\Classification\KNearestNeighbors;
use Illuminate\Validation\Rule;
use App\Imports\upload_orders_via_excel;


class Order extends Controller
{
     
    public function prices()
    {
        $prod_price = prod_price::all();
        $prod_price_local = Prod_price_local::all();
        return Result(compact('prod_price','prod_price_local'), 200);
    }

    public function SearchOrder(Request $request)
    {

        $get_prods = User_order::where('track_code', $request->input('Code'))->first();
        if (!$get_prods) {return response()->json(null, 404);}

        $find = User_order::find($get_prods->id);
        $find['Credit_Invoice'] = $find->Deliver_Fee + $find->recieved_price;

        $sender = Orders_group::where('orders_groups.id', $find->OrderGroupe_Id)
        ->when($find->account_type == 3, function ($q) {
            return $q->join('stores', 'stores.id', '=', 'orders_groups.member_id')->select('orders_groups.*', 'stores.store_name', 'stores.Code');
        })
        ->when($find->account_type == 2, function ($q) {
            return $q->join('users', 'users.id', '=', 'orders_groups.member_id')->select('orders_groups.*', 'users.Code');
        })
        ->first();
        
        return response()->json([ "customer" => $find, "sender" => $sender ], 200);

    }

    public function GetPrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required',
            'to' => 'required',
        ]);

        if($validator->fails()){ 
            return Result(Null, 400, $validator->errors()); 
        }

        return ResultNoSB(DeliverFee(ar_english_country($request->get('from')), ar_english_country($request->get('to'))), 200);
    }

    public function Check_Receiver_by_phone(Request $request)
    {
        $Req = $request->all();
        if (!PhoneFormat($request->get('phone_number'))) { return Result(Null, 400, 'Wrong Format'); }
        $Req['phone_number'] = ($request->has('phone_number')) ? "9647".explode("7", AR_TO_EN($request->get('phone_number')), 2)[1] : 0;

        $validator = Validator::make($Req, [ 'phone_number' => 'required|numeric|digits_between:13,13' ]);
        if($validator->fails()){ return response()->json($validator->errors()->toJson(), 400); }


        $check = Orders_black_list::where('receiver_phone_number', $Req['phone_number'])->first();

        if($check){ return Result(Null, 203, 'Receiver is in the Black List'); }

        return Result('Receiver is NOT in the black list', 200, Null);
    }

    public function new_order(Request $request)
    {   

        $requestData = $request->all();
        $requestData['insurance'] = filter_var($requestData['insurance'], FILTER_VALIDATE_BOOLEAN);
        
        $validator = Validator::make($requestData, [
            'product_name' => 'required|string|max:255',
            'receiver_full_name' => 'required|string|max:255',
            'size' => 'required|Numeric',
            'sender_phone_number' => 'Numeric',
            'payment_method' => ['required', Rule::in(['SENDER','RECEIVER'])],
            'reciever_phone_number' => 'required|Numeric|digits_between:10,13',
            'recieved_price' => 'required|Numeric',
            'location_from_state' => 'string|max:255',
            'location_from_region' => 'string|max:5000',
            'location_to_country' => 'string|max:255',
            'location_to_state' => 'required|string|max:255',
            'insurance' => 'required|BOOLEAN',
            'location_to_region' => 'required|string|max:255',
            'location_on_map_to' => 'string|max:255',
            'recieve_date' => 'string|max:255',
            'location_on_map_from' => 'string|max:255',
        ]);

        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
        
        if ($request->get('recieved_price') < 1000 and $request->get('recieved_price') != 0) {
            return ResultNoSB(Null, 403, "سعر البريد لا يجب ان يقل 1000 دينار عراقي اذا كان البريد هدية قم بادخال الرقم 0");
        }

        //SHIPPING TYPE
        $ShippingTypt = (user()->address_state == ar_english_country($request->get('location_to_state'))) ? "local" : "global";

        //FEES
        $FEES = DeliverFee(user()->address_state, ar_english_country($request->get('location_to_state')));

        $requestData['user_id'] = user()->id;
        $requestData['account_type'] = user_role_number();
        $requestData['track_code'] = Track_Code_Rand();
        $requestData['in_cart'] = 1;
        $requestData['App_Fee'] = $FEES->App_Fee;
        $requestData['Deliver_Fee'] = $FEES->Deliver_Fee;
        $requestData['shipping_type'] = $ShippingTypt;
        $requestData['location_to_state'] = ar_english_country($request->get('location_to_state'));
        $requestData['sender_full_name'] = (!$request->has('sender_full_name')) ? user()->first_name." ".user()->last_name : $requestData['sender_full_name'];
        $requestData['sender_phone_number'] =  (!$request->has('sender_phone_number')) ? user()->phone_number : $requestData['sender_phone_number'];
        $requestData['reciever_phone_number'] = "9647".explode("7", AR_TO_EN($request->get('reciever_phone_number')), 2)[1];
        $requestData['recieved_price'] = AR_TO_EN($request->get('recieved_price'));
        $requestData['recieve_date'] = Carbon::now()->format('Y-m-d');
        $requestData['status'] = "waiting";
        $requestData['location_from_country'] = "iraq";
        $requestData['location_to_country'] = "iraq";
        $requestData['location_from_state'] = (!$request->has('location_from_state')) ? user()->address_state : $request->get('location_from_state');
        $requestData['location_from_region'] = (!$request->has('location_from_region')) ? user()->address_region : $request->get('location_from_region');
        $requestData['location_on_map_from'] = (!$request->has('location_on_map_from')) ? '33.3152,44.3661' : $request->get('location_on_map_from');
        $requestData['location_on_map_to'] = (!$request->has('location_on_map_to')) ? '33.3152,44.3661' : $request->get('location_on_map_to');
        $requestData['handeled_by'] = current_receiver();
        $requestData['created_by_shared_link'] = 0;

        $Order = User_order::create($requestData);
        
        return Result(["response" => "seccuss", "Code" => $Order->track_code], 200);

    }
    
    public function submit_orders(Request $request)
    {   

        $validator = Validator::make($request->all(), [
            'created_by_shared_link' => 'required|boolean',
        ]);

        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
        
        $user_role = user_role();
        $user = user();

        $GetCartOrders = User_order::where('user_id', $user->id)
            ->where('account_type' ,user_role_number())
            ->where('in_cart', 1)
            ->where('created_by_shared_link', $request->get('created_by_shared_link'))
            ->get();
        
        if ($GetCartOrders->count() == 0) { return Result(Null, 400, "You Don't Have Orders in Cart"); }

        //Discount
        if($request->has('discount_code')){

            $find_code = Discount_code::where('Code', $request->get('discount_code'))->where('GifttsOrOrders', 'Orders')->first();

            if(!$find_code) { return Result(Null, 404, 'Invalid Discount Code!'); }

            if($find_code->Expire < Carbon::now()) { return Result(Null, 403, 'Discount Code Has Been Expired'); }

            $GetCartOrders->map(function ($order) use($find_code) {
                if(in_array($order->location_to_state, explode(',', $find_code->available_state)))
                {
                    $up_or = User_order::find($order->id);
                    $discount = ($up_or->Deliver_Fee * $find_code->discount_percent) / 100;
                    $up_or->Deliver_Fee = $up_or->Deliver_Fee - $discount;
                    $up_or->Order_Discount = $discount;
                    $up_or->save();
                }
            });
        }

        //Create New Cart
        $NewCart = new Orders_group;
        $NewCart->member_id = $user->id;
        $NewCart->account_type = ($user_role == "users") ? 2 : 3;
        $NewCart->save();
        
        
        //Set Orders In The New Cart
        $GetCartOrders->map(function ($order) use($NewCart) {
            $update = User_order::find($order->id);
            $update->OrderGroupe_Id = $NewCart->id;
            $update->in_cart = 0;
            // $update->deliver_track_code = @json_decode(BaridiInterface($order->toArray()))->data;
            $update->save();
        });
        
        
        Eventing(user()->first_name." ".user()->last_name." Posted ".$GetCartOrders->count()." orders ", "/ordersget/1", ['admins','delivers']);
        
        return Result("seccuss", 200);
    }

    public function edit_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'receiver_full_name' => 'required|string|max:255',
            'payment_method' => 'required|string|max:255',
            'reciever_phone_number' => 'required|Numeric|digits_between:10,13',
            'recieved_price' => 'required|Numeric',
            'location_from_state' => 'string|max:255',
            'location_from_region' => 'string|max:5000',
            'location_to_country' => 'string|max:255',
            'location_to_state' => 'required|string|max:255',
            'location_to_region' => 'required|string|max:255',
            'location_on_map_to' => 'string|max:255',
            'recieve_date' => 'string|max:255',
            'location_on_map_from' => 'string|max:255',
        ]);

        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
        if ($request->get('recieved_price') < 1000 and $request->get('recieved_price') != 0) {
            return ResultNoSB(Null, 401, "Product price should be more than 1000 IQD");
        }

        $Order = User_order::find($request->get('id'));
        $Order->product_name = $request->get('product_name');
        $Order->recieved_price = (int)$request->get('recieved_price');
        $Order->receiver_full_name = $request->get('receiver_full_name');
        $Order->reciever_phone_number = (int)$request->get('reciever_phone_number');
        $Order->location_to_state = $request->get('location_to_state');
        $Order->location_to_region = $request->get('location_to_region');
        $Order->payment_method = $request->get('payment_method');
        $Order->save();

        if (!$Order) { return Result(null, 400); }

        return Result("seccuss", 200);
    }

    public function DownloadSubmittedCartOrders()
    {
        return Excel::download(new StoreSubmitCartInvoice(user()->id,user_role()), Carbon::now().'.xlsx');
    }

    //======================================================================================= CART

    public function member_stack($Account_type,$Member_id)
    {   
        $get_stack = Member_stack::
        when($Account_type == "4", function ($q) use($Member_id) {return $q->where('member_stacks.deliver_id', $Member_id); })
        ->when($Account_type !== "4", function ($q) use($Member_id, $Account_type) {
            return $q->where('member_stacks.member_id', $Member_id)->where('member_stacks.account_type', $Account_type);
        })
        ->where('popped', 0)
        ->join('user_orders', 'user_orders.id', '=', 'member_stacks.order_id')
        ->get();

        return ResultNoSb($get_stack, 200, null);
    }

    public function Member_Pop_stack(Request $request)
    {   

        $validator = Validator::make($request->all(), [
            'member_id' => 'required|numeric',
            'account_type' => 'required|numeric',
        ]);
        
        if($validator->fails()){  return Result(Null, 400, $validator->errors()); }
        
        $ID = $request->get('member_id');
        $Role = $request->get('account_type');

        $get_stack = Member_stack::when($Role == "4", function ($q) use($ID) {return $q->where('member_stacks.deliver_id', $ID); })
        ->when($Role !== "4", function ($q) use($ID, $Role) {
            return $q->where('member_stacks.member_id', $ID)->where('member_stacks.account_type', $Role);
        })
        ->update(['popped' => 1]);
        
        return ResultNoSb('successful', 200, null);
    }

    public function Download_Member_Stack(Request $request)
    {   

        $validator = Validator::make($request->all(), [
            'member_id' => 'required|numeric',
            'account_type' => 'required|numeric',
        ]);

        if($validator->fails()){  return Result(Null, 400, $validator->errors()); }
        
        $ID = $request->get('member_id');
        $Role = $request->get('account_type');

        $get_stack = Member_stack::when($Role == "4", function ($q) use($ID) {return $q->where('member_stacks.deliver_id', $ID); })
            ->when($Role !== "4", function ($q) use($ID, $Role) {
                return $q->where('member_stacks.member_id', $ID)->where('member_stacks.account_type', $Role);
            })
            ->where('member_stacks.popped', 0)
            ->join('user_orders', 'user_orders.id', '=', 'member_stacks.order_id')
            ->get();

        return Excel::download(new DownloadOrdersExcel($get_stack), Carbon::now().'.xlsx');
        
    }

    public function Get_Cart()
    {
        $get = User_order::where('in_cart', 1)
            ->where('created_by_shared_link', false)
            ->where('user_id', user()->id)
            ->where('account_type' ,user_role_number())
            ->orderBy('id','DESC')
            ->select('id','receiver_full_name','track_code','recieve_date','recieved_price','product_name','size','location_to_state','location_to_region')
            ->get();

        return ResultNoSB($get, 200);
    } 
    
    public function My_Orders()
    {
        $get = User_order::where('in_cart', 0)
            ->where('user_id', user()->id)
            ->where('account_type' ,user_role_number())
            ->where('created_by_shared_link', false)
            ->orderBy('id','DESC')
            ->paginate(20);
            
            $get->map(function ($order) {
                
                $txt_status = Txt_service::where('order_id', $order->id)->latest()->first();
                $order['txt_status'] = ($txt_status) ? $txt_status->status : null;
                return $order;
            });

        return ResultNoSB($get, 200);
    } 

    public function DeleteOrderFromCart(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'id' => 'required|exists:user_orders,id', 
        ]);
        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

        $GetOrder = User_order::where('id', $request->get('id'))->where('user_id', user()->id)->where('account_type', user_role_number())->first();

        //if this is the last order in the cart then delete the cart as well
        $CountOrdersInTheSameCart = User_order::where('OrderGroupe_Id', $GetOrder->OrderGroupe_Id)->get()->count();
        if($CountOrdersInTheSameCart == 1){  Orders_group::where('id', $GetOrder->OrderGroupe_Id)->delete(); }

        //delete the order
        $GetOrder->delete();

        return ResultNoSB("Seccuss", 200);
    }

    public function Cart_Orders($Cart_id)
    {
        $get = User_order::where('OrderGroupe_Id', $Cart_id)
            ->where('user_id', user()->id)
            ->where('account_type', user_role_number())
            ->get();

        return ResultNoSB($get);
    }

    public function Cart_Orders_waiting($Cart_id)
    {
        $get = User_order::where('OrderGroupe_Id', $Cart_id)
            ->where('status', 'waiting')
            ->where('user_id', user()->id)
            ->where('account_type' ,user_role_number())
            ->get();

        return ResultNoSB($get);
    }

    //======================================================================================= ORDERS (STORES, USERS, DELIVERS)

    public function orders_by_id($id)
    {
        $get = User_order::where('user_orders.id', $id)
            ->join('orders_groups', 'orders_groups.id', '=', 'user_orders.OrderGroupe_Id')
            ->select('orders_groups.*','user_orders.*')
            ->get();
        return ResultNoSB($get);
    }
    
    public function wating_orders()
    {   

        $get = Orders_group::where('member_id', user()->id)
        ->where('account_type' , user_role_number())
        ->orderBy('id', 'DESC')
        ->select('id')
        ->get()
        ->map(function ($cart) {
            
            $get_waiting_orders = User_order::where('OrderGroupe_Id', $cart->id)->where('status', 'waiting')->get();
            $cart['Total_Orders'] = $get_waiting_orders->count();
            $cart['Total_Credit_Invoice'] = $get_waiting_orders->sum('Deliver_Fee') + $get_waiting_orders->sum('recieved_price');

            if($get_waiting_orders->count() > 0){
                return $cart;
            }
        })->filter()->toArray();
        
        return ResultNoSB(array_values($get));
    }
    
    public function waiting($Cart_id)
    {
        $get = User_order::where('OrderGroupe_Id', $Cart_id)
            ->where('user_id', user()->id)
            ->where('account_type' , user_role_number())
            ->where('status', 'waiting')
            ->select('id','receiver_full_name','track_code','recieve_date','recieved_price')
            ->get();

        return ResultNoSB($get);
    }
    
    public function pending_states_distinct()
    {
        $get = User_order::select('location_to_state')
            ->distinct('location_to_state')
            ->where('user_id',user()->id)
            ->where('account_type' , user_role_number())
            ->where('status', "pending")
            ->get()
            ->map(function ($order) {
                $order['Total_Orders'] = User_order::where('user_id',user()->id)
                    ->where('account_type' , user_role_number())
                    ->where('status', "pending")
                    ->where('location_to_state', $order->location_to_state)
                    ->get()->count(); 
                return $order;
            });
        
        return ResultNoSB($get);
        
    }
    
    public function pending_orders_by_state($state)
    {
        $get = User_order::where('location_to_state', urldecode($state))
            ->where('user_id',user()->id)
            ->where('account_type' , user_role_number())
            ->where('status', "pending")
            ->get();

        return ResultNoSB($get);
    }
    
    public function OrdersByStatus($status)
    {
        $get = User_order::orderBy('updated_at', 'DESC')
        ->where('user_id',user()->id)
        ->where('account_type', user_role_number())
        ->where('status', $status)
        ->select('id','receiver_full_name','track_code','recieved_price','location_to_region')
        ->get();

        return ResultNoSB($get);

    }

    // public function DownloadExportOrdersLabels(Request $request)
    // {
    //     $IDS =  explode(',',$request->get('ids'));
    //     $result = [];
    //     foreach ($IDS as $ID) {
    //         $order = User_order::where('track_code', $ID)->select('Deliver_Fee','track_code','receiver_full_name', 'product_name', 'reciever_phone_number', 'recieved_price', 'location_to_region', 'created_at')->first();
    //         $order['recieved_price'] = $order->recieved_price + $order->Deliver_Fee;
    //         $result[] = $order;
    //     }
    //     return response()->json($result);

    // }

    public function DownloadOrderExcel(Request $request)
    {
        return Excel::download(new DownloadOrderExcel($request->get('ids')), Carbon::now().'.xlsx');
    }

// =================================================================== COUSTOMER LINK ================================================

    public function new_item_in_warehouse(Request $request){
            
        $validator = Validator::make($request->all(), [
            'branch_ids' => 'required|string',
            'item' => 'required|string',
            'quantity' => 'Numeric',
            'price' => 'required|Numeric',
            'sizes' => 'required|string',
            'colors' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'description' => 'required|string',
        ]);

        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
        
        $sizes = [];
        $colors = [];
        $branches = [];

        $sizes_an = explode(',', $request->get('sizes'));
        $colors_an = explode(',', $request->get('colors'));
        $branches_an = explode(',', $request->get('branch_ids'));
        
        foreach($sizes_an as $size){
            $sizes[] = preg_replace("/\s+/", "", $size);
        }

        foreach($colors_an as $color){
            $colors[] = "#".preg_replace("/\s+/", "", $color);
        }
        
        foreach($branches_an as $branch){
            $branches[] = preg_replace("/\s+/", "", $branch);
        }

        $new = new Store_item;
        $new->branch_ids = json_encode($branches);
        $new->store_id = user()->id;
        $new->item = $request->get('item');
        $new->price = $request->get('price');
        $new->quantity = $request->get('quantity');
        $new->sizes = json_encode($sizes);
        $new->colors = json_encode($colors);
        $new->description = $request->get('description');
        $new->image = images($request, "image", "images/stores_items");
        $new->available = 1;
        $new->save();

        return Result('seccuss');
    }
    
    public function new_branch(Request $request){
            
        $validator = Validator::make($request->all(), [
            'branch' => 'required',
        ]);

        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
        
        $branches = explode(',', $request->get('branch'));

        foreach($branches as $branch){
            $new = new Stores_branche;
            $new->store_role = user_role();
            $new->store_id = user()->id;
            $new->branch = $branch;
            $new->active = 1;
            $new->save();
        }

        return Result('seccuss');
    }

    public function deactivate_branch(Request $request){
            
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|exists:stores_branches,id',
            'active' => 'required',
        ]);

        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
        
        $deactivate = Stores_branche::where('id', $request->get('branch_id'))->where('store_id', user()->id)->first();
        $deactivate->active = ($request->get('active') == "true") ? 1 : 0;
        $deactivate->save();

        return Result('seccuss');
    }

    public function get_branches($active)
    {
        $get = Stores_branche::select('id','branch', 'active')
        ->when($active !== 'All', function($q) use($active) { $q->where('active', $active); })
        ->where('store_id', user()->id)
        ->where('store_role', user_role())
        ->orderBy('id','DESC')->get();

        return ResultNoSB($get, 200);
    }

    public function get_branche_items($branch_id){

        $get_store_items = Store_item::where('store_id', user()->id)
        ->where('branch_ids', 'like', '%"'.$branch_id.'"%')
        ->get()
        ->map(function ($item) use($branch_id){
            $item['sizes'] = json_decode($item->sizes); 
            $item['colors'] = json_decode($item->colors); 
            $item['view_url'] = 'https://'.user()->subdomain_name.'.ihodhod.com/'.$item->id;
            $rate = Store_item_rate::where('item_id', $item->id)->get();
            $item['rate'] = ($rate->count() == 0) ? 1 : floor($rate->sum('rate') / $rate->count());
            return $item;
        });

        return ResultNoSB($get_store_items);

    }

    

    public function update_item_in_warehouse(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'Required|exists:store_items,id',
            'quantity' => 'Numeric',
            'price' => 'Numeric',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
        
        $update = Store_item::where('id', $request->get('id'))->where('store_id', user()->id)->first();

        if(!is_null($request->get('sizes'))){
            $sizes = [];
            $sizes_an = explode(',', $request->get('sizes'));
            foreach($sizes_an as $size){
                $sizes[] = preg_replace("/\s+/", "", $size);
            }
            $update->sizes = json_encode($sizes);
        }
        
        if(!is_null($request->get('colors'))){
            $colors = [];
            $colors_an = explode(',', $request->get('colors'));
            foreach($colors_an as $color){
                $colors[] = "#".preg_replace("/\s+/", "", $color);
            }
            $update->colors = json_encode($colors);
        }

        if(!is_null($request->file('image'))){
            $update->image = images($request, "image", "images/stores_items");
        }

        $update->item = (!is_null($request->get('item'))) ? $request->get('item') : $update->item;
        $update->price = (!is_null($request->get('price'))) ? $request->get('price') : $update->price;
        $update->quantity = (!is_null($request->get('quantity'))) ? $request->get('quantity') : $update->quantity;
        $update->description = (!is_null($request->get('description'))) ? $request->get('description') : $update->description;
        $update->available = (!is_null($request->get('available'))) ? $request->get('available') : $update->available;
        $update->save();

        return Result('seccuss');
    }

    public function Store_Delete_From_warehouse(Request $request){

        $validator = Validator::make($request->all(), [ 'id' => 'required|Numeric' ]);
        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

        $find = Store_item::where('id', $request->get('id'))->where('store_id', user()->id)->delete();
        if(!$find){ return Result(Null, 404, 'Id was not found !'); }
        return Result('Seccuss');
    }

    public function Get_Store_warehouse(){
        $get_store_items = Store_item::where('store_id', user()->id)
        ->get()
        ->map(function ($item) {
            $item['sizes'] = json_decode($item->sizes); 
            $item['colors'] = json_decode($item->colors); 
            $item['view_url'] = 'https://'.user()->subdomain_name.'.ihodhod.com/'.$item->id;
            $rate = Store_item_rate::where('item_id', $item->id)->get();
            $item['rate'] = ($rate->count() == 0) ? 1 : floor($rate->sum('rate') / $rate->count());

            return $item;
        });

        return ResultNoSB($get_store_items);
    }

    public function new_shared_link_orders(Request $request){
        return Result(['Code' => 'https://'.user()->subdomain_name.'.ihodhod.com']);
    }
    
    

    public function view_item($Order_id){

        $validator = Validator::make(
            [ 'id' => $Order_id ], 
            [ 'id' => 'exists:store_items,id' ]
        );

        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

        $item = Store_item ::find($Order_id);
        $item['store'] = Store::find($item->store_id)->store_name;
        $item['sizes'] = json_decode($item->sizes); 
        $item['colors'] = json_decode($item->colors); 
        $rate = Store_item_rate::where('item_id', $item->id)->get();
        $item['rate'] = ($rate->count() == 0) ? 1 : floor($rate->sum('rate') / $rate->count());

        return ResultNoSB($item);
    }

    public function Get_shared_orders_Cart(Request $request)
    {
        
        $get = User_order::where('user_id', user()->id)
        ->where('account_type', user_role_number())
        ->where('in_cart', 1)
        ->where('created_by_shared_link', 1)
        ->orderBy('id','DESC')
        ->select('id','receiver_full_name','track_code','recieved_price','location_to_region','quantity','size','color','product_name','product_image')
        ->get();
        
        return ResultNoSB($get, 200);
    }

    public function rate_item(Request $request){ 
        
        $REQ = $request->all();

        $validator = Validator::make($REQ, [
            'item_id' => ['required','exists:store_items,id'],
            'rate' => ['required', 'numeric', Rule::in([1,2,3,4,5])]
        ]);
        
        if($validator->fails()){ return Result($validator->errors(), 400); }
        
        $AddComment = new Store_item_rate;
        $AddComment->item_id = $request->get('item_id');
        $AddComment->rate = $request->get('rate');
        $AddComment->save();

        return Result("seccuss", 200);

    }

    public function submit_orders_By_Coustomer(Request $request){

        $requestData = $request->all();
        $requestData['insurance'] = filter_var($requestData['insurance'], FILTER_VALIDATE_BOOLEAN);

        $validator = Validator::make($requestData, [
            'store_id' => 'required|Numeric|exists:stores,id',
            'product_name' => 'required|string|max:255',
            'receiver_full_name' => 'required|string|max:255',
            'payment_method' => 'required|string|max:255',
            'reciever_phone_number' => 'required|Numeric',
            'recieved_price' => 'required|Numeric',
            'size' => 'required',
            'color' => 'required',
            'quantity' => 'required',
            'location_to_state' => 'required|string|max:255', 
            'location_to_region' => 'required|string|max:255',
            'location_from_state' => 'string|max:255',
            'location_from_region' => 'string|max:5000',
            'location_to_country' => 'string|max:255',
            'location_on_map_to' => 'string|max:255',
            'location_on_map_from' => 'string|max:255',
        ]);

        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
        
        //Get Store 
        $user = Store::find($request->get('store_id'));
        
        //Account_Type
        $account_type = 3;

        //SHIPPING TYPE
        $ShippingTypt = ($user->address_state == ar_english_country($request->get('location_to_state'))) ? "local" : "global";

        //FEES
        $FEES = DeliverFee($user->address_state, ar_english_country($request->get('location_to_state')), 'stores');
    
        $requestData['user_id'] = $user->id;
        $requestData['account_type'] = $account_type;
        $requestData['track_code'] = Track_Code_Rand();
        $requestData['in_cart'] = 1;
        $requestData['sender_full_name'] = $user->first_name." ".$user->last_name;
        $requestData['sender_phone_number'] = $user->phone_number;
        $requestData['location_from_state'] = $user->address_state;
        $requestData['location_from_region'] = $user->address_region;
        $requestData['Deliver_Fee'] = $FEES->Deliver_Fee;
        $requestData['App_Fee'] = $FEES->App_Fee;
        $requestData['shipping_type'] = $ShippingTypt;
        $requestData['location_from_country'] = "iraq";
        $requestData['location_to_country'] = "iraq";
        $requestData['location_to_state'] = ar_english_country($request->get('location_to_state')); 
        $requestData['recieved_price'] = AR_TO_EN($request->get('recieved_price'));
        $requestData['recieve_date'] = Carbon::now()->format('Y-m-d');
        $requestData['status'] = "waiting";
        $requestData['location_on_map_from'] = (!$request->has('location_on_map_from')) ? '33.3152,44.3661' : $request->get('location_on_map_from');
        $requestData['location_on_map_to'] = (!$request->has('location_on_map_to')) ? '33.3152,44.3661' : $request->get('location_on_map_to');
        $requestData['size'] = $request->get('size');
        $requestData['color'] = $request->get('color');
        $requestData['created_by_shared_link'] = 1;
        $requestData['handeled_by'] = current_receiver();

        $Order = User_order::create($requestData);

        return Result(["response"=>"seccuss","Code"=>$Order->track_code], 200);


    }
    
    public function Store_Statistic(Request $request){

        Carbon::setWeekStartsAt(Carbon::SATURDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        $today_sales = User_order::where('account_type', 3)->where('user_id', user()->id)->whereDate('created_at', Carbon::today())->where('status', 'delivered')->get()->count();
        $this_week_sales = User_order::where('account_type', 3)->where('user_id', user()->id)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('status', 'delivered')->get()->count();
        $this_month_sales = User_order::where('account_type', 3)->where('user_id', user()->id)->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where('status', 'delivered')->get()->count();
        $this_year_sales = User_order::where('account_type', 3)->where('user_id', user()->id)->whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])->where('status', 'delivered')->get()->count();
        $delivered_orders = User_order::where('account_type', 3)->where('user_id', user()->id)->where('status', 'delivered')->get()->count();
        $returned_orders = User_order::where('account_type', 3)->where('user_id', user()->id)->where('status', 'LIKE','%'.'returned'.'%')->get()->count();
        $discounts_amount = User_order::where('account_type', 3)->where('user_id', user()->id)->where('status', 'delivered')->get()->sum('Order_Discount');
        $deliver_fees_amount = User_order::where('account_type', 3)->where('user_id', user()->id)->where('status', 'delivered')->get()->sum('Deliver_Fee');
            

        $orders = User_order::where('created_by_shared_link', 0)
        ->where('account_type', 3)
        ->where('user_id', user()->id)
        ->where('status','LIKE', '%'.'delivered'.'%')
        ->get()->count();

        $shared_link_orders = User_order::where('created_by_shared_link', 1)
        ->where('account_type', 3)
        ->where('user_id', user()->id)
        ->where('status','LIKE', '%'.'delivered'.'%')
        ->get()->count();

        $Top_5__items_sales = User_order::select('product_id','product_name','product_image')
        ->where('user_id', user()->id)
        ->where('account_type', 3)
        ->where('status', 'delivered')->get()
        ->unique('product_id')
        ->map(function ($item) {
            $item['product_image'] = url().'/images/stores_items/'.basename($item->product_image);
            $item['sales'] = User_order::where('product_id', $item->product_id)->get()->count();
            $rate = Store_item_rate::where('item_id', $item->id)->get();
            $item['rate'] = ($rate->count() == 0) ? 1 : floor($rate->sum('rate') / $rate->count());
            return $item;
        });
        $Top_5__items_sales = array_values($Top_5__items_sales->toArray());
        array_multisort(array_column($Top_5__items_sales, 'sales'), SORT_DESC, $Top_5__items_sales);

        $Top_5_states_sales = User_order::select('product_id','location_to_state')
        ->where('user_id', user()->id)
        ->where('account_type', 3)
        ->where('status', 'delivered')->get()
        ->unique('location_to_state')
        ->map(function ($item) {
            $item['sales'] = User_order::where('location_to_state', $item->location_to_state)->get()->count();
            return $item;
        });
        $Top_5_states_sales = array_values($Top_5_states_sales->toArray());
        array_multisort(array_column($Top_5_states_sales, 'sales'), SORT_DESC, $Top_5_states_sales);

        $Top_5_clients = User_order::select('product_id','receiver_full_name','reciever_phone_number')
        ->where('user_id', user()->id)
        ->where('account_type', 3)
        ->where('status', 'delivered')->get()
        ->unique('reciever_phone_number')
        ->map(function ($item) {
            $item['sales'] = User_order::where('reciever_phone_number', $item->reciever_phone_number)->get()->count();
            return $item;
        });
        $Top_5_clients = array_values($Top_5_clients->toArray());
        array_multisort(array_column($Top_5_clients, 'sales'), SORT_DESC, $Top_5_clients);

        $total_items_rate = Store_item::where('store_id', user()->id)->get()
        ->map(function ($item) {
            $rate = Store_item_rate::where('item_id', $item->id)->get();
            $item['rate'] = ($rate->count() == 0) ? 1 : floor($rate->sum('rate') / $rate->count());
            return $item;
        });

        $total_items_rate = ($total_items_rate->count() == 0) ? 1 : floor($total_items_rate->sum('rate') / $total_items_rate->count());

        return Result([
            "store_name" => user()->store_name,
            "Code" => user()->Code,
            "today_sales" => $today_sales,
            "this_week_sales" => $this_week_sales,
            "this_month_sales" => $this_month_sales,
            "this_year_sales" => $this_year_sales,
            "Top_5_items_sales" => array_slice($Top_5__items_sales, 0, 5, true),
            "Top_5_states_sales" => array_slice($Top_5_states_sales, 0, 5, true),
            "Top_5_clients" => array_slice($Top_5_clients, 0, 5, true),
            "total_items_rate" => $total_items_rate,
            "delivered_orders" => $delivered_orders,
            "returned_orders" => $returned_orders,
            "orders" => $orders,
            "shared_link_orders" => $shared_link_orders,
            "discounts_amount" => $discounts_amount,
            "deliver_fees_amount" => $deliver_fees_amount,
            "store_items" => Store_item::where('store_id', user()->id)->get()->count()
        ], 200);
        
    }

    public function upload_orders_via_excel(Request $request){
        
        $this->validate($request, [
            'file'  => 'required|mimes:xls,xlsx'
        ]);
    
        $path = $request->file('file')->getRealPath();

        $res = Excel::import(new upload_orders_via_excel(), $request->file('file'));
    
        return Result($res);

        
    }

// =================================================================== Discount ================================================

    public function get_discount_codes(){
        
        //  $get = Discount_code::find(8);
        // return json_decode($get->customized_clients)[0][1]->id;
        $get = Discount_code::where('Expire', '>', Carbon::now())
        ->get()
        ->map(function ($code) {
            
            $get_code_usages = Discount_code_usage::where('user_id', user()->id)->where('account_type', user_role_number())->where('code', $code->Code)->get()->count();
            
            //Allowed usage excude
            if($get_code_usages < $code->allowd_usages || $code->allowd_usages == 0){
                // return "ads";
                //customized codes match
                if(!is_null($code->customized_clients)){
                    foreach(json_decode($code->customized_clients) as $c_code){
                        if($c_code[1]->id == user()->id && $c_code[1]->type == user_role_number()){
                            return $code;
                        }
                    }
                }
                
                //state match
                elseif(in_array(user()->address_state, explode(',', $code->available_state))){
                    return $code;
                }
            }
        })
        ->filter()
        ->toArray();

        return ResultNoSB(array_values($get), 200);

    }

    public function txt_service(Request $request){
        
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:user_orders,id',
            'status' => 'required',
            'Deliver_Fee' => 'required|Numeric',
        ]);

        if($validator->fails()){ Result(Null, 400, $validator->errors()); }
        
        //set txt service record
        $NewCart = new Txt_service;
        $NewCart->order_id = $request->get('order_id');
        $NewCart->status = $request->get('status');
        $NewCart->Deliver_Fee = $request->get('Deliver_Fee');
        $NewCart->save();

        //reverse order details
        $order = User_order::find($request->get('order_id'));
        $order->update([
            "sender_full_name" => $order->receiver_full_name,
            "sender_phone_number" => $order->reciever_phone_number,
            "location_from_state" => $order->location_to_state,
            "location_from_region" => $order->location_to_region,
            "location_on_map_from" => $order->location_on_map_to,
            "receiver_full_name" => $order->sender_full_name,
            "reciever_phone_number" => $order->sender_phone_number,
            "location_to_state" => $order->location_from_state,
            "location_to_region" => $order->location_from_region,
            "location_on_map_to" => $order->location_on_map_from,
            "status" => "waiting",
        ]);
        
        return Result("seccuss", 200);
    }

    public function search_orders($keyword){

        $keywordF = urldecode($keyword);

            $get = User_order::where('user_id', user()->id)
            ->where('account_type', user_role_number())
            ->where('created_by_shared_link', false)
            ->where(function($query) use($keywordF) {
                $query->where('track_code', $keywordF)
                ->orWhere('product_name', "LIKE", "%".$keywordF."%")
                ->orWhere('receiver_full_name', "LIKE", "%".$keywordF."%")
                ->orWhere('reciever_phone_number', "LIKE", "%".ltrim($keywordF, '0')."%")
                ->orWhere('location_to_region', "LIKE", "%".$keywordF."%")
                ->orWhere('sender_full_name', "LIKE", "%".$keywordF."%")
                ->orWhere('sender_phone_number', "LIKE", "%".ltrim($keywordF, '0')."%")
                ->orWhere('location_from_region', "LIKE", "%".$keywordF."%")
                ->orWhere('status', $keywordF)
                ->orWhere('case_details', $keywordF)
                ->orWhere('recieve_date', $keywordF);
            })
            ->get()
            ->map(function ($order) {
                $txt_status = Txt_service::where('order_id', $order->id)->latest()->first();
                $order['txt_status'] = ($txt_status) ? $txt_status->status : null;
                return $order;
            });
            
            return ResultNoSB($get, 200);
    }
    
}
