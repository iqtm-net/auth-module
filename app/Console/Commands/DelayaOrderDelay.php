<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Deliver;
use App\User_order;
Use \Carbon\Carbon;
class DelayaOrderDelay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Notify:OrderDelay';

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
        //pending Orders Delay
        $pending =  User_order::Where('status', 'pending')
            ->whereNotNull('deliver_id')
            ->where('updated_at', '<', Carbon::now()->subMinutes(60))
            ->get();
            
            EventingPublic(0,0,$pending->count().' orders still pending','/ordersget', ['admins', 'delivers']);
            
        foreach ($pending as $get){
            Notification([Deliver::find($get->deliver_id)->firebase_token],
                "تنبيه تأخر البريد"."(".$get->track_code.")",
                "لم يتم تسليم البريد"."(".$get->track_code.")"."الى الزبون منذ اكثر من ساعة");
        }

        //Waiting Orders Delay
        $waiting =  User_order::Where('status', 'waiting')
            ->whereNotNull('deliver_id')
            ->where('updated_at', '<=', Carbon::now()->subMinutes(30))->get();
            
            EventingPublic(0,0,$waiting->count().' orders still waiting','/ordersget', ['admins', 'delivers']);
            
        foreach ($waiting as $get){
            Notification([Deliver::find($get->deliver_id)->firebase_token],
                "تنبيه تأخر البريد"."(".$get->track_code.")",
                "مرت اكثر من 30 دقيقة على البريد المقبول"."(".$get->product_name.")"."يرجى المباشرة بعملية توصيل البريد ووضعه في قيد المعالجة");
        }
    }
}
