<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING = 0;
    const STATUS_PAYED = 1;
    const PRODUCTS_PIVOT_TABLE = 'product_order';

    protected $casts = ['payment_issued_at' => 'datetime'];

    protected $fillable = ['status', 'payment_issued_at', 'customer_id'];

    public static function validatePaymentMessage(array $json): bool
    {
        return array_key_exists('message', $json) && $json['message'] === 'Payment Successful';
    }

    public function productCanBeAdded(int $productId): bool
    {
        return !($this->products()->where('products.id', $productId)->exists() || $this->isPayed());
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, self::PRODUCTS_PIVOT_TABLE)->withPivot([
            'created_at',
            'price',
        ]);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isPayed(): bool
    {
        return $this->status === self::STATUS_PAYED;
    }

    public function scopePay($query, Customer $customer)
    {
        return $query->update([
            'status' => self::STATUS_PAYED,
            'payment_issued_at' => Carbon::now(),
            'customer_id' => $customer->id,
        ]);
    }

    public function price(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->products()->sum(self::PRODUCTS_PIVOT_TABLE . '.price')
        );
    }

    public function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->isPayed() ? 'Payed' : 'Not payed'
        );
    }
}
