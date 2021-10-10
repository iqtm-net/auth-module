<?php

namespace App\Imports;

use App\User_order;
use App\User;
use App\Store;
use App\Member_stack;
use App\Order_status_changes_history;
use Tymon\JWTAuth\JWTAuth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Change_orders_status implements ToModel, WithHeadingRow 
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function model(array $row)
    {   
        $rows = array_filter($row);
        
        $status = $row['status'];

        $update = User_order::where('track_code', $row['order_id'])->first();

        $GetPoster = ($update->account_type == 2) ? User::find($update->user_id): Store::find($update->user_id);

        if ($status == "delivered") {

            if($update->status == 'delivered') { return Result("already delivered!", 400); }

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
                $GetPoster->phone_number,
                $update->track_code
            );

        }

        if (in_array($status, ['ReturnedToClient','ReturnedToDeliver'])) {

            //Set Receiver Phone Number In The Black List
            $set_to_black_list = new Orders_black_list;
            $set_to_black_list->order_id = $update->id;
            $set_to_black_list->receiver_phone_number = $update->reciever_phone_number;
            $set_to_black_list->save();

            Notification(
                [$GetPoster->firebase_token], 
                'حالة الطلب',
                " لم نستطع ايصال بريد "."(".$update->receiver_full_name.")"." بسبب "."(".$update->case_details.")",
                $GetPoster->phone_number,
                $update->track_code
            );
        }

        if ($status == "pending") {
            Notification(
                [$GetPoster->firebase_token], 
                " تم استلام البريد "."(".$update->product_name.")",
                " سوف يباشر هدهد بعملة ايصال بريدك  "."(".$update->product_name.")"." الى المستلم "."(".$update->receiver_full_name.")",
                $GetPoster->phone_number,
                $update->track_code
            );
        }

        $update->status = $status;
        $update->save();
        
        //Set in history
        $Order_status_changes_history = new Order_status_changes_history;
        $Order_status_changes_history->order_id = $update->id;
        $Order_status_changes_history->deliver_id = $update->deliver_id;
        $Order_status_changes_history->user_id = $update->user_id;
        $Order_status_changes_history->account_type = $update->account_type;
        $Order_status_changes_history->status = $status;
        $Order_status_changes_history->save();
       
    }
}