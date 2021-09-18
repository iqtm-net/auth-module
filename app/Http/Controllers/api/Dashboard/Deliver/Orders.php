<?php

namespace App\Http\Controllers\api\Dashboard\Deliver;

use App\Deliver;
use App\User_order;
use App\Orders_group;
use App\Forward_order;
use App\Member_stack;
use App\Forward_cart;
use App\Receiving_deliver;
use App\Gd_warehouse;
use App\Txt_service;
use App\Gd;
use App\Orders_black_list;
use App\Order_status_changes_history;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;
Use \Carbon\Carbon;
use App\Events\UpdateDeliverLocation;
use Illuminate\Validation\Rule;


class Orders extends Controller
{

    public function update_track_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'numeric|exists:user_orders,id',
            'track_code' => 'exists:user_orders,track_code',
            'status' => ['required', Rule::In(config('constants.sys_statuses'))],
            'case_details' => 'required_if:status,=,ReturnedToDeliver|required_if:status,=,ReturnedToClient|string|max:255'
        ]);

        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

        $update = User_order::where('id', $request->get('id'))->orWhere('track_code', $request->get('track_code'))->first();

        if (!$update) { return ResultNoSB("Order id or track_code Was Not Found", 404); }
            
        $GetPoster = table_byAccountType($update->account_type, $update->user_id);

        if ($request->get('status') == "delivered") {

            if($update->status == 'delivered') { return Result("already delivered !", 400); }

            //deal with user store balance
            $GetPoster->update([
                "balance" => $GetPoster->balance + $update->recieved_price
            ]);

            //put in member stack
            $New_M_Stack = new Member_stack;
            $New_M_Stack->deliver_id = user()->id;
            $New_M_Stack->member_id = $GetPoster->id;
            $New_M_Stack->account_type = $update->account_type;
            $New_M_Stack->order_id = $update->id;
            $New_M_Stack->popped = 0;
            $New_M_Stack->save();
            
            Notification(
                [$GetPoster->firebase_token], " تم تحديث حالة الطلب للبريد ".$update->track_code,
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

        if ($request->get('status') == "pending") {
            Notification(
                [$GetPoster->firebase_token], 
                " تم استلام البريد "."(".$update->product_name.")",
                " سوف يباشر هدهد بعملة ايصال بريدك "."(".$update->product_name.")"." الى المستلم "."(".$update->receiver_full_name.")",
                $GetPoster->phone_number
            );
        }

        $update->status = $request->get('status');
        $update->case_details = ($request->has('case_details')) ? $request->get('case_details') : Null;
        $update->save();

        $Order_status_changes_history = new Order_status_changes_history;
        $Order_status_changes_history->order_id = $update->id;
        $Order_status_changes_history->deliver_id = user()->id;
        $Order_status_changes_history->user_id = $update->user_id;
        $Order_status_changes_history->account_type = $update->account_type;
        $Order_status_changes_history->status = $request->get('status');
        $Order_status_changes_history->save();

        Eventing("Order (".$update->track_code.") status was updated to (".$update->status.")" , "/order_details/".$update->id, ['admins', 'delivers']);

        return ResultNoSB("succuss");
    }

    public function update_location(Request $request)
    {
        $validator = Validator::make($request->all(), [ 'current_location_on_map' => 'required|string|max:255', ]);

        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

        User_order::where('deliver_id', user()->id)
            ->where(function ($query) { $query->Where('status', 'pending')->orWhere('status', 'waiting'); })
            ->update(['current_location_on_map' => $request->get('current_location_on_map')]);

        $update_Deliver_location = Deliver::find(user()->id);
        $update_Deliver_location->more_address_details = $request->get('current_location_on_map');
        $update_Deliver_location->save();

        event(new UpdateDeliverLocation([
            'deliver_id' => user()->id,
            'Current_location_lng' => explode(',', $request->get('current_location_on_map'))[1],
            'Current_location_lat' => explode(',', $request->get('current_location_on_map'))[0],
        ]));

        return Result("succuss");
    }

    public function DelayScheduling()
    {
        //Pending Orders 48 hours Delay
        $pending =  User_order::Where('status', 'pending')->whereNotNull('deliver_id')
            ->where('updated_at', '<=', Carbon::now()->subHours(48))->get();
        foreach ($pending as $get){
            Notification([Deliver::find($get->deliver_id)->firebase_token],
                "تنبيه تأخر البريد"."(".$get->track_code.")",
                " لم يتم تسليم البريد"."(".$get->track_code.")"."الى الزبون منذ 48 ساعة");
        }

        //Waiting Orders 4 hours Delay
        $waiting =  User_order::Where('status', 'waiting')->whereNotNull('deliver_id')
            ->where('updated_at', '<=', Carbon::now()->subHours(0))->get();
        foreach ($waiting as $get){
            Notification([Deliver::find($get->deliver_id)->firebase_token],
                "تنبيه تأخر البريد"."(".$get->track_code.")",
                "مرت 4 ساعات على البريد المقبول"."(".$get->product_name.")"."يرجى المباشرة بعملية توصيل البريد ووضعه في قيد المعالجة");
        }
    }

    public function Set_Delay_Status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'numeric|exists:user_orders,id',
        ]);

        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

        User_order::where('id', $request->get('order_id'))->update([
            "delayed" => 1
        ]);

        return ResultNoSB("succuss");
    }


}
