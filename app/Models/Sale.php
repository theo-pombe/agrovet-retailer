<?php

namespace App\Models;

use App\Enums\PurchaseSalePaymentStatus;
use App\Enums\PurchaseSaleStatus;
use App\Enums\TransactionType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'sale_date',
        'total_amount',
        'status',
        'payment_status',
        'amount_paid',
        'due_date',
        'notes',
    ];

    protected $casts = [
        'sale_date'      => 'datetime',
        'due_date'       => 'datetime',
        'status'         => PurchaseSaleStatus::class,
        'payment_status' => PurchaseSalePaymentStatus::class,
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
        return $this->hasMany(SaleItem::class);
    }

    public function stockTransactions()
    {
        return $this->morphMany(StockTransaction::class, 'transactionable');
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    /**
     * Check if sale is pending.
     */
    public function isPending(): bool
    {
        return $this->status === PurchaseSaleStatus::PENDING;
    }

    /**
     * Check if sale is fully paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === PurchaseSalePaymentStatus::PAID;
    }

    /**
     * Calculate balance due.
     */
    public function getBalanceDueAttribute(): float
    {
        return max(0, $this->total_amount - $this->amount_paid);
    }

    /**
     * Check if payment is overdue.
     */
    public function isPaymentOverdue(): bool
    {
        if (!$this->due_date instanceof Carbon) {
            return false;
        }

        return $this->due_date->isPast() && !$this->isPaid();
    }

    /**
     * Recalculate and update total amount from items.
     */
    public function calculateTotals(): void
    {
        $total = $this->items()->sum('subtotal');
        $this->update(['total_amount' => $total]);
    }

    /**
     * Increment payment and update payment status.
     */
    public function markAsPaid(float $amount): void
    {
        $this->increment('amount_paid', $amount);

        $this->refresh(); // Ensure fresh values after increment

        if ($this->amount_paid >= $this->total_amount) {
            $this->update(['payment_status' => PurchaseSalePaymentStatus::PAID]);
        } elseif ($this->amount_paid > 0) {
            $this->update(['payment_status' => PurchaseSalePaymentStatus::PARTIAL]);
        }
    }

    /**
     * Confirm the sale and generate stock transactions.
     */
    public function confirm(): void
    {
        if ($this->status === PurchaseSaleStatus::COMPLETED) {
            return; // already confirmed
        }

        $this->update(['status' => PurchaseSaleStatus::COMPLETED]);

        foreach ($this->items as $item) {
            if ($item->product && $item->product->stock) {
                $this->stockTransactions()->create([
                    'stock_id' => $item->product->stock->id,
                    'quantity' => $item->quantity,
                    'type'     => TransactionType::SALE,
                ]);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
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

    public function scopeOverdue($query)
    {
        return $query
            ->where('due_date', '<', now())
            ->where('payment_status', '!=', PurchaseSalePaymentStatus::PAID);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', PurchaseSalePaymentStatus::PAID);
    }
}
