<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Providers\PaymentFailed;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return OrderResource::collection(Order::query()->orderByDesc('created_at')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
        $order = Order::query()->create([]);
        $products = Product::query()
            ->select('id', 'price')
            ->whereIn('id', $request->get('products'))
            ->get();

        $productsForOrder = [];
        foreach ($products as $product) {
            $productsForOrder[$product->id] = ['price' => $product->price];
        }


        $order->products()->attach($productsForOrder);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return OrderResource::make($order);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $this->validate($request, ['product_id' => ['required', 'exists:products,id']]);

        if (! $order->productCanBeAdded($request->get('product_id'))) {
            return response()->json(['message' => 'Product can\'t be added to the order'], 422);
        }
        $product = Product::query()->findOrFail($request->get('product_id'));

        $order->products()->attach($product->id, ['price' => $product->price]);

        return response()->json($order->load('products'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json(['message' => 'Order is deleted successfully']);
    }

    public function pay(PaymentRequest $request, Order $order)
    {
        $customer = Customer::query()->findOrFail($request->get('customer_id'));

        $data = [
            'order_id' => $order->id,
            'customer_email' => $customer->email,
            'value' => $order->price,
            'customer' => $customer
        ];

        if (PaymentService::pay($order, $data)) {
            return response()->json(['message' => 'Payment is completed successfully']);
        } else {
            return response()->json(['message' => 'Payment failed!'], 400);
        }
    }
}
