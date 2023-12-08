<?php

declare(strict_types=1);


namespace App\Services;

use App\Interfaces\PaymentInterface;
use App\Models\Order;
use App\Providers\PaymentFailed;
use Illuminate\Support\Facades\Http;
use Throwable;

class PaymentService implements PaymentInterface
{
    /**
     * @param  \App\Models\Order  $order
     * @param  array  $data
     * @return bool
     * @throws \App\Exceptions\PaymentFailed
     */
    public static function pay(Order $order, array $data)
    {
        $customer = $data['customer'];
        unset($data['customer']);

        try {
            $response = Http::post(env('LOOP_PAYMENT_URL'), $data);
        } catch (Throwable $e) {
            $body['message'] = $e->getMessage();
            PaymentFailed::dispatch($order->id, $body);
            throw new \App\Exceptions\PaymentFailed();
        }

        if (Order::validatePaymentMessage($response->json() ?? [])) {
            $order->pay($customer);
            return true;
        } else {
            $data = ['data' => $response->json() ?? [], 'body' => $data];
            PaymentFailed::dispatch($order->id, $data);
            return false;
        }
    }
}
