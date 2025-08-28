<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\PurchaseSalePaymentStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Carbon\Carbon;

class Purchase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'purchase_date',
        'total_amount',
        'status',
        'payment_status',
        'amount_paid',
        'due_date',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
        'due_date'      => 'datetime',
        'status'        => TransactionStatus::class,
        'payment_status' => PurchaseSalePaymentStatus::class,
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
        return $this->hasMany(PurchaseItem::class);
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
     * Check if purchase is pending.
     */
    public function isPending(): bool
    {
        return $this->status === TransactionStatus::PENDING;
    }

    /**
     * Check if purchase is fully paid.
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
        return $this->due_date instanceof Carbon
            && $this->due_date->isPast()
            && !$this->isPaid();
    }

    public function calculateTotals(): void
    {
        $total = $this->items()->sum('subtotal');
        $this->update(['total_amount' => $total]);
    }

    public function markAsPaid(float $amount): void
    {
        $this->increment('amount_paid', $amount);

        if ($this->amount_paid >= $this->total_amount) {
            $this->update(['payment_status' => PurchaseSalePaymentStatus::PAID]);
        } elseif ($this->amount_paid > 0) {
            $this->update(['payment_status' => PurchaseSalePaymentStatus::PARTIAL]);
        }
    }

    /**
     * Confirm the purchase and generate stock transactions.
     */
    public function confirm(): void
    {
        if ($this->status === TransactionStatus::COMPLETED) {
            return; // already confirmed
        }

        $this->update(['status' => TransactionStatus::COMPLETED]);

        foreach ($this->items()->with('product.stock')->get() as $item) {
            if ($item->product && $item->product->stock) {
                $this->stockTransactions()->create([
                    'stock_id' => $item->product->stock->id,
                    'quantity' => $item->quantity,
                    'type'     => TransactionType::PURCHASE,
                ]);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to get only pending purchases.
     */
    public function scopePending($query)
    {
        return $query->where('status', TransactionStatus::PENDING);
    }

    /**
     * Scope to get only completed purchases.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', TransactionStatus::COMPLETED);
    }

    /**
     * Scope to get only overdue purchases (due_date passed & not fully paid).
     */
    public function scopeOverdue($query)
    {
        return $query
            ->where('due_date', '<', now())
            ->where('payment_status', '!=', PurchaseSalePaymentStatus::PAID);
    }

    /**
     * Scope to get only fully paid purchases.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', PurchaseSalePaymentStatus::PAID);
    }
}
