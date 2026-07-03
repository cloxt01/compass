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
    public $jobData;
    public $provider;
    public $status;

    public function __construct(int $user_id, $jobData, string $provider, string $status)
    {
        $this->user_id = $user_id;
        $this->jobData = (array) $jobData;
        $this->provider = $provider;
        $this->status =  $status;
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
        Log::info('ID : '.$this->user_id.' | JobStatus Event Payload: ' . json_encode($this->jobData));
        return [
            'data' => [
                'job' => [
                    'id' => $this->jobData['metadata']['id'] ?? null,
                    'title' => $this->jobData['metadata']['title'] ?? null,
                    'company' => $this->jobData['metadata']['company'] ?? null,
                ],
            ],
            'provider' => $this->provider,
            'status' => $this->status,
        ];
    }
    public function broadcastAs()
    {
        return 'JobStatus';
    }
}
