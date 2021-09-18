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

        public function view_item($item_id){

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


    }
