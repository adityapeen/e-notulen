<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNote implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $noteId, $userId, $text;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($noteId, $userId, $text)
    {
        $this->noteId   = $noteId;
        $this->userId   = $userId;
        $this->text     = $text;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('note.'.$this->noteId);
    }
}
