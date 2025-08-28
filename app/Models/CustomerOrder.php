<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
        'status'        => TransactionStatus::class,
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

    public function items()
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    public function sale()
    {
        return $this->hasOne(Sale::class, 'order_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */
    public function calculateTotals(): void
    {
        $total = $this->items()->sum('subtotal');
        $this->update(['total_amount' => $total]);
    }

    public function confirm(): void
    {
        if ($this->status !== TransactionStatus::PENDING) {
            return;
        }

        $this->update(['status' => TransactionStatus::COMPLETED]);
    }

    public function cancel(): void
    {
        $this->update(['status' => TransactionStatus::CANCELLED]);
    }

    public function convertToSale(bool $autoConfirm = false): Sale
    {
        return DB::transaction(function () use ($autoConfirm) {
            // Create Sale record
            $sale = Sale::create([
                'customer_id'   => $this->customer_id,
                'sale_date'     => now(),
                'total_amount'  => 0, // will calculate after adding items
                'status'        => TransactionStatus::PENDING,
                'payment_status' => PaymentStatus::UNPAID,
                'due_date'      => $this->due_date,
                'notes'         => $this->notes,
            ]);

            // Copy items
            foreach ($this->items as $orderItem) {
                $sale->items()->create([
                    'product_id' => $orderItem->product_id,
                    'quantity'   => $orderItem->quantity,
                    'unit_price' => $orderItem->unit_price,
                    'subtotal'   => $orderItem->quantity * $orderItem->unit_price,
                ]);
            }

            // Recalculate totals
            $sale->calculateTotals();

            // Optionally confirm immediately (trigger stock transactions)
            if ($autoConfirm) {
                $sale->confirm();
            }

            return $sale;
        });
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
        return $query->where('status', TransactionStatus::PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', TransactionStatus::COMPLETED);
    }
}
