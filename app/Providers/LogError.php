<?php

namespace App\Providers;

use App\Models\Log;
use App\Providers\PaymentFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Event;

class LogError extends Event
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentFailed|ImportSkipped $event): void
    {
        if ($event instanceof PaymentFailed) {
            Log::query()->create([
                'message' => 'Payment failed',
                'details' => json_encode($event->details),
                'is_critical' => false,
            ]);
        }

        if ($event instanceof ImportSkipped) {
            Log::query()->create([
                'message' => $event->message,
                'details' => json_encode($event->details),
                'is_critical' => false,
            ]);
        }
    }
}
