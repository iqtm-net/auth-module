<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Deliver;
use App\User_order;
use App\Orders_black_list;
use App\Order_status_changes_history;
Use \Carbon\Carbon;
class AlBark_Auto_Updator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Notify:AlBark_Auto_Updator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify delivers for orders delays for pending/waiting status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $json = Anon_http(0,1,
            "https://albarqexpress.com/client/php/_getOrderTrack.php?id=1+union+select+1,note,3,4,5,6,7,customer_phone,id,order_no,date,order_status_id+from+orders+WHERE+client_id+=+479+ORDER+BY+id+DESC+--+",
            0,0,1,1,0,1,0);

        $arr = explode('},',$json);

        //
        foreach ($arr as $item){
            $ljason = json_decode($item."}");
            @$reciever_phone = @ltrim(@$ljason->items_no, '0');
            $phone_numbers[] = $reciever_phone;
        }

        $phones = array_count_values($phone_numbers); //array with all phones and how much repeats

        foreach ($arr as $item){

            $ljason = json_decode($item."}");

            @$reciever_phone = @ltrim(@$ljason->items_no, '0');

            @$order_status = @$ljason->hour;

            if($phones[$reciever_phone] == '1'){ //only for single shop clients

                $update = User_order::where('reciever_phone_number','964'.$reciever_phone)
                    ->where(function ($query) { $query->Where('status', 'pending')->orWhere('status', 'waiting'); })
                    ->first();

                if ($update){
                    
                    $GetPoster = table_byAccountType($update->account_type, $update->user_id);
                    
                    if ($order_status == '4' || $order_status == '9'){

                        if ($order_status == '4') {

                            //deal with user store balance

                            $GetPoster->balance = $GetPoster->balance + $update->recieved_price;
                            $GetPoster->save();

                            $update->status = 'delivered';
                            $update->save();

                            Notification([$GetPoster->firebase_token], " تم تحديث حالة الطلب للبريد ".$update->track_code, " تم ايصال بريد "."(".$update->product_name.")"." يمكنك تقييم عملية التوصيل من خلال قائمة البريد المكتمل. ");

                        }

                        if ($order_status == '9') {

                            //Set Receiver Phone Number In The Black List
                            $set_to_black_list = new Orders_black_list;
                            $set_to_black_list->order_id = $update->id;
                            $set_to_black_list->receiver_phone_number = $update->reciever_phone_number;
                            $set_to_black_list->save();

                            $update->status = 'issued';
                            $update->case_details = (!is_null(@$ljason->order_id)) ? @$ljason->order_id : 'تحقق من الزبون';
                            $update->save();

                            Notification([$GetPoster->firebase_token], 'حالة الطلب', " لم نستطع ايصال بريد "."(".$update->receiver_full_name.")"." بسبب "."(".$update->case_details.")");
                        }

                        $Order_status_changes_history = new Order_status_changes_history;
                        $Order_status_changes_history->order_id = $update->id;
                        $Order_status_changes_history->deliver_id = 83;
                        $Order_status_changes_history->user_id = $update->user_id;
                        $Order_status_changes_history->account_type = $update->account_type;
                        if ($order_status == '4') { $Order_status_changes_history->status = 'delivered'; }
                        if ($order_status == '9') { $Order_status_changes_history->status = 'issued'; }
                        $Order_status_changes_history->save();

                    }
                }
            }
        }
        
        EventingPublic( 'auto_updator', '1',  "orders updated", "/center" );
    }
}
