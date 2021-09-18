<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Gifts_event;
use App\Taxi;
Use \Carbon\Carbon;
class TaxiLocationUpdator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Notify:TaxiLocationUpdator';

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
        $arr = [
            '33.342566,44.452943',
            '33.352623,44.440797',
            '33.357354,44.433212',
            '33.376009,44.411177',
            '33.396172,44.383105',
        ];

        Taxi::where('id', 4)->first()->update([
            'current_location' => $arr[array_rand($arr, 2)[0]]
        ]); 

    }
}
