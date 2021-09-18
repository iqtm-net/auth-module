<?php

namespace App\Http\Controllers\api;

use App\Offers_branch;
use App\Offers_order;
use App\Offers_orders_cart;
use App\Offer;
use App\User;
use App\Store;
use App\Offer_rate;
use App\Top_offer;
use App\Discounted_offer;
use App\Specail_gifts_branch;
use App\Slide_gifts_branch;
use App\Slide_gifts_item;
use App\Specail_gifts_branch_item;
use App\Offers_favourite;
use App\Gifts_event;
use App\Discount_code;
Use \Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\JWTAuth;
use Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File; 
use App\Exports\DownloadGiftsOrdersExcel;
use App\Exports\DownloadGiftsExcel;
use Maatwebsite\Excel\Facades\Excel;

class Offers extends Controller
{
     

    public function Main_Random()
    {
        $Offer = Offer::inRandomOrder()->paginate(50);
        return ResultNoSB($get);
    }
    
    // ================================== DOWNLOAD EXCEL
    public function DownloadGiftsOrdersExcel($branch_id)
    {   
        return Excel::download(new DownloadGiftsOrdersExcel($branch_id), 'GIFTS_'.Carbon::now().'.xlsx');
    }

    public function DownloadGiftsExcel($branch_id)
    {   
        return Excel::download(new DownloadGiftsExcel($branch_id), 'GIFTS_ORDERS_'.Carbon::now().'.xlsx');
    }

    //=================================== Slide show
    
    public function slide_branch_gifts()
    {
        $get = Slide_gifts_branch::all();
        return ResultNoSB($get);
    }

    public function slide_branch_items($branch_id)
    {
        $get = Slide_gifts_item::where('branch_id', $branch_id)->get();
        return ResultNoSB($get);
    }
    
    public function AddSlideOffer(Request $request){
    
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'branch_id' => 'required|numeric|exists:slide_gifts_branches,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,pdf',
        ]);

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 400); }
        
        $update = new Slide_gifts_item;
        $update->title = $request->get('title');
        $update->branch_id = $request->get('branch_id');
        $update->image = images($request, "image", "images/Offers");
        $update->save();

        return ResultNoSB("Seccuss");

    }

    public function DeleteSlideOffer(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:slide_gifts_items,id',
        ]);

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 400); }
        
        $find = Slide_gifts_item::find($request->get('id'));
        $delete_image = File::delete(base_path('public').'/images/Offers/'.$find->image);
        $delete_data = $find->delete();
        return ResultNoSB("Seccuss");

    }
    
    //==================================== EVENTS
    public function GetEvents()
    {
        $get = Gifts_event::
            where('account_id', user()->id)
            ->where('account_type',user_role())
            ->get();
        return ResultNoSB($get);
    }

    public function AddEvent(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            //'offer_id' => 'required|numeric|exists:offers,id',
            'event_date' => 'required',
            'event_note' => 'required',
            'title' => 'required',
            'gender' => ['required', Rule::in(['male','female'])],
        ]);

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 400); }
        
        $add = new Gifts_event;
        $add->account_id = user()->id;
        $add->account_type = user_role();
        $add->gender = $request->get('gender');
        $add->date = $request->get('event_date');
        $add->event_note = $request->get('event_note');
        $add->title = $request->get('title');
        $add->save();
        
        return Result("seccuss");
    }
    
    public function DeleteEvent(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:gifts_events,id',
        ]);

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 400); }
        
        Gifts_event::find($request->get('id'))->delete();
        
        return Result("seccuss");
    }

    //============================================== OFFERS
    public function GetBranches()
    {
        $get = Offers_branch::all();
        return ResultNoSB($get);
    }

    //============================================== SPECAIL
    public function Specail_branch()
    {
        $branch = Specail_gifts_branch::first();
        return ResultNoSB($branch);
    }
    
    public function Members_Specail_branch()
    {
        $branch = Specail_gifts_branch::first();
        $branch['background1'] = str_replace('#', '', $branch['background1']);
        $branch['background2'] = str_replace('#', '', $branch['background2']);
        return Result($branch);
    }
    
    public function specail_branch_items()
    {
        $get = Specail_gifts_branch_item::join('offers','offers.id','=','specail_gifts_branch_items.offer_id')->get();
        return ResultNoSB($get);
    }

    public function AddOfferToSpecail(Request $request){
        $validator = Validator::make($request->all(), [
            'offer_id' => 'required|numeric|exists:offers,id',
        ]);

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 400); }
        
        $find = Specail_gifts_branch_item::where('offer_id', $request->get('offer_id'))->first();
        if($find) { $find->delete(); }
        else{
            $add = new Specail_gifts_branch_item;
            $add->offer_id = $request->get('offer_id');
            $add->save();
        }
        
        return ResultNoSB("Seccuss");

    }

    public function EditSpecialBranch(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'background1' => 'required',
            'background2' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg,pdf',
        ]);

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 400); }
        
        $update = Specail_gifts_branch::first();
        $update->title = $request->get('title');
        $update->background1 = $request->get('background1');
        $update->background2 = $request->get('background2');
        $update->image = $request->has('image') ? images($request, "image", "images/Offers") : $update->image;
        $update->save();

        return ResultNoSB("Seccuss");

    }
    
    //===================================
    public function GetOffers($BranchId)
    {
        $get = Offer::where('brache_id', $BranchId)->orderBy('id', 'DESC')->get();

        $results = [];
        foreach($get as $item){
            $find_in_specail = Specail_gifts_branch_item::where('offer_id', $item->id)->first();
            $item['specail'] = ($find_in_specail) ? 1 : 0;
            $results[] = $item;
        }

        return ResultNoSB($results);
    }

    public function Orders($status)
    {   
        $type = user_role();

        $get = Offers_order::where('offers_orders.status', $status)
        ->join('offers', 'offers.id', '=', 'offers_orders.offer_id')
        ->select('offers_orders.*','offers.product_name')
        ->OrderBy('offers_orders.id','DESC')
        ->paginate(50);
        
        $results = [];

        foreach($get as $order){
            if($order['account_type'] == 2){
                $sender = User::find($order['account_id']);
            }
            elseif($order['account_type'] == 3){
                $sender = Store::find($order['account_id']);
            }
            $order['first_name'] = $sender->first_name;
            $order['last_name'] = $sender->last_name;
            $order['phone_number'] = $sender->phone_number;
            $results[] = $order;
        }

        return response()->json($results, 200);
    }

    public function My_Orders($status)
    {   
        $type = user_role();
        //return user()->id;
        $get = Offers_order::where('offers_orders.status', $status)
        ->where('offers_orders.account_id', user()->id)
        ->when($type == 'users', function($q) { $q->where('offers_orders.account_type', 2); })
        ->when($type == 'stores', function($q) { $q->where('offers_orders.account_type', 3); })
        ->join('offers', 'offers.id', '=', 'offers_orders.offer_id')
        ->OrderBy('offers_orders.id','DESC')
        ->get();
        
        return ResultNoSB($get);
    }

    public function AddBranche(Request $request)
    {
        $Offers_branch = new Offers_branch;
        $Offers_branch->branche = $request->get('branche');
        $Offers_branch->image = images($request, "image", "images/Offers");
        $Offers_branch->save();

        return ResultNoSB("seccus", 200);
    }

    public function AddOffer(Request $request)
    {
        $REQ = $request->all();

        $validator = Validator::make($REQ, [
            'brache_id' => 'required',
            'product_name' => 'required',
            'describtion' => 'required',
            'price' => 'required',
            'gender' => 'required',
            'color' => 'required',
            'mark' => 'required',
            'size' => 'required',
            'made_in' => 'required',
            'materail' => 'required',
            'free_shipping' => 'required',
            'discount' => 'numeric',
            'media' => 'image|mimes:jpeg,png,jpg,gif,svg,pdf',
            'media2' => 'image|mimes:jpeg,png,jpg,gif,svg,pdf',
            'media3' => 'image|mimes:jpeg,png,jpg,gif,svg,pdf',
        ]);

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 400); }

        $REQ['free_shipping'] = ($request->get('free_shipping')) ? 1 : 0;
        $REQ['media'] = images($request, "media", "images/Offers");
        $REQ['media2'] = images($request, "media2", "images/Offers");
        $REQ['media3'] = images($request, "media3", "images/Offers");
        $REQ['in_stock'] = 1;

        $add = Offer::create($REQ);
        
        if($request->get('TopOrDiscounted') == 'Top')
            {
                $Top = new Top_offer;
                $Top->brache_id = $request->get('brache_id');
                $Top->offer_id = $add->id;
                $Top->save();
            }
        if($request->get('TopOrDiscounted') == 'Discounted')
            {
                $Top = new Discounted_offer;
                $Top->brache_id = $request->get('brache_id');
                $Top->offer_id = $add->id;
                $Top->discount = $request->get('Discount');
                $Top->save();
            }

        return response()->json('success', 200);
    }

    public function SuggestedOffers($BranchId)
    {
        $get = Offer::where('brache_id', $BranchId)->inRandomOrder()->paginate(5);
        $result = [];
        foreach ($get as $GET){
            $GET['media'] = '/images/Offers/'.$GET->media;
            $GET['media2'] = '/images/Offers/'.$GET->media2;
            $GET['media3'] = '/images/Offers/'.$GET->media3;
            $result[] = $GET;
        }

        return ResultNoSB($result);
    }

    public function SuggestedOffersRandom($take)
    {
        $get = Offer::inRandomOrder()
        ->when($take !== "All", function ($q) use ($take) { return $q->take($take); })
        ->get();

        return ResultNoSB($get);
    }

    public function ViewOffer($id)
    {
        $get = Offer::find($id);
        return Result($get);
    }

    public function NewOfferOrder(Request $request)
    {   
        //Discount
        
        $REQ = $request->all();
        $validator = Validator::make($REQ, [
            'offer_id' => 'required|numeric|exists:offers,id',
            'hide_sender_infos' => 'required',
            'receiver_full_name' => 'required|string',
            'receiver_phone_number' => 'required|numeric|digits_between:0,20',
            'receiver_address' => 'required|string',
            'receiver_location' => 'required|string',
            'receiver_gender' => ['required', Rule::in(['male','female'])],
            'card_text' => 'required|string',
            'notes' => 'required|string',
            'total_price' => 'required|numeric|digits_between:0,20',
            'discount_code' => 'string',
        ]);

        if($validator->fails()){ return response()->json($validator->errors(), 400); }

        //Check Code Expiration Date
        if($request->has('discount_code')){    
            $find_code = Discount_code::where('Code', $request->get('discount_code'))->where('GifttsOrOrders', 'Gifts')->first();
            if(!$find_code) { return Result(Null, 404, 'Wrong Code!'); }
            if($find_code->Expire < Carbon::now()) { return Result(Null, 403, 'Code Has Been Expired'); }
        }
        
        $REQ['account_id'] = user()->id;
        $REQ['account_type'] = (user_role() == "users") ? 2 : 3;
        $REQ['order_number'] = Track_Code_Rand();
        $REQ['status'] = 'waiting';
        $REQ['total_price'] = ($request->has('discount_code')) ? ($REQ['total_price'] * $find_code->discount_percent) / 100 : $REQ['total_price'];

        Offers_order::create($REQ);

        return Result("seccuss", 200);
    }

    public function OfferRating(Request $request){

        $user = user();
        $account_type = (user_role() == 'users') ? 2 : 3;
        $validator = Validator::make($request->all(), [
            'offer_id' => 'required|numeric',
            'rate' => 'required|numeric|max:5',
        ]);

        if($validator->fails()){ return response()->json($validator->errors(), 400); }

        if (!Offer::find($request->get('offer_id'))) { return Result(Null, 400, 'Offer Not Found'); }

        if (Offer_rate::where('offer_id', $request->get('offer_id'))->where('user_id', $user->id)->where('account_type', $account_type)->first())
        { return Result(Null, 400, 'Already Rated !'); }

        $New = new Offer_rate;
        $New->offer_id = $request->get('offer_id');
        $New->user_id = $user->id;
        $New->account_type = $account_type;
        $New->rate = $request->get('offer_id');
        $New->save();

        return Result("seccuss", 200);
    }

    //=========================== FILTERED 

    public function MostRated(){

        $TopOffers = Offer::select('id')->distinct('id')->get();
        foreach($TopOffers as $Offer){
            $GetOffer = Offer::where('offers.id', $Offer->id)
                ->join('offers_branches', 'offers_branches.id', '=', 'offers.brache_id')
                ->select('offers.*','offers_branches.branche as Branche')->first();
            $GetOffer['Total_Rates'] = Offer_rate::where('offer_id',$Offer->id)->get()->count();
            $RsTopOffers[] = $GetOffer;
        }
        array_multisort(array_column($RsTopOffers, 'Total_Rates'), SORT_DESC, $RsTopOffers);
        array_slice($RsTopOffers, 0, 5);

        return ResultNoSB($RsTopOffers);

    }

    public function MostPurchased()
    {
        $TopOffers = Offer::select('id')->distinct('id')->get();
        foreach($TopOffers as $Offer){
            $GetOffer = Offer::where('offers.id',$Offer->id)
                ->join('offers_branches', 'offers_branches.id', '=', 'offers.brache_id')
                ->select('offers.*','offers_branches.branche as Branche')->first();

            if($GetOffer){
                $GetOffer['Total_Orders'] = Offers_order::where('offer_id',$Offer->id)->get()->count();
                $RsTopOffers[] = $GetOffer;
            }
            
        }
        array_multisort(array_column($RsTopOffers, 'Total_Orders'), SORT_DESC, $RsTopOffers);
        array_slice($RsTopOffers, 0, 4);

        return ResultNoSB($RsTopOffers);

    } 

    public function NewOffers($take){
        $NewOffers = Offer::whereDate('offers.created_at', '>', Carbon::now()->subHours(48))
        ->join('offers_branches', 'offers_branches.id', '=', 'offers.brache_id')
        ->select('offers.*','offers_branches.branche as Branche')
        ->when($take !== "All", function ($q) use ($take) { return $q->take($take); })
        ->get();
        
        return ResultNoSB($NewOffers);

    }
    
    public function TopOffers($take){

        $NewOffers = Top_offer::
        join('offers_branches', 'offers_branches.id', '=', 'top_offers.brache_id')
        ->join('offers', 'offers.id', '=', 'top_offers.offer_id')
        ->select('offers.*','offers_branches.branche as Branche')
        ->when($take !== "All", function ($q) use ($take) { return $q->take($take); })
        ->get();
        
        return ResultNoSB($NewOffers);

    }
    
    public function discounted($take){

        $NewOffers = Discounted_offer::
        join('offers_branches', 'offers_branches.id', '=', 'discounted_offers.brache_id')
        ->join('offers', 'offers.id', '=', 'discounted_offers.offer_id')
        ->select('offers.*','offers_branches.branche as Branche','discounted_offers.discount')
        ->when($take !== "All", function ($q) use ($take) { return $q->take($take); })
        ->get();
        
        return ResultNoSB($NewOffers);

    }

    //==================================== Favourites
    public function OfferFavourite(Request $request){

        $user = user();
        $account_type = (user_role() == 'users') ? 2 : 3;

        $validator = Validator::make($request->all(), [
            'offer_id' => 'required|numeric',
        ]);

        if($validator->fails()){ return response()->json($validator->errors(), 400); }

        if (!Offer::find($request->get('offer_id'))) { return Result(Null, 400, 'Offer Not Found'); }

        if (Offers_favourite::where('offer_id', $request->get('offer_id'))->where('user_id', $user->id)->where('account_type', $account_type)->first())
        { return Result(Null, 400, 'Already Set As Favourite !'); }

        $New = new Offers_favourite;
        $New->offer_id = $request->get('offer_id');
        $New->user_id = $user->id;
        $New->account_type = $account_type;
        $New->save();

        return Result("seccuss", 200);
    }
    
    public function OfferFavouriteDelete(Request $request){
        $user = user();
        $account_type = (user_role() == 'users') ? 2 : 3;

        $validator = Validator::make($request->all(), [ 'id' => 'required|numeric', ]);

        if($validator->fails()){ return response()->json($validator->errors(), 400); }

        if (!Offers_favourite::where('id', $request->get('id'))->where('user_id', $user->id)->where('account_type', $account_type)->first())
        { return Result(Null, 400, 'The Offer Is not in the favourites list'); }

        Offers_favourite::find($request->get('id'))->delete();

        return Result("seccuss", 200);
    }

    public function MyFavourites(){
        $user = user();
        $account_type = (user_role() == 'users') ? 2 : 3;

        $Get = Offers_favourite::where('user_id', $user->id)->where('account_type', $account_type)
            ->join('offers', 'offers.id', '=', 'offers_favourites.offer_id')
            ->select('offers_favourites.id as favourites_ID','offers.*')
            ->get()
            ->makeHidden(['id']);

        return ResultNoSB($Get, 200);
    }

    //============================================== FILER
    public function Filter(Request $request){

        $validator = Validator::make($request->all(), [
            'Branch_id' => 'required|numeric',
            'gender' => ['required', Rule::in(['All','male','female'])],
            'pricefrom' => 'required|numeric', 
            'priceto' => 'required|numeric', 
            'take' => 'required|numeric', 
        ]);

        if($validator->fails()){ return response()->json($validator->errors(), 400); }
        
        $get = Offer::where('brache_id', $request->get('Branch_id'))
        ->when($request->get('gender') !== 'All', function ($q) { return $q->where('gender', $request->get('gender')); })
        ->whereBetween('price', [$request->get('pricefrom'), $request->get('priceto')])
        ->take($request->get('take'))
        ->get();

        return ResultNoSB($get, 200);
    }

    public function search(Request $request){

        $validator = Validator::make($request->all(), [ 
            'keyword' => 'required|string', 
        ]);

        if($validator->fails()){ return response()->json($validator->errors(), 400); }
        
        $get = Offer::where('product_name', '%'.'LIKE'.'%', $request->get('keyword'))->orWhere('gender', $request->get('keyword'))->get();

        return ResultNoSB($get, 200);
    }

    //ORDERS
    public function ViewGiftOrder($order_id, $member_id, $member_role){
        //return $member_role;
        $Offer = Offers_order::where('offers_orders.id', $order_id)
        ->join('offers', 'offers.id', '=', 'offers_orders.offer_id')
        ->when($member_role == 2, function($q) { 
            $q->leftjoin('users', 'users.id', '=', 'offers_orders.account_id');
        })
        ->when($member_role == 3, function($q) { 
            $q->leftjoin('stores', 'stores.id', '=', 'offers_orders.account_id');
        })   
        ->first();

        return Result($Offer);
    }

    public function UpdateGiftOrderStatus(Request $request){
        
        //return $member_role;
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|numeric|exists:offers_orders,id',
            'status' => ['required', Rule::in(['waiting','pending','delivered'])],
        ]);

        if($validator->fails()){ return response()->json($validator->errors(), 400); }
        
        $update = Offers_order::find($request->get('order_id'));
        $update->status = $request->get('status');
        $update->save();
        
        $GetPoster = table_byAccountType($update->account_type, $update->account_id);
        $Notif = GiftNotifications($request->get('status'), Offer::find($update->offer_id)->product_name);
        Eventing($Notif, "#", ['admins','delivers','support']);
        Notification([$GetPoster->firebase_token], 'تم تحديث حالة الطلب', $Notif);

        return Result('seccuss');
    }

    public function GiftOrderSearch($keyword){
        
        $search = Offers_order::where('order_number', $keyword)->first();
        if(!$search) { return Result(Null, 404); }

        $Offer = Offers_order::where('offers_orders.order_number', $keyword)
        ->join('offers', 'offers.id', '=', 'offers_orders.offer_id')
        ->when($search->account_type == 2, function($q) { 
            $q->leftjoin('users', 'users.id', '=', 'offers_orders.account_id')
            ->select('offers_orders.*', 'offers_orders.id as order_id','users.*', 'offers.*');
        })
        ->when($search->account_type == 3, function($q) { 
            $q->leftjoin('stores', 'stores.id', '=', 'offers_orders.account_id')
            ->select('offers_orders.*', 'offers_orders.id as order_id','stores.*', 'offers.*');
        })
        ->first();
        
        return ResultNoSB($Offer);
    }
}
