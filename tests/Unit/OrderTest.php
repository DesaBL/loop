<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_validate_payment_message()
    {
        $this->assertTrue(Order::validatePaymentMessage(['message' => 'Payment Successful']));
        $this->assertFalse(Order::validatePaymentMessage(['message' => 'Payment Failed']));
        $this->assertFalse(Order::validatePaymentMessage(['other_key' => 'Payment Failed']));
        $this->assertFalse(Order::validatePaymentMessage([]));
    }

    public function test_payment()
    {
        $order = Order::factory()->create(['status' => 0]);
        $customer = Customer::factory()->create();

        $this->assertFalse($order->isPayed());
        $this->assertEquals('Not payed', $order->statusLabel);

        $order->pay($customer);
        $order = $order->fresh();

        $this->assertTrue($order->isPayed());
        $this->assertEquals('Payed', $order->statusLabel);
    }

    public function test_product_price_change_doesnt_affect_order_price()
    {
        $order = Order::factory()->create();
        Product::factory()->count(3)->create()->each(function ($product) use ($order) {
            $order->products()->attach($product, ['price' => $product->price]);
        });

        $this->assertEquals($order->price, Product::query()->sum('price'));

        $product = Product::query()->orderByDesc('created_at')->first();
        $product->update(['price' => $product->price + 10]);

        $this->assertNotEquals($order->price, Product::query()->sum('price'));
        $this->assertEquals($order->price + 10, Product::query()->sum('price'));
    }
}
