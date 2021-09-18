<?php

namespace App\Listeners;

use App\Events\RTNotify;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class Notifications
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\RTNotify  $event
     * @return void
     */
    public function handle(RTNotify $event)
    {
        //
    }
}
