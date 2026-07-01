<?php
namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class JobStatus implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $job_id;
    public $provider;
    public $status;

    public function __construct($user_id, $job_id, $provider, $status)
    {
        $this->user_id = $user_id;
        $this->job_id = (string) $job_id;
        $this->provider = (string) $provider;
        $this->status = (string) $status;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('users.' . $this->user_id),
        ];
    }

    /**
     * Customize the broadcast payload
     */
    public function broadcastWith()
    {

        return [
            'job_id' => $this->job_id,
            'provider' => $this->provider,
            'status' => $this->status,
        ];
    }
    public function broadcastAs()
    {
        return 'JobStatus';
    }
}
