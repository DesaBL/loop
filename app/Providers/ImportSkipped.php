<?php

namespace App\Providers;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportSkipped
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message;
    public array $details;

    /**
     * Create a new event instance.
     */
    public function __construct(array $details, string $module)
    {
        $this->details = $details;
        $this->message = $module . ' import skipped';
    }
}
