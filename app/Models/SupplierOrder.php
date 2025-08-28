<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\TransactionStatus;

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
