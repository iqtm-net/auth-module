<?php

namespace App\Events;

use App\Admin;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Taxi_New_Order extends Event implements ShouldBroadcast
{   
    use SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {   
        return ['Taxi_New_Order-channel'];
    }

    public function broadcastAs()
    {
        return 'Taxi_New_Order-Event';
    }
}

