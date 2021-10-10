<?php

    namespace App\Http\Controllers\api;

    use App\Notification;
    use App\Event;
    use App\Gd_Event;
    use App\New_data_counter;
    Use \Carbon\Carbon;
    use App\Http\Controllers\Controller;
    use Tymon\JWTAuth\JWTAuth;
    use Illuminate\Http\Request;
    use App\User_order;

    use Config;

    class my_notifications extends Controller
    { 

        public function My_Notifications()
        {
            $get = Notification::where('MemberPhoneNumber', user()->phone_number)->orWhere('MemberPhoneNumber', 0)
            ->orderBy('id', 'DESC')
            ->get()
            ->map(function ($data) {
                $data['full_name'] = ($data->order_id !== null) ? User_order::where('track_code', $data->order_id)->first()->sender_full_name : null;
                return $data;
            });
            
            return ResultNoSB($get, 200);
        }

        public function events()
        {   

            $get = Event::orderBy('created_at', 'DESC')->paginate(20);
            $get->map(function ($event) {
                $event['Panel'] = json_decode($event->Panel);
                return $event;
            }); 

            return response()->json($get, 200);
        }

        public function update_counter(Request $request){

            New_data_counter::where('object', 'notifications')->update(['updated_at' => Carbon::now()]);
        }

        public function getcounter(){

            $New_data_counter = New_data_counter::where('object', 'notifications')->first();

            $notifications = Event::where('created_at', ">=", $New_data_counter->updated_at)
                ->get()
                ->map(function ($event) {
                    return (in_array(user_role(), json_decode($event->Panel))) ? $event : Null;
                })
                ->toArray();

            return response()->json([ 
                "notifications" => (count(array_filter($notifications)) > 100) ? "+100" : count(array_filter($notifications))
            ], 200);

        }
    }

