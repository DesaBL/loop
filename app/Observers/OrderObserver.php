<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    public function updated(Order $order)
    {
        if ($order->isDirty('status')) {
            if ($order->isPayed()) {
                // We could send an email here?
            }
        }
    }
}
