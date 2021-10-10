<?php

    namespace App\Http\Controllers\api;

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
    use App\Customer_cart;
    use App\Customer_favourite;
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

    class Stores extends Controller
    {   
        //App
        public function check_shared_link_code($subdomain_name){

            $get_store = Store::where('subdomain_name', $subdomain_name)->select('id', 'store_name', 'phone_number', 'Code', 'theme_logo', 'store_theme_id')->first();
            if (!$get_store) { return Result(Null, 404, 'No Shared Link Found With This Code.');  }
            
            $get_store->theme = Stores_theme::find($get_store->store_theme_id)->theme;
    
            $store_branches = Stores_branche::where('store_id', $get_store->id)->where('active', 1)->select('id', 'branch')->get();
    
            $suggested_items = Store_item::where('store_id', $get_store->id)
            ->get()
            ->take(4)
            ->map(function ($item) {
                $store = Store::find($item->store_id);
                $item['store'] = $store->store_name;
                $item['sizes'] = json_decode($item->sizes); 
                $item['colors'] = json_decode($item->colors); 
                return $item;
            });
    
            // $suggestte_items = Store_items::where('id', $request->get('id'))->orWhere('track_code', $request->get('track_code'));
            $suggested_stores = Store::take(5)->get();
    
            return ResultNoSB([
                'store_infos' => $get_store,
                'suggested_items' => $suggested_items,
                'store_branches' => $store_branches,
                'store_footer_branches' => $store_branches->take(4),
                'more_stores' => $suggested_stores,
            ]);
    
        }

        public function get_active_stores(){

            $get_stores = Store::get()
            ->map(function ($item){

                $find_items = Store_item::where('store_id', $item['id'])->first();
                    if (!$find_items) { return null; }

                return $item;
            })
            ->toArray();
    
            return ResultNoSB(array_values(array_filter($get_stores)));
    
        }
        
        public function get_active_store_branches($subdomain_name){

            $get_store = Store::where('subdomain_name', $subdomain_name)
            ->select('id', 'store_name', 'phone_number', 'Code', 'theme_logo', 'store_theme_id')
            ->first();
                if (!$get_store) { return Result(Null, 404, 'No Shared Link Found With This Code.');  }
                
            //check if store active (has at least 1 item)
            $find_items = Store_item::where('store_id', $get_store->id)->first();
                if (!$find_items) { return Result(Null, 404, 'No Shared Link Found With This Code.');  }
    
            //get store branches
            $store_branches = Stores_branche::where('store_id', $get_store->id)->where('active', 1)->select('id', 'branch')->get();
    
            
    
            return ResultNoSB($store_branches);
    
        }

        public function get_branche_items_pag($subdomain_name, $branch_id){

            
            $validator = Validator::make([
                "subdomain_name" => $subdomain_name,
            ], [
                'subdomain_name' => 'exists:stores,subdomain_name',
            ]);
    
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

            $get_store = Store::where('subdomain_name', $subdomain_name)->select('id', 'store_name', 'phone_number', 'Code', 'theme_logo')->first();

            $get_store_items = Store_item::where('store_id', $get_store->id)
            ->when($branch_id !== "All", function($q) use($branch_id){ 
                $q->whereJsonContains('branch_ids', [$branch_id]);
            })
            ->get()
            ->map(function ($item) use($subdomain_name, $branch_id){
                $item['sizes'] = json_decode($item->sizes); 
                $item['colors'] = json_decode($item->colors); 
                $item['view_url'] = 'https://'.$subdomain_name.'.ihodhod.com/'.$item->id;
                $rate = Store_item_rate::where('item_id', $item->id)->get();
                $item['rate'] = ($rate->count() == 0) ? 1 : floor($rate->sum('rate') / $rate->count());

                return [$item->id,$item];
            })
            ->toArray();
    
            return ResultNoSB(array_values(array_filter($get_store_items)));
        }

        public function view_item_app($item_id){

            $validator = Validator::make(
                [ 'id' => $item_id ], 
                [ 'id' => 'exists:store_items,id' ]
            );
    
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
    
            $item = Store_item ::find($item_id);
            $item['store'] = Store::find($item->store_id)->store_name;
            $item['sizes'] = json_decode($item->sizes); 
            $item['colors'] = json_decode($item->colors);
            $rate = Store_item_rate::where('item_id', $item->id)->get();
            $item['rate'] = ($rate->count() == 0) ? 1 : floor($rate->sum('rate') / $rate->count());
    
            return ResultNoSB($item);
        }

        //Web
        public function main($subdomain_name){

            $get_store = Store::where('subdomain_name', $subdomain_name)->select('id', 'store_name', 'phone_number', 'theme_logo')->first();
            if (!$get_store) { return Result(Null, 404, 'Store Not Found.');  }
                
            $store_branches = Stores_branche::where('store_id', $get_store->id)->where('active', 1)->select('id', 'branch')->get();
    
            $suggested_items = Store_item::where('store_id', $get_store->id)->where('available', 1)
            ->inRandomOrder()
            ->take(10)
            ->get();
    
            return ResultNoSB([
                'store_infos' => $get_store,
                'store_branches' => $store_branches,
                'suggested_items' => $suggested_items,
            ]);
    
        }
        
        public function branches($subdomain_name){

            $get_store = Store::where('subdomain_name', $subdomain_name)->first();
            if (!$get_store) { return Result(Null, 404, 'Store Not Found.');  }
    
            $store_branches = Stores_branche::where('store_id', $get_store->id)->where('active', 1)->select('id', 'branch')->get();
    
            return ResultNoSB($store_branches);
    
        }

        public function items($subdomain_name, $branch_id){

            $get_store = Store::where('subdomain_name', $subdomain_name)->first();
            if (!$get_store) { return Result(Null, 404, 'Store Not Found.');  }

            $get_store_items = Store_item::where('store_id', $get_store->id)
            ->whereJsonContains('branch_ids', [$branch_id])
            ->paginate(50);
            
            return ResultNoSB($get_store_items);
        }

        public function view_item($subdomain_name, $item_id){

            $get_store = Store::where('subdomain_name', $subdomain_name)->first();
            if (!$get_store) { return Result(Null, 404, 'Store Not Found.');  }

            $validator = Validator::make( [ 'id' => $item_id ], [ 'id' => 'exists:store_items,id' ]);
            if($validator->fails()){ return Result(Null, 404, 'Item Not Found.'); }
    
            $item = Store_item::find($item_id);

            return ResultNoSB($item);
        }
        
        public function add_to_cart(Request $request){

            $validator = Validator::make($request->all(), [
                'item_id' => 'required|Numeric|exists:store_items,id',
                'quantity' => 'required|Numeric',
            ]);
    
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

            $New = new Customer_cart;
            $New->ip = $request->ip();
            $New->item_id = $request->get('item_id');
            $New->size = $request->get('size');
            $New->color = $request->get('color');
            $New->quantity = $request->get('quantity');
            $New->in_cart = 1;
            $New->save();
    
            return Result();
    
        }

        public function cart(Request $request){

            $get = Customer_cart::where('ip', $request->ip())
            ->where('in_cart', 1)
            ->get()
            ->map( function($item){
                $item->item;
                return $item;
            });
    
            return Result([
                "total_price" => $get->sum('item.price'),
                "items" => $get,
            ]);
    
        }
        
        public function remove_from_cart(Request $request){
            $validator = Validator::make($request->all(), [
                'id' => 'required|Numeric|exists:customer_carts,id',
            ]);
    
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

            Customer_cart::where('id', $request->get('id'))->where('ip', $request->ip())->delete();
    
            return Result();
        }

        public function submit_cart(Request $request){
            
            $requestData = $request->all();

            $validator = Validator::make($requestData, [
                'full_name' => 'required|string|max:255',
                'phone_number' => 'required|Numeric|digits_between:10,13',
                'state' => 'required|string|max:255',
                // 'payment_method' => ['required', Rule::in(['SENDER','RECEIVER'])],
                'region' => 'required',
            ]);
    
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

            $get = Customer_cart::where('ip', $request->ip())->where('in_cart', 1)->get()
                ->map( function($item) use($request){
                    $item->item;
                    $item->store = Store::find($item->item->store_id);

                    $FEES = DeliverFee($item->store->address_state, $request->get('state'), 'stores', $item->store);

                    $requestData['user_id'] = $item->store->id;
                    $requestData['account_type'] = 3;
                    $requestData['sender_full_name'] = $item->store->first_name." ".$item->store->last_name;
                    $requestData['sender_phone_number'] =  $item->store->phone_number;
                    $requestData['location_from_country'] = "iraq";
                    $requestData['location_from_state'] = $item->store->address_state;
                    $requestData['location_from_region'] = $item->store->address_region;
                    $requestData['location_on_map_from'] = '33.3152,44.3661';
                    
                    $requestData['receiver_full_name'] = $request->get('full_name');
                    $requestData['reciever_phone_number'] = "9647".explode("7", AR_TO_EN($request->get('phone_number')), 2)[1];
                    $requestData['location_to_country'] = "iraq";
                    $requestData['location_to_state'] = ar_english_country($request->get('state'));
                    $requestData['location_to_region'] = ar_english_country($request->get('region'));
                    $requestData['location_on_map_to'] = '33.3152,44.3661';

                    $requestData['size'] = $item->size;
                    $requestData['color'] = $item->color;
                    $requestData['quantity'] = $item->quantity;
                    $requestData['recieved_price'] = $item->item->price;
                    $requestData['product_name'] = $item->item->item;

                    $requestData['track_code'] = Track_Code_Rand();
                    $requestData['in_cart'] = 1;
                    $requestData['App_Fee'] = $FEES->App_Fee;
                    $requestData['Deliver_Fee'] = $FEES->Deliver_Fee;
                    $requestData['shipping_type'] = $FEES->type;
                    $requestData['recieve_date'] = Carbon::now()->format('Y-m-d');
                    $requestData['status'] = "waiting";
                    $requestData['created_by_shared_link'] = 1;
                    $requestData['payment_method'] = 'SENDER';

                    User_order::create($requestData);
            });

            Customer_cart::where('ip', $request->ip())->where('in_cart', 1)->update([ "in_cart" => 0 ]);
    
            return Result();
    
        }

        public function add_to_favourites(Request $request){

            $validator = Validator::make($request->all(), [
                'item_id' => 'required|Numeric|exists:store_items,id',
            ]);
    
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

            $New = new Customer_favourite;
            $New->ip = $request->ip();
            $New->item_id = $request->get('item_id');
            $New->in_favourite = 1;
            $New->save();
    
            return Result();
    
        }

        public function favourites(Request $request){

            $get = Customer_favourite::where('ip', $request->ip())
            ->where('in_favourite', 1)
            ->get();
    
            return Result([
                "total_price" => $get->sum('item.price'),
                "items" => $get->pluck('item'),
            ]);
    
        }

        public function remove_from_favourites(Request $request){
            $validator = Validator::make($request->all(), [
                'id' => 'required|Numeric|exists:customer_favourites,id',
            ]);
    
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

            Customer_favourite::where('id', $request->get('id'))->where('ip', $request->ip())->delete();
    
            return Result();
        }
        
        public function search($subdomain_name, $keyword){

            $get_store = Store::where('subdomain_name', $subdomain_name)->first();
            if (!$get_store) { return Result(Null, 404, 'No Shared Link Found With This Code.');  }
            
            $keywordF = urldecode($keyword);

            $get = Store_item::where('store_id', $get_store->id)
            ->where('item', "LIKE", "%".$keywordF."%")
            ->get();
            
            return ResultNoSB($get, 200);
        }

    }
