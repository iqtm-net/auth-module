<?php

    namespace App\Http\Controllers\api;

    use App\Models\HodHod_Taxi\Taxi_trip;
    use App\Models\HodHod_Taxi\Taxi_type;
    use App\Models\HodHod_Taxi\Taxi_trip_confirmation_history;
    use App\Models\HodHod_Taxi\Taxi_trip_status_update_history;
    use App\Models\HodHod_Taxi\Taxi_favourite_location;
    use App\Models\HodHod_Taxi\Taxi_notification;
    use App\Models\HodHod_Taxi\Taxi_redeem_code;
    use App\Models\HodHod_Taxi\Taxi_fines_history;
    use App\Taxi;
    use App\Event;
    use App\Admin;
    use App\Events\Taxi_New_Order;
    use App\Events\Taxi_Update_Location;
    Use \Carbon\Carbon;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rule;
    use App\Http\Controllers\Controller;
    use Tymon\JWTAuth\JWTAuth;
    use Illuminate\Http\Request;

    use Config;

    class HodHod_Taxi extends Controller
    { 

        public function Prices(){
            $get = Taxi_type::all();
            return ResultNoSB($get);
        }

        public function NewOrder(Request $request)
        {
            $REQ = $request->all();
            $taxi_types = Taxi_type::pluck('car_type')->all();
            $validator = Validator::make($REQ, [
                'taxi_type' => ['required', Rule::in($taxi_types), 'exists:taxi_types,car_type'],
                'taxi_id' => 'required|numeric|exists:taxis,id',
                'start_location' => 'required|string',
                'start_state' => 'required|string',
                'end_location' => 'required|string',
                'end_state' => 'required|string',
                'distance' => 'required|numeric',
                'basic_price' => 'required|numeric',
                'final_price' => 'numeric',
                'expected_arrival_minutes' => 'required'
            ]);
    
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
            
            $REQ['client_id'] = user()->id;
            $REQ['client_role'] = user_role();
            $REQ['final_price'] = ($REQ['start_state'] !== $REQ['end_state'] && $request->has('final_price')) ? $REQ['final_price'] : Null;
            $REQ['Code'] = Track_Code_Rand();
            $REQ['status'] = "waiting";
            
            $order = Taxi_trip::create($REQ);
            
            Notification([Taxi::find($REQ['taxi_id'])->firebase_token],
                'لديك طلب اجرة جديد',
                ''
            );

            event(new Taxi_New_Order($order));

            //Set To Notifications
            $Add = new Taxi_notification;
            $Add->member_id = $REQ['taxi_id'];
            $Add->member_role = "taxis";
            $Add->title = "لديك طلب اجرة جديد";
            $Add->body = "قم بالدخول الى قائمة الطلاب للأطلاع على تفاصيل الاجرة";
            $Add->save();

            return Result("seccuss");
        }

        public function Get_Taxis()
        {      
            $taxis = Taxi::where('available', 1)->where('confirmed', 1)->select('id', 'first_name', 'last_name', 'current_location')->get();

            return ResultNoSB($taxis);
        }

        public function Get_Trip_Details($id)
        {      
            $trip = Taxi_trip::find($id);

            $taxi = $trip->Taxi_details()->first()->toArray();

            $client = ($trip->client_role == 'users') ? $trip->user_details()->first()->toArray() : $trip->store_details()->first()->toArray();

            return Result(array_merge($trip->toArray(), $taxi, $client));
        }

        public function Trip_Confirmation(Request $request)
        {      
            $validator = Validator::make($request->all(), [
                'trip_id' => 'required|numeric|exists:taxi_trips,id',
                'Accept' => 'required|Boolean',
            ]);
    
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
            
            //Allow Only One Post Each Trip
            if(Taxi_trip_confirmation_history::where('id', $request->get('trip_id'))->where('taxi_id', user()->id)->first()){
                return Result(Null, 400, "One Confirmation Allowed On This Trip");
            }

            //If Confirmed 
            if($request->get('Accept')){

                Taxi_trip::where('id', $request->get('trip_id'))
                ->where('taxi_id', user()->id)
                ->where('status', "waiting")
                ->update(['status' => 'accepted']);
                
                event(new Taxi_New_Order(user()));
            }
            
            //Set In History
            $new = new Taxi_trip_confirmation_history;
            $new->taxi_id = user()->id;
            $new->trip_id = $request->get('trip_id');
            $new->accepted = $request->get('Accept');
            $new->save();
            

            return Result("seccuss");

        }

        public function Trip_Next_Proper_Taxi(Request $request)
        {    
            $validator = Validator::make($request->all(), [ 'trip_id' => 'required|numeric|exists:taxi_trips,id' ]);
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

            //Allow Only Unaccepted Trip
            if(Taxi_trip::where('id', $request->get('trip_id'))->where('status', '!=', 'waiting')->first()){
                return Result(Null, 400, "Trip Already Accepted By Other Taxi");
            }

            $list_rejectors = Taxi_trip_confirmation_history::where('trip_id', $request->get('trip_id'))
            ->where('accepted', 0)
            ->pluck('taxi_id')->all();

            $taxis = Taxi::where('available', 1)
            ->where('confirmed', 1)
            ->whereNotIn('id', $list_rejectors)
            ->select('id', 'first_name', 'last_name', 'current_location')
            ->get();

            return ResultNoSB($taxis);
        }

        public function Trip_Redirection(Request $request)
        {    
            $validator = Validator::make($request->all(), [ 
                'trip_id' => 'required|numeric|exists:taxi_trips,id',
                'taxi_id' => 'required|numeric|exists:taxis,id'
            ]);
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

            $update = Taxi_trip::find($request->get('trip_id'));
            $update->taxi_id = $request->get('taxi_id');
            $update->save();
            
            Notification([Taxi::find($request->get('taxi_id'))->firebase_token],
                'لديك طلب اجرة جديد',
                ''
            );

            event(new Taxi_New_Order(Taxi::find($request->get('taxi_id'))));

            //Set To Notifications
            $Add = new Taxi_notification;
            $Add->member_id = $request->get('taxi_id');
            $Add->member_role = "taxis";
            $Add->title = "لديك طلب اجرة جديد";
            $Add->body = "قم بالدخول الى قائمة الطلاب للأطلاع على تفاصيل الاجرة";
            $Add->save();

            return Result("seccuss");
        }

        public function Update_Trip_Status(Request $request)
        {   
            
            //Validation
            $validator = Validator::make($request->all(), [ 
                'trip_id' => 'required|numeric|exists:taxi_trips,id',
                'status' => ['required', Rule::in(['moving', 'arrived'])],
                'final_price' => 'required_if:status,=,arrived|numeric'
            ]);

            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
        
            $update = Taxi_trip::where('id', $request->get('trip_id'))->where('taxi_id', user()->id)->first();
            
            if(!$update){ return Result(Null, 400, "Invalid Trip Id"); }
            if($update->status == 'arrived'){ return Result(Null, 400, "Trip Was Over"); }
            
            //Withdraw From Taxi
            if($request->get('status') == "arrived"){
                $Fee = ($request->get('final_price') * 20) / 100;

                $withdraw = Taxi::find(user()->id);
                $withdraw->wallet = $withdraw->wallet - $Fee;
                $withdraw->save();
            }

            //Update status
            $update->status = $request->get('status');
            $update->final_price = ($request->get('status') == "arrived") ? $request->get('final_price') : Null;
            $update->save();
            
            //Set in history
            $Add = new Taxi_trip_status_update_history;
            $Add->taxi_id = user()->id; 
            $Add->trip_id = $request->get('trip_id'); 
            $Add->status = $request->get('status');
            $Add->save();

            return Result("seccuss");
        }

        public function Update_Taxi_Status(Request $request)
        {   
            $validator = Validator::make($request->all(), [  'available' => 'required|Boolean' ]);
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
        
            $update = Taxi::find(user()->id);
            $update->available = $request->get('available');
            $update->save();

            return Result("seccuss");
        }

        public function Update_Taxi_Location(Request $request)
        {   
            $validator = Validator::make($request->all(), [  'current_location' => 'required' ]);
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
            
            // $update = Taxi::find(user()->id); 
            // $update->current_location = $request->get('current_location');
            // $update->save();
            // Taxi::where('id', user()->id)
            // ->select('first_name', 'last_name', 'id', 'current_location')
            // ->first()->toArray()

            $find = [
                'taxi_id' => user()->id,
                'Current_location_lng' => explode(',', $request->get('current_location'))[1],
                'Current_location_lat' => explode(',', $request->get('current_location'))[0],
            ];
            
            event(new Taxi_Update_Location($find));
            
            return Result("seccuss");
        }
        
        public function Fav_Location_Get(Request $request)
        {   
            $get = Taxi_favourite_location::where('client_id', user()->id)->where('client_role', user_role())->get();

            return ResultNoSB($get);
        }

        public function Fav_Location_Add(Request $request)
        {   
            $validator = Validator::make($request->all(), [  
                'start_location' => 'required', 
                'start_state' => 'required',
                'end_location' => 'required', 
                'end_state' => 'required' 
            ]);

            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
        
            $Add = new Taxi_favourite_location;
            $Add->client_id = user()->id;
            $Add->client_role = user_role();
            $Add->start_location = $request->get('start_location');
            $Add->start_state = $request->get('start_state');
            $Add->end_location = $request->get('end_location');
            $Add->end_state = $request->get('end_state');
            $Add->save();

            return Result("seccuss");
        }

        public function Cancel_Trip(Request $request)
        {   
            
            $validator = Validator::make($request->all(), [ 
                'trip_id' => 'required|numeric|exists:taxi_trips,id',
                'cancelation_reason' => 'required',
                'distance_between_client_taxi' => 'required|numeric',
            ]);

            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
            
            
            $account_role = user_role();
            $update = Taxi_trip::where('id', $request->get('trip_id'))
            ->when($account_role == 'taxis', function($q) { $q->where('taxi_id', user()->id); })
            ->when($account_role !== 'taxis', function($q) { $q->where('client_id', user()->id)->where('client_role', $account_role); })
            ->first();
            
            if(!$update){ return Result(Null, 400, "Invalid Trip Id"); }
            if($update->canceled_by !== null){ return Result(Null, 400, "Trip Was Canceled"); }
            
            //Fine algorithm
            if($request->get('distance_between_client_taxi') < 20){
                
                $get_fine = Taxi_type::where('car_type', $update->taxi_type)->first()->fine;

                $fine = Taxi::find(user()->id);
                $fine->wallet = $fine->wallet - $get_fine;
                $fine->save();

                //Set in history
                $history = new Taxi_fines_history;
                $history->trip_id = $request->get('trip_id');
                $history->member_id = user()->id;
                $history->member_role = user_role()->id;
                $history->fine = $get_fine;
                $history->save();
            }
            else{
                $get_fine = 0;
            }
            
            //Cancel Trip
            $update->canceled_by = $account_role;
            $update->cancelation_reason = $request->get('cancelation_reason');
            $update->status = "canceled";
            $update->save();

            return Result([
                "fine" => $get_fine
            ]);
        }

        public function My_Trips($status)
        {   
            $account_role = user_role();
            $get = Taxi_trip::where('status', $status)
            ->when($account_role == 'taxis', function($q) { $q->where('taxi_id', user()->id); })
            ->when($account_role !== 'taxis', function($q) { $q->where('client_id', user()->id)->where('client_role', $account_role); })
            ->get();

            return ResultNoSB($get);
        }
        
        public function My_notifications()
        {   
            $get = Taxi_notification::where('member_id', user()->id)->orderBy('id', 'DESC')->get();

            return ResultNoSB($get);
        }

        public function Redeem_Points(Request $request)
        {   
            $validator = Validator::make($request->all(), [ 
                'code' => 'required|string|exists:taxi_redeem_codes,code',
            ]);

            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }
            
            $find = Taxi_redeem_code::where('code', $request->get('code'))->where('available', 1)->first();
            if(!$find){ return Result(Null, 400, "Code Has Been Expired"); }

            $find->taxi_id = user()->id;
            $find->available = 0;
            $find->save();
            
            $taxi = Taxi::find(user()->id);
            $taxi->wallet = $taxi->wallet + $find->wallet;
            $taxi->save();

            return Result("seccuss");
        }

        public function Car_Types()
        {   
            $get = Taxi_type::all();
            return ResultNoSB($get);
        }
        
    }

