<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class UpdateDeliverLocation extends Event implements ShouldBroadcast
{
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return ['UpdateDeliverLocation-channel'];
    }

    public function broadcastAs()
    {
        return 'UpdateDeliverLocation-Event';
    }
}

