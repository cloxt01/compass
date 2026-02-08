<?php
namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class JobProcessed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id;
    public $message;

    public function __construct($id, $message)
    {
        $this->id = (string) $id;  // Cast to string untuk serialization
        $this->message = (string) $message;
        Log::info('JobProcessed event created', ['id' => $this->id, 'message' => $this->message]);
    }

    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\Channel('app-ch');
    }

    /**
     * Customize the broadcast payload
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
        ];
    }

    /**
     * Broadcast event name used on the client
     */
    public function broadcastAs()
    {
        return 'JobProcessed';
    }
}