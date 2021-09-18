<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Gifts_event;
use App\Offer;
Use \Carbon\Carbon;
class GiftsEventsReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Notify:GiftsEventsReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify Members Gifts Events';

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
        $get = Gifts_event::whereDate('date', '=', Carbon::now()->format('Y-m-d'))->get();

        foreach($get as $order){
            $GetPoster = table_byAccountType($order->account_type, $order->member_id);
            $Offer = Offer::find($order->offer_id);

            Notification([$GetPoster->firebase_token], 
            'لديك مناسبة اليوم : ',
            'ملاحضتك : '.$order->event_note.
            'المنتج المختار للمناسبة : '.$Offer->event_note.
            '');
        }
    }
}
