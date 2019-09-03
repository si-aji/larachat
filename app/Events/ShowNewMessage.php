<?php

namespace App\Events;

use Illuminate\Broadcasting\PresenceChannel;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ShowNewMessage  implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id;
    public $to;
    public $from;
    public $message;
    public $status;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->to = $data['to'];
        $this->from = $data['from'];
        $this->message = $data['message'];
        $this->status = $data['status'];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['show-message'];
    }

    public function broadCastAs()
    {
        return 'show-message';
    }
}
