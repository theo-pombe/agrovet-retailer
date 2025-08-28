<?php

namespace App\Models;

use App\Enums\PurchaseSaleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_id',
        'order_date',
        'total_amount',
        'status',
        'expected_date',
        'notes',
    ];

    protected $casts = [
        'order_date'    => 'datetime',
        'expected_date' => 'datetime',
        'status'        => PurchaseSaleStatus::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */
    public function confirm(): void
    {
        if ($this->status !== PurchaseSaleStatus::PENDING) {
            return;
        }

        $this->update(['status' => PurchaseSaleStatus::COMPLETED]);
    }

    public function cancel(): void
    {
        $this->update(['status' => PurchaseSaleStatus::CANCELLED]);
    }

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::creating(function (CustomerOrder $order) {
            if (empty($order->order_number)) {
                // Example format: CORD-20250827-0001
                $prefix = 'CORD-' . now()->format('Ymd');
                $latestNumber = static::whereDate('created_at', today())
                    ->max('id') ?? 0;

                $order->order_number = $prefix . '-' . str_pad($latestNumber + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopePending($query)
    {
        return $query->where('status', PurchaseSaleStatus::PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', PurchaseSaleStatus::COMPLETED);
    }
}
