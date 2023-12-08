<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->timestamp('payment_issued_at')->nullable();
            $table->timestamps();
        });

        Schema::create('product_order', function (Blueprint $table) {
            $table->foreignIdFor(Product::class);
            $table->foreignIdFor(Order::class);
            $table->timestamp('created_at');
            $table->float('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_order');
        Schema::dropIfExists('orders');
    }
};
