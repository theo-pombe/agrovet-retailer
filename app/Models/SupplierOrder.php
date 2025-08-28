<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\TransactionStatus;
use Illuminate\Support\Facades\DB;

class SupplierOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'supplier_id',
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
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(SupplierOrderItem::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class, 'order_id'); // add order_id in purchases table
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

    public function convertToPurchase(bool $autoConfirm = false): Purchase
    {
        return DB::transaction(function () use ($autoConfirm) {
            // Create Purchase record
            $purchase = Purchase::create([
                'supplier_id'   => $this->supplier_id,
                'purchase_date' => now(),
                'total_amount'  => 0, // will calculate after adding items
                'status'        => TransactionStatus::PENDING,
                'payment_status' => PaymentStatus::UNPAID,
                'due_date'      => $this->due_date,
                'notes'         => $this->notes,
            ]);

            // Copy items
            foreach ($this->items as $orderItem) {
                $purchase->items()->create([
                    'product_id' => $orderItem->product_id,
                    'quantity'   => $orderItem->quantity,
                    'unit_price' => $orderItem->unit_price,
                    'subtotal'   => $orderItem->quantity * $orderItem->unit_price,
                ]);
            }

            // Recalculate totals
            $purchase->calculateTotals();

            // Optionally confirm immediately (trigger stock transactions)
            if ($autoConfirm) {
                $purchase->confirm();
            }

            return $purchase;
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::creating(function (SupplierOrder $order) {
            if (empty($order->order_number)) {
                // Example format: SORD-20250827-0001
                $prefix = 'SORD-' . now()->format('Ymd');
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
