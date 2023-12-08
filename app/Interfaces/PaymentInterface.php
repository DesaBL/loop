<?php

declare(strict_types=1);


namespace App\Interfaces;

use App\Models\Order;

interface PaymentInterface
{
    public static function pay(Order $order, array $data);
}
