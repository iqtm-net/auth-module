<?php

namespace App\Http\Controllers\api\Dashboard\Admin;

use App\Models\HodHod_Taxi\Taxi_trip;
use App\Models\HodHod_Taxi\Taxi_type;
use App\Models\HodHod_Taxi\Taxi_trip_confirmation_history;
use App\Models\HodHod_Taxi\Taxi_trip_status_update_history;
use App\Models\HodHod_Taxi\Taxi_favourite_location;
use App\Models\HodHod_Taxi\Taxi_notification;
use App\Models\HodHod_Taxi\Taxi_redeem_code;
use App\Models\HodHod_Taxi\Taxi_fines_history;
use App\Models\HodHod_Taxi\Taxi_states_price;
use App\Exports\RedeemCodes;
use App\Store;
use App\User;
use App\Taxi;
use App\Exports\Withdraw;
Use \Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\DB;
use File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class HodHod_Taxi extends Controller
{
    
    // ================== TYPES =================== //
    public function Types(){
        $get = Taxi_type::all();
        return ResultNoSB($get);
    }
    
    public function Delete_Types(Request $request){
        $validator = Validator::make($request->all(), [ 'id' => 'required|numeric|exists:Taxi_types,id',  ]);
        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }

        $posts = Taxi_type::find($request->get('id'))->delete();
        return response()->json(null, 200);
    }

    public function Add_Types(Request $request)
    {   
        $validator = Validator::make($request->all(), [ 
            'car_type' => 'required',
            'km' => 'required|numeric',
            'second' => 'required|numeric',
            'price' => 'required|numeric', 
            'fine' => 'required|numeric' 
        ]);
        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }

        $post = new Taxi_type;
        $post->car_type = $request->get('car_type');
        $post->km = $request->get('km');
        $post->second = $request->get('second');
        $post->price = $request->get('price');
        $post->fine = $request->get('fine');
        $post->save();

        return response()->json(null, 200);
    }

    public function Update_Types(Request $request)
    {   
        $validator = Validator::make($request->all(), [ 
            'id' => 'required|numeric|exists:taxi_types,id',
            'car_type' => 'string',
            'km' => 'numeric',
            'second' => 'numeric',
            'price' => 'numeric', 
            'fine' => 'numeric' 
        ]);

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }
        
        $post = Taxi_type::find($request->get('id'));
        $post->car_type = $request->get('car_type');
        $post->km = $request->get('km');
        $post->second = $request->get('second');
        $post->price = $request->get('price');
        $post->fine = $request->get('fine');
        $post->save();

        return response()->json(null, 200);
    }

    // ================== FINES =================== //
    public function Fines($Role, $Id){
        
        $get = Taxi_fines_history::when($Role !== 'All' && $Id !== 'All', function($q){
            return $q->where('member_role', $Role)->where('member_id', $Id);
        })
        ->join('taxi_trips', 'taxi_trips.id', '=', 'taxi_fines_histories.trip_id')
        ->paginate(50)
        ->map(function ($fine) {
            $Member = DB::table($fine->member_role)->where('id', $fine->member_id)->first();
            $fine['Member_Fname'] = $Member->first_name; 
            $fine['Member_Lname'] = $Member->last_name; 
            $fine['Member_Phone'] = $Member->phone_number; 
            return $fine;
        });

        return ResultNoSB($get);
    }

    // ================== TRIPS =================== //
    public function Get_Trips($status,$dateFrom,$dateto)
    {
        $get = Taxi_trip::
        when($dateFrom !== 'All' AND $dateto !== 'All', function ($q) use($dateFrom, $dateto) {
            return $q->whereBetween('taxi_trips.created_at', [$dateFrom, $dateto]);
        })
        ->when($status !== 'All', function ($q) use ($status){
            return $q->where('taxi_trips.status', $status);
        })
        ->join('taxis', 'taxis.id', '=', 'taxi_trips.taxi_id')
        ->select(
            'taxi_trips.*',
            'taxis.first_name as Taxi_Fname',
            'taxis.last_name as Taxi_Lname',
            'taxis.phone_number as Taxi_Phone',
        )
        ->paginate(50)
        ->map(function ($user) {
            $client = DB::table($user->client_role)->where('id', $user->client_id)->first();
            $user['Client_Fname'] = $client->first_name; 
            $user['Client_Lname'] = $client->last_name; 
            $user['Client_Phone'] = $client->phone_number; 
            return $user;
        });

        return response()->json($get);
    }

    public function View_Trip($id)
    {
        $get = Taxi_trip::where('taxi_trips.id', $id)
        ->join('taxis', 'taxis.id', '=', 'taxi_trips.taxi_id')
        ->select(
            'taxi_trips.*',
            'taxis.first_name as Taxi_Fname',
            'taxis.last_name as Taxi_Lname',
            'taxis.phone_number as Taxi_Phone',
        )
        ->get()
        ->map(function ($user) {
            $client = DB::table($user->client_role)->where('id', $user->client_id)->first();
            $user['Client_Fname'] = $client->first_name; 
            $user['Client_Lname'] = $client->last_name; 
            $user['Client_Phone'] = $client->phone_number; 
            return $user;
        });

        return response()->json($get);
    }

    public function Update_Trips(Request $request){

        $validator = Validator::make($request->all(), [ 
            'id' => 'required|numeric|exists:taxi_trips,id',
            'distance' => 'numeric',
            'basic_price' => 'numeric',
            'final_price' => 'numeric',
            'expected_arrival_minutes' => 'numeric', 
            'canceled_by' => 'string',
            'cancelation_reason' => 'string'
        ]);

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }
        
        $post = Taxi_trip::find($request->get('id'));
        $post->distance = $request->get('distance');
        $post->basic_price = $request->get('basic_price');
        $post->final_price = $request->get('final_price');
        $post->expected_arrival_minutes = $request->get('expected_arrival_minutes');
        $post->canceled_by = $request->get('canceled_by');
        $post->cancelation_reason = $request->get('cancelation_reason');
        $post->save();

        return response()->json(null, 200);
    }

    public function Search_Trips($keyword){

        $get = Taxi_trip::where('taxi_trips.Code', $keyword)
        ->join('taxis', 'taxis.id', '=', 'taxi_trips.taxi_id')
        ->select(
            'taxi_trips.*',
            'taxis.first_name as Taxi_Fname',
            'taxis.last_name as Taxi_Lname',
            'taxis.phone_number as Taxi_Phone',
        )
        ->get()
        ->map(function ($user) {
            $client = DB::table($user->client_role)->where('id', $user->client_id)->first();
            $user['Client_Fname'] = $client->first_name; 
            $user['Client_Lname'] = $client->last_name; 
            $user['Client_Phone'] = $client->phone_number;

            $fine = Taxi_fines_history::where('trip_id', $user->id)->first();
            $user['Fine'] = ($fine) ? $fine->fine : 0;

            return $user;
        });
        
        if($get->count() > 0){ return response()->json($get, 200); }
        return response()->json(null, 404);
    }

    // ============ State Prices =================== //
    public function State_Prices(){
        $get = Taxi_states_price::all();
        return ResultNoSB($get);
    }
    
    public function Delete_Prices(Request $request){
        $validator = Validator::make($request->all(), [ 'id' => 'required|numeric|exists:taxi_states_prices,id',  ]);
        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }

        $posts = Taxi_states_price::find($request->get('id'))->delete();
        return response()->json(null, 200);
    }

    public function Add_Prices(Request $request)
    {   
        $validator = Validator::make($request->all(), [ 
            'distance_range_from' => 'required|numeric',
            'distance_range_to' => 'required|numeric',
            'price' => 'required|numeric', 
        ]);
        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }

        $post = new Taxi_states_price;
        $post->distance_range_from = $request->get('distance_range_from');
        $post->distance_range_to = $request->get('distance_range_to');
        $post->price = $request->get('price');
        $post->save();

        return response()->json(null, 200);
    }

    public function Update_Prices(Request $request)
    {   
        $validator = Validator::make($request->all(), [ 
            'id' => 'required|numeric|exists:taxi_states_prices,id',
            'distance_range_from' => 'required|numeric',
            'distance_range_to' => 'required|numeric',
            'price' => 'required|numeric', 
        ]);

        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }
        
        $post = Taxi_states_price::find($request->get('id'));
        $post->distance_range_from = $request->get('distance_range_from');
        $post->distance_range_to = $request->get('distance_range_to');
        $post->price = $request->get('price');
        $post->save();

        return response()->json(null, 200);
    }

    // ======== Trip Confirmation History ========= //
    public function Trip_Confirmation_History($status,$dateFrom,$dateto){
        
        $get = Taxi_trip_confirmation_history::
        when($dateFrom !== 'All' AND $dateto !== 'All', function ($q) use($dateFrom, $dateto) {
            return $q->whereBetween('taxi_trip_confirmation_histories.created_at', [$dateFrom, $dateto]);
        })
        ->when($status !== 'All', function ($q) use($status) {
            return $q->where('taxi_trip_confirmation_histories.accepted', $status);
        })
        ->join('taxis', 'taxis.id', '=', 'taxi_trip_confirmation_histories.taxi_id')
        ->select(
            'taxi_trip_confirmation_histories.*',
            'taxis.first_name as Taxi_Fname',
            'taxis.last_name as Taxi_Lname',
            'taxis.phone_number as Taxi_Phone',
        )
        ->paginate(10);

        return response()->json($get);
    }

    public function Search_Confirmation($keyword){

        $search = Taxi_trip::where('Code', $keyword)->first();

        if($search){

            $get = Taxi_trip_confirmation_history::where('taxi_trip_confirmation_histories.trip_id', $search->id)
            ->join('taxis', 'taxis.id', '=', 'taxi_trip_confirmation_histories.taxi_id')
            ->select(
                'taxi_trip_confirmation_histories.*',
                'taxis.first_name as Taxi_Fname',
                'taxis.last_name as Taxi_Lname',
                'taxis.phone_number as Taxi_Phone',
            )
            ->get();

            return response()->json($get, 200);
        }

        return response()->json(null, 404);
    }

    // ======== Trip Status History ========= //
    public function Trip_Status_History($status,$dateFrom,$dateto){
        
        $get = Taxi_trip_status_update_history::
        when($dateFrom !== 'All' AND $dateto !== 'All', function ($q) use($dateFrom, $dateto) {
            return $q->whereBetween('taxi_trip_status_update_histories.created_at', [$dateFrom, $dateto]);
        })
        ->when($status !== 'All', function ($q) use($status) {
            return $q->where('taxi_trip_status_update_histories.status', $status);
        })
        ->join('taxis', 'taxis.id', '=', 'taxi_trip_status_update_histories.taxi_id')
        ->select(
            'taxi_trip_status_update_histories.*',
            'taxis.first_name as Taxi_Fname',
            'taxis.last_name as Taxi_Lname',
            'taxis.phone_number as Taxi_Phone',
        )
        ->paginate(10);

        return response()->json($get);
    }

    public function Search_Status($keyword){

        $search = Taxi_trip::where('Code', $keyword)->first();
        
        if($search){

            $get = Taxi_trip_status_update_history::where('taxi_trip_status_update_histories.trip_id', $search->id)
            ->join('taxis', 'taxis.id', '=', 'taxi_trip_status_update_histories.taxi_id')
            ->select(
                'taxi_trip_status_update_histories.*',
                'taxis.first_name as Taxi_Fname',
                'taxis.last_name as Taxi_Lname',
                'taxis.phone_number as Taxi_Phone',
            )
            ->get();

            return response()->json($get, 200);
        }

        return response()->json(null, 404);
    }

    // ================ Taxis =============== // 
    public function Register_Taxi(Request $request)
    {
        $REQ = $request->all();
        $REQ['phone_number'] = "9647".explode("7", AR_TO_EN($request->get('phone_number')), 2)[1];

        $validator = Validator::make($REQ, [
            'first_name' => 'required',
            'last_name' => 'required',
            'car_type' => 'required',
            'car_color' => 'required',
            'car_license_number' => 'required',
            'password' => 'required|string|min:6|confirmed',
            'confirmed' => 'required',
            'available' => 'required',
            'phone_number' => 'required|numeric|digits_between:10,13|unique:delivers|unique:admins|unique:users|unique:stores|unique:receivers|unique:taxis',
            'car_docs_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,pdf',
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg,pdf',
        ],CostumVal());

        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

        $new = new Taxi;
        $new->first_name = $request->get('first_name');
        $new->last_name = $request->get('last_name');
        $new->car_type = $request->get('car_type');
        $new->car_color = $request->get('car_color');
        $new->car_license_number = $request->get('car_license_number');
        $new->password = Hash::make($request->get('password'));;
        $new->confirmed = ($request->get('confirmed') == "true") ? 1 : 0;
        $new->available = ($request->get('available') == "true") ? 1 : 0;
        $new->phone_number = $REQ['phone_number'];
        $new->profile_picture = images($request, "profile_picture", "images/Taxis");
        $new->car_docs_image = images($request, "car_docs_image", "images/Taxis");
        $new->Code = Str::random(7);
        $new->current_location = "33.3152,44.3661";
        $new->wallet = 0;
        $new->debts = 0;
        $new->save();

        return response()->json("Seccuss", 200);

    }

    // ================ Redeem Code =============== // 
    public function Get_Codes($available)
    {
        $get = Taxi_redeem_code::where('taxi_redeem_codes.available', $available)
        ->when($available == "0", function ($q) {
            return $q->join('taxis', 'taxis.id', '=', 'taxi_redeem_codes.taxi_id')
            ->select(
                'taxi_redeem_codes.*',
                'taxis.first_name as Taxi_Fname',
                'taxis.last_name as Taxi_Lname',
                'taxis.phone_number as Taxi_Phone',
            );
        })
        ->orderBy('id', 'DESC')
        ->paginate(50);

        return response()->json($get);

    }

    public function Add_Codes(Request $request)
    {   
        $validator = Validator::make($request->all(), [ 
            'points' => 'required|numeric',
            'quantity' => 'required|numeric' 
        ]);
        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }
        
        $Codes = [];
        for ($i=0; $i < $request->get('quantity'); $i++) {

            $post = new Taxi_redeem_code;
            $post->points = $request->get('points');
            $post->code = Str::random(12);
            $post->available = 1;
            $post->save();

            $Codes[] = [
                'points' => $post->points,
                'code' => $post->code,
            ];
        }
        
        return response()->json($Codes, 200);
    }

    public function DownloadRedeemCodes(Request $request)
    {   
        return Excel::download(new RedeemCodes($request->get('Codes')), Carbon::now().'.xlsx');
    }
    
    public function Delete_Code(Request $request){
        $validator = Validator::make($request->all(), [ 'id' => 'required|numeric|exists:taxi_redeem_codes,id',  ]);
        if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }

        $posts = Taxi_redeem_code::find($request->get('id'))->delete();
        return response()->json(null, 200);
    }

    // ================ Taxi Map =============== //
    public function Map_Get_Trips($status){

        $get = Taxi_trip::where('status', $status)
        ->get()
        ->map(function ($trip) {
            $trip['client_localtion_lat'] = Location_Filter($trip->start_location, 0);
            $trip['client_localtion_lng'] = Location_Filter($trip->start_location, 1);
            return $trip;
        });

        return response()->json($get);
    }
}
