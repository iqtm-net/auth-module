<?php

    namespace App\Http\Controllers\api\Dashboard\Admin;

    use App\Exports\BooksExport;
    use App\Exports\DownloadOrdersExcel;
    use App\Offers_order;
    use App\User_order;
    use App\prod_price;
    use App\Prod_price_local;
    use App\Order_status_changes_history;
    use App\Forward_order;
    use App\Orders_group;
    use App\Deliver;
    use App\Store;
    use App\Offers_branch;
    use App\Top_offer;
    use App\Stores_specialty;
    use App\Stores_theme;
    use App\Discounted_offer;
    use App\Offers_orders_cart;
    use App\Partial_refund;
    use App\Discount_code;
    use Illuminate\Validation\Rule; 
    use App\Txt_service;
    use App\Taxi;
    use App\Offer;
    use App\Gd;
    use App\User;
    use App\States_option;
    use App\Orders_black_list;
    use App\Member_stack;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Maatwebsite\Excel\Facades\Excel;
    use Illuminate\Database\Eloquent\Builder;
    use Tymon\JWTAuth\JWTAuth;
    Use \Carbon\Carbon;
    use Illuminate\Support\Arr;
    use Illuminate\Support\Facades\DB;
    use App\Imports\Change_orders_status;
    use Picqer;
    use File;

    class Orders extends Controller
    {
        
        public function Account_Cart($Cart_id)
        {

            $get = User_order::where('OrderGroupe_Id', $Cart_id)
            
            ->join('orders_groups', 'orders_groups.id', '=', 'user_orders.OrderGroupe_Id')
            ->select('orders_groups.sender_full_name','user_orders.*')
            ->get();

            return ResultNoSB($get, 200, Null);

        }

        public function delete_orders(Request $request)
        {   
            User_order::findOrFail($request->get('order_id'))->delete();
            return response()->json('success', 200);
        }
        
        public function get_order_in_cart_by_id($type, $order_id)
        {
            $get = User_order::where('user_orders.id', $order_id)->where('account_type', $type)->where('in_cart', 0)
                ->when($type == 3, function ($q) {
                    return $q->join('stores', 'stores.id', '=', 'user_orders.user_id');
                })
                ->when($type == 2, function ($q) {
                    return $q->join('users', 'users.id', '=', 'user_orders.user_id');
                })
                ->first();

            if (!$get) { return response()->json(null, 203); }

            return response()->json([ "customer" => $get ], 200);
        }

        public function get_orders($role,$id,$status,$dateFrom,$dateto,$FromState,$ToState)
        {
            $get = User_order::where('in_cart', 0)
            ->orderBy('sender_full_name')->orderBy('id','DESC')
            ->when($role !== "All", function ($q) use($role,$id) {  return $q->where('user_id', $id)->where('account_type',$role); })
            ->when(user_role() == "companies", function ($q) use($role,$id) {  return $q->where('handeled_by', user()->id); })
            ->when(user()->id == 24, function ($q) use($role,$id) {  return $q->where('created_at', '>=', "2021-09-01T9:14:14.865282Z"); })
            ->when($dateFrom !== 'All' && $dateto !== 'All', function ($q) use($dateFrom, $dateto) {return $q->whereBetween('created_at', [$dateFrom, $dateto]); })
            ->when($ToState !== 'All', function ($q) use($ToState) { return $q->Where('location_to_state', 'LIKE','%'.$ToState.'%'); })
            ->when($FromState !== 'All', function ($q) use($FromState) { return $q->Where('location_from_state', 'LIKE','%'.$FromState.'%'); }) 
            ->when($status !== "Delay" && $status !== "All", function ($q) use($status) { return $q->where('status', $status);})
            ->when($status == "Delay" && $status !== "All", function ($q) use($status) { 
                return $q
                ->whereIn('status', ["waiting", "pending"])
                ->where(function($query) { 
                    $query
                    ->where('created_at', '<=', Carbon::now()->subHours(40))
                    ->orWhere('created_at', '<=', Carbon::now()->subHours(60));
                });
            })
            ->paginate(50);

            return response()->json($get);
        }
        
        public function check_order_id($order_id){

            $validator = Validator::make(["order_id" => $order_id], [
                'order_id' => ["exists:user_orders,track_code"],
            ]);
            
            if($validator->fails()){ return Result($validator->errors(), 400); }

            $generator = new Picqer\Barcode\BarcodeGeneratorPNG();

            $order = User_order::where('track_code', $order_id)->first();
            $order['barcode'] = 'data:image/png;base64,' . base64_encode($generator->getBarcode($order->track_code, $generator::TYPE_CODE_128));
            
            return response()->json($order);
        
        }

        public function ChangeSelectedOrdersStatus(Request $request){

            $statuses = config('constants.sys_statuses');
            $statuses[] = "ReceivedByHodHod";

            $validator = Validator::make([
                "status" => $request->get('status'),
                "order_ids" => json_decode($request->get('order_ids')),
            ], 
            [
                "status" => [Rule::In($statuses)],
                "order_ids" => ["required", "array"],
            ]
            );
            
            if($validator->fails()){ return Result($validator->errors(), 400); }
            
            foreach(json_decode($request->get('order_ids')) as $order_id){
                
                $update = User_order::where('id', $order_id->id)->first();
                
                $GetPoster = ($update->account_type == 2) ? User::find($update->user_id): Store::find($update->user_id);

                if ($request->get('status') == "pending") {

                    Notification(
                        [$GetPoster->firebase_token],
                        " تم استلام البريد "."(".$update->product_name.")",
                        " سوف يباشر هدهد بعملة ايصال بريدك  "."(".$update->product_name.")"." الى المستلم "."(".$update->receiver_full_name.")",
                        $GetPoster->phone_number
                    );
                }

                if ($request->get('status') == "delivered") {

                    if($update->status == 'delivered') { return Result("already delivered !", 400); }

                    //deal with user store balance
                    $GetPoster->balance = $GetPoster->balance + $update->recieved_price;
                    $GetPoster->save();

                    //put in member stack
                    $New_M_Stack = new Member_stack;
                    $New_M_Stack->deliver_id = $update->deliver_id;
                    $New_M_Stack->member_id = $GetPoster->id;
                    $New_M_Stack->account_type = $update->account_type;
                    $New_M_Stack->order_id = $update->id;
                    $New_M_Stack->popped = 0;
                    $New_M_Stack->save();

                    Notification(
                        [$GetPoster->firebase_token], 
                        " تم تحديث حالة الطلب للبريد ".$update->track_code,
                        " تم ايصال بريد "."(".$update->product_name.")"." يمكنك تقييم عملية التوصيل من خلال قائمة البريد المكتمل. ",
                        $GetPoster->phone_number
                    );

                }

                if (in_array($request->get('status'), ['ReturnedToClient','ReturnedToDeliver'])) {

                    //Set Receiver Phone Number In The Black List
                    $set_to_black_list = new Orders_black_list;
                    $set_to_black_list->order_id = $update->id;
                    $set_to_black_list->receiver_phone_number = $update->reciever_phone_number;
                    $set_to_black_list->save();

                    Notification(
                        [$GetPoster->firebase_token], 
                        'حالة الطلب',
                        " لم نستطع ايصال بريد "."(".$update->receiver_full_name.")"." بسبب "."(".$update->case_details.")",
                        $GetPoster->phone_number
                    );
                }
                
                if ($request->get('status') == "ReceivedByHodHod") {
                    $update->handeled_by = 0;
                }

                (in_array($request->get('status'), config('constants.sys_statuses'))) ? 
                    $update->status = $request->get('status') : false;

                $update->case_details = ($request->has('case_details')) ? $request->get('case_details') : Null;
                $update->save();
                
                //Set in history
                $Order_status_changes_history = new Order_status_changes_history;
                $Order_status_changes_history->order_id = $update->id;
                $Order_status_changes_history->deliver_id = $update->deliver_id;
                $Order_status_changes_history->user_id = $update->user_id;
                $Order_status_changes_history->account_type = $update->account_type;
                $Order_status_changes_history->status = $request->get('status');
                $Order_status_changes_history->save();

            }
            
            Record_action("changed orders status of ".$request->get('order_ids')." to ".$request->get('status'));

            return Result();
        }

        public function ChangeOrdersStatusByExcel(Request $request){
            
            $this->validate($request, [
                'file'  => 'required|mimes:xls,xlsx'
            ]);
            
            $path1 = $request->file('file');
            //->store('temp'); 
            $path=storage_path('app').'/'.$path1;
        
            Excel::import(new Change_orders_status(), $path1);
        
            return Result();

        }

        public function Statistic($role, $id, $dateFrom, $dateto, $FromState, $ToState)
        {   
            
            $AllOrders = User_order::all()->count();
            $Waiting = 0;
            $DelayGlobal = 0;
            $DelayLocal = 0;
            $Pending = 0;
            $ReturnedToDeliver = 0;
            $ReturnedToClient = 0;
            $DeliveredRepayed = 0; 
            $DeliveredUnRepayed = 0; 
            
            $Orders = User_order::
            when($role !== 'All', function ($q) use($role,$id) { return $q->where('user_id', $id)->where('account_type',$role); })
            
            ->when(user()->id == 24, function ($q) use($role,$id) {  return $q->where('created_at', '>=', "2021-09-01T9:14:14.865282Z"); })
            
            ->when($dateFrom !== 'All' && $dateto !== 'All', function ($q) use($dateFrom, $dateto) {return $q->whereBetween('created_at', [$dateFrom, $dateto]); })
            ->when($ToState !== 'All', function ($q) use($ToState) { return $q->Where('location_to_state', 'LIKE','%'.$ToState.'%'); })
            ->when($FromState !== 'All', function ($q) use($FromState) { return $q->Where('location_from_state', 'LIKE','%'.$FromState.'%'); }) 
            ->where('in_cart', 0)
            ->get()
            ->map(function ($Order) use (&$Waiting, &$DelayGlobal, &$DelayLocal, &$Pending, &$ReturnedToDeliver, &$ReturnedToClient, &$DeliveredRepayed, &$DeliveredUnRepayed){

                $CheckRepayment = Member_stack::where('order_id', $Order->id)->where('popped', 1)->first();

                if($Order->status == "waiting")  { $Waiting++; }
                if($Order->status == "waiting" && $Order->location_from_state == $Order->location_to_state  && $Order->created_at <= Carbon::now()->subHours(24)){ $DelayLocal++; }
                if($Order->status == "waiting" && $Order->location_from_state !== $Order->location_to_state && $Order->created_at <= Carbon::now()->subHours(72)){ $DelayGlobal++; }
                if($Order->status == "pending") { $Pending++; }
                if($Order->status == "ReturnedToDeliver") { $ReturnedToDeliver++; }
                if($Order->status == "ReturnedToClient") { $ReturnedToClient++; }
                if($Order->status == "delivered" && $CheckRepayment) { $DeliveredRepayed++; }
                if($Order->status == "delivered" && !$CheckRepayment) { $DeliveredUnRepayed++; }

                return $Order;
            }); 

            $TotalOrders = $Orders->count();

            return response()->json([ 
                [ 'name' => 'Waiting', 'value' => $Waiting ],
                [ 'name' => 'DelayGlobal', 'value' => $DelayGlobal ],
                [ 'name' => 'DelayLocal', 'value' => $DelayLocal ],
                [ 'name' => 'Pending', 'value' => $Pending ],
                [ 'name' => 'Returned to deliver', 'value' => $ReturnedToDeliver ],
                [ 'name' => 'Returned to client', 'value' => $ReturnedToClient ],
                [ 'name' => 'Delivered Withdrawn', 'value' => $DeliveredRepayed ],
                [ 'name' => 'Delivered Unwithdrawn', 'value' => $DeliveredUnRepayed ]
            ], 200);

            // "Waiting" => [$Waiting, percentage($Waiting,$TotalOrders)],
            // "DelayGlobal" => [$DelayGlobal, percentage($DelayGlobal,$TotalOrders)],
            // "DelayLocal" => [$DelayLocal, percentage($DelayLocal,$TotalOrders)],
            // "Pending" => [$Pending, percentage($Pending,$TotalOrders)],
            // "ReturnedToDeliver" => [$ReturnedToDeliver, percentage($ReturnedToDeliver,$TotalOrders)],
            // "ReturnedToClient" => [$ReturnedToClient, percentage($ReturnedToClient,$TotalOrders)],
            // "DeliveredRepayed" => [$DeliveredRepayed, percentage($DeliveredRepayed,$TotalOrders)],
            // "DeliveredUnRepayed" => [$DeliveredUnRepayed, percentage($DeliveredUnRepayed,$TotalOrders)],
            
        }

        public function DownloadStat($id, $type, $status, $DateFrom, $DateTo)
        {
            return Excel::download(new BooksExport($id, $type, $status, $DateFrom, $DateTo), Carbon::now().'.xlsx');
        }

        public function Remove_Order()
        {
            $id = $request->only('id')['id'];
            $posts = User_order::find($id);
            $posts->delete();
            return response()->json(null, 200);
        }
        
        public function AddPartialRefund(Request $request){

            $validator = Validator::make($request->all(), [
                'order_id' => ["exists:user_orders,id"],
                'track_code' => ["exists:user_orders,track_code"],
                'post_name' => ["required"],
                'post_price' => ["required", "numeric"],
            ]);
            
            if($validator->fails()){ return Result($validator->errors(), 400); }
            
            //Edit Post Price
            $edit_post = User_order::find($request->get('order_id'));
            $edit_post->recieved_price = $edit_post->recieved_price - $request->get('post_price');
            $edit_post->save();
            
            //Record Partial Refund
            $new = new Partial_refund;
            $new->track_code = $request->get('track_code');
            $new->post_name = $request->get('post_name');
            $new->post_price = $request->get('post_price');
            $new->save();

            Record_action("Added partial refund to order ".$request->get('order_id'). " with name of : ".$request->get('post_name')." and price of : ".$request->get('post_price'));

            return Result();
            
        }

        public function search_for_order($role, $id, $keyword)
        {
            $keywordF = urldecode($keyword);

            $get_his_orders = User_order::when($role !== 'All', function ($q) use($role,$id) { return $q->where('user_id', $id)->where('account_type', $role); })
            ->where(function($query) use($keywordF) {
                $query->where('track_code', $keywordF)
                ->orWhere('product_name', "LIKE", "%".$keywordF."%")
                ->orWhere('receiver_full_name', "LIKE", "%".$keywordF."%")
                ->orWhere('reciever_phone_number', "LIKE", "%".ltrim($keywordF, '0')."%")
                ->orWhere('location_to_state', "LIKE", "%".$keywordF."%")
                ->orWhere('sender_full_name', "LIKE", "%".$keywordF."%")
                ->orWhere('sender_phone_number', "LIKE", "%".ltrim($keywordF, '0')."%")
                ->orWhere('location_from_state', "LIKE", "%".$keywordF."%")
                ->orWhere('case_details', $keywordF)
                ->orWhere('recieve_date', $keywordF);
            })
            ->paginate(50);

            return response()->json($get_his_orders, 200);
        }

        public function order_status_history($order_id)
        {
            $get = Order_status_changes_history::where('order_id', $order_id)->orderBy('id','DESC')->get();
            return response()->json($get, 200);
        }
        
        public function DownloadExcel(Request $request)
        {   
            return Excel::download(new DownloadOrdersExcel(json_decode($request->get('orders'))), Carbon::now().'.xlsx');
        }

        public function PrintLabels(Request $request)
        {   
            $generator = new Picqer\Barcode\BarcodeGeneratorPNG();

            $orders = [];

            foreach (json_decode($request->get('orders')) as $order_id){
                $order = User_order::find($order_id->id);
                $order['barcode'] = 'data:image/png;base64,' . base64_encode($generator->getBarcode($order->track_code, $generator::TYPE_CODE_128));
                $orders[] = $order;
            }
            
            return ResultNoSB($orders, 200, Null);
        }

        public function FT(Request $request)
        {   
            $up = DB::table(user_role())->where('id', user()->id);
            
            $up->update([
                "label_counter_start" => (int) $up->first()->label_counter_start + 1
            ]);

            return user()->label_counter_start;
        }

        public function get_order_by_id($id)
        {
            $find = User_order::where('id', $id)->orWhere('track_code', $id)->first();

            if (!$find) { return response()->json(null, 404); }

            $find['Credit_Invoice'] = $find->Deliver_Fee + $find->recieved_price;

            $find['current_location_lat'] = Location_Filter($find->current_location_on_map, 0);
            $find['current_location_lng'] = Location_Filter($find->current_location_on_map, 1);

            $find['current_location_from_lat'] = Location_Filter($find->location_on_map_from, 0);
            $find['current_location_from_lng'] = Location_Filter($find->location_on_map_from, 1);

            $find['current_location_to_lat'] = Location_Filter($find->location_on_map_to, 0);
            $find['current_location_to_lng'] = Location_Filter($find->location_on_map_to, 1);

            $find['store_name'] = ($find->account_type == 3) ? Store::find($find->user_id)->store_name : Null;

            $find['partial_refunded'] = Partial_refund::where('track_code', $find->track_code)->get();

            $find['txt_service'] = Txt_service::where('order_id', $find->id)->get()->sum('Deliver_Fee');

            return response()->json([ "customer" => $find ]);
        }

        public function update_order(Request $request, $id) 
        {
            $REQ = $request->only('case_details', 'size','insurance', 'current_location_on_map','distance','sender_full_name','location_from_state','location_from_country','sender_phone_number','location_from_region','location_on_map_from','location_on_map_to','location_to_country','location_to_region','location_to_state','payment_method','product_name','rate','receiver_full_name','recieve_date','recieved_price','reciever_phone_number','sender_phone_number','shipping_type','status','track_code');
            $validator = Validator::make($REQ, [
                'receiver_full_name' => 'required|string|max:50',
                'shipping_type' => 'required|string|max:50',
                'payment_method' => 'required|string|max:50',
                'reciever_phone_number' => 'required|Numeric|digits_between:0,13',
                'recieved_price' => 'required|Numeric',
                'status' => 'required|string|max:50',
                'location_to_country' => 'required|string|max:20',
                'location_to_state' => 'required|string|max:20',
                'location_to_region' => 'required|string|max:120',
                'recieve_date' => 'required|string',
                'product_name' => 'required|string|max:50',
                'track_code' => 'required|string|max:20'
            ], CostumVal());


            if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }

            $posts = User_order::where('id', $id);

            $posts->update($REQ);

            Record_action("updated order informations of ".$posts->first()->track_code." from <br>".json_encode($posts->first())." to <br>".json_encode($request->all()));

            return response()->json($posts, 200);
        }
 
        //==================================================================== PRICE ==========================================================
        public function Add_price(Request $request) 
        {
            //return response()->json(null, 200);
            $validator = Validator::make($request->all(), [
                'type' => 'required',
                'distance_range_from' => 'required|numeric|digits_between:0,20',
                'distance_range_to' => 'required|numeric|digits_between:0,20',
                'Deliver_Fee' => 'required|numeric|digits_between:0,20',
                'App_Fee' => 'required|numeric|digits_between:0,20',
            ]);

            if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }

            $posts = prod_price::create($request->all());
            return response()->json(null, 200);
        }

        public function prices()  
        {

            $get = prod_price::latest()->paginate(50);
            $get2 = Prod_price_local::latest()->paginate(50);
            return response()->json(["by_type" => $get, "by_local" => $get2], 200);
        }

        public function getprice($id)  
        {
            $get = prod_price::where('id', $id)->first();

            if (!$get) { return response()->json(null, 203); }

            return response()->json([ "customer" => $get ], 200);
        }

        public function deleteprice(Request $request)
        {
            $id = $request->only('id')['id'];
            $posts = prod_price::find($id)->delete();
            return response()->json(null, 200);
        }

        public function update_price(Request $request, $id)
        {
            $Req = $request->except('created_at','updated_at');
            $validator = Validator::make($Req, [
                'distance_range_from' => 'required|numeric|digits_between:0,20',
                'distance_range_to' => 'required|numeric|digits_between:0,20',
                'Deliver_Fee' => 'required|numeric|digits_between:0,20',
                'App_Fee' => 'required|numeric|digits_between:0,20'
            ]);

            if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }

            $posts = DB::table('prod_prices')->where('id', $id)->update($Req);
            return response()->json($posts, 200);
        }

        public function Add_price_local(Request $request)  
        {
            //return response()->json(null, 200);
            $validator = Validator::make($request->all(), [
                'distance_range_from' => 'required|numeric|digits_between:0,20',
                'distance_range_to' => 'required|numeric|digits_between:0,20',
                'Deliver_Fee' => 'required|numeric|digits_between:0,20',
                'App_Fee' => 'required|numeric|digits_between:0,20',
            ]);


            if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }

            $check = Prod_price_local::where('distance_range_from',$request->only('distance_range_from'))
            ->where('distance_range_to',$request->only('distance_range_to'))->first();

            if ($check) { return response()->json(null, 205); }

            $posts = Prod_price_local::create($request->all());

            return response()->json(null, 200);
        }

        public function prices_local() 
        {
            $get = Prod_price_local::latest()->paginate(50);
            return response()->json($get);
        }

        public function getprice_local($id)  
        {
            $get = Prod_price_local::where('id', $id)->first();

            if (!$get) { return response()->json(null, 203); }

            return response()->json([ "customer" => $get ], 200);
        }

        public function deleteprice_local(Request $request)
        {
            $id = $request->only('id')['id'];
            $posts = Prod_price_local::find($id)->delete();
            return response()->json(null, 200);
        }

        public function update_price_local(Request $request, $id)
        {
            $Req = $request->except('created_at','updated_at');
            $validator = Validator::make($Req, [
                'distance_range_from' => 'required|numeric|digits_between:0,20',
                'distance_range_to' => 'required|numeric|digits_between:0,20',
                'Deliver_Fee' => 'required|numeric|digits_between:0,20',
                'App_Fee' => 'required|numeric|digits_between:0,20'
            ]);

            if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }

            $posts = DB::table('prod_price_locals')->where('id', $id)->update($Req);
            return response()->json($posts, 200);
        }

        //======================================================== OPTIONS =======================================================
        public function options()
        {
            $get = States_option::all();
            return ResultNoSB($get, 200);
        }

        public function check_tate(Request $request)
        {

            //return ar_english_country($request->get('state'));

            $validator = Validator::make($request->all(), [ 'state' => 'required|string' ]);
            if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }

            $get = States_option::where('state', ar_english_country($request->get('state')))->first();
            return Result($get, 200);
        }

        public function update_options(Request $request)
        {
            $up = States_option::find($request->get('id'));
            $up->available = $request->get('bool');
            $up->save();

            return ResultNoSB("seccus", 200);
        }

        //======================================================== THEMES =======================================================
        public function get_stores_themes()
        {
            $get = Stores_theme::orderBy('id','DESC')
            ->where('active', 1)
            ->get();

            return ResultNoSB($get, 200);
        }

        public function add_store_theme(Request $request)
        {   
            ini_set('memory_limit', '-1');

            $validator = Validator::make($request->all(), [
                'theme' => 'required',
                'image' => 'required',
            ],CostumVal());
    
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

            $add = new Stores_theme;
            $add->theme = $request->get('theme');
            $add->image = base64images($request, "image", "images/stores_themes");
            $add->active = 1;
            $add->save();

            return response()->json('success', 200);
        }

        public function deactivate_theme(Request $request)
        {   

            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:stores_themes,id',
            ],CostumVal());
    
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

            $add = Stores_theme::find($request->get('id'));
            $add->active = 0;
            $add->save();

            return response()->json('success', 200);
        }

        public function virtual_store_setup(Request $request)
        {   
            //validation
            $validator = Validator::make($request->all(), [
                'store_theme_id' => 'required|exists:stores_themes,id',
                'theme_logo' => ['required', 'image', 'mimes:jpg,png,jpeg'],
                'store_type' => ['required', Rule::In(config('constants.store_types'))],
                'specialties' => 'required',
                'subdomain_name' => ['required', 'unique:stores,subdomain_name', 'regex:/^[a-zA-Z0-9_.-]+$/u'],
            ]);
    
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
            
            //specialties
            $specialties = [];
        
            $specialties_an = explode(',', $request->get('specialties'));
            
            foreach($specialties_an as $specialty){
                $specialties[] = preg_replace("/\s+/", "", $specialty);
            }

            //Submit
            $update = DB::table(user_role())->where('id', user()->id)->update([
                "store_type" => $request->get('store_type'),
                "store_theme_id" => $request->get('store_theme_id'),
                "theme_logo" => images($request, "theme_logo", "images/stores_themes_logos"),
                "subdomain_name" => $request->get('subdomain_name'),
                "specialties" => json_encode($specialties),
            ]);
    
            return Result('seccuss');
        }

        public function virtual_store_edit(Request $request)
        {   
            $REQ = $request->all();

            //validation
            $validator = Validator::make($request->all(), [
                'store_theme_id' => 'exists:stores_themes,id',
                'theme_logo' => ['image', 'mimes:jpg,png,jpeg'],
                'store_type' => [Rule::In(config('constants.store_types'))],
                'subdomain_name' => ['unique:stores,subdomain_name', 'regex:/^[a-zA-Z0-9_.-]+$/u'],
            ]);
    
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
            
            //specialties
            if($request->has('specialties')){

                $specialties = [];
        
                $specialties_an = explode(',', $request->get('specialties'));
                
                foreach($specialties_an as $specialty){
                    $specialties[] = preg_replace("/\s+/", "", $specialty);
                }

                $REQ['specialties'] = json_encode($specialties);

            }
            
            ($request->has('theme_logo')) ? $REQ['theme_logo'] = images($request, "theme_logo", "images/stores_themes_logos") : false;
            
            DB::table(user_role())->where('id', user()->id)->update($REQ);
    
            return Result('seccuss');
        }

        public function virtual_store_check(Request $request)
        {   
            $validator = Validator::make($request->all(), [
                'subdomain_name' => ['unique:stores,subdomain_name', 'regex:/^[a-zA-Z0-9_.-]+$/u'],
            ]);
            
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
            
            return (DB::table(user_role())->where('subdomain_name', $request->get('subdomain_name'))->first()) 
                ? Result('subdomain name exists' ,400) 
                : Result('seccuss');

        }

        //======================================================== SPECIALTIES =======================================================
        public function specialties()
        {
            $get = Stores_specialty::orderBy('id','DESC')->get();
            return ResultNoSB($get, 200);
        }
        
        public function add_specialties(Request $request)
        {   
            $add = new Stores_specialty;
            $add->specialty = $request->get('specialty');
            $add->save();

            return response()->json('success', 200);
        }
        
        public function delete_specialties(Request $request)
        {
            Stores_specialty::find($request->get('id'))->delete();
            return response()->json('success', 200);
        }

        // public function select_specialties(Request $request)
        // {
        //     $validator = Validator::make($request->all(), [
        //         'specialties' => 'required',
        //     ]);
    
        //     if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
            
        //     $specialties = [];
    
        //     $specialties_an = explode(',', $request->get('specialties'));
            
        //     foreach($specialties_an as $specialty){
        //         $specialties[] = preg_replace("/\s+/", "", $specialty);
        //     }
            
        //     $update = Store::find(user()->id);
        //     $update->specialties = json_encode($specialties);;
        //     $update->save();
    
        //     return Result('seccuss');
        // }
        
        // public function select_store_type(Request $request)
        // {
        //     $validator = Validator::make($request->all(), [
        //         'store_type' => ['required', Rule::In(config('constants.store_types'))],
        //     ]);
    
        //     if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
            
        //     $update = Store::find(user()->id);
        //     $update->store_type = $request->get('store_type');
        //     $update->save();
    
        //     return Result('seccuss');
        // }
    }
