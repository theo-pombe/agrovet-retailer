<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    protected $fillable = [
        'invoiceable_type',
        'invoiceable_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'total_amount',
        'amount_paid',
        'status',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'due_date' => 'datetime',
        'status' => PaymentStatus::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function invoiceable()
    {
        return $this->morphTo();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Boot Logic
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_date)) {
                $invoice->invoice_date = now();
            }

            if (empty($invoice->invoice_number)) {
                $date = now()->format('Ymd');
                $count = static::query()->whereDate('created_at', now())->count() + 1;
                $invoice->invoice_number = "INV-{$date}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function calculateStatus(): void
    {
        $newStatus = $this->status;

        if ($this->amount_paid >= $this->total_amount) {
            $newStatus = PaymentStatus::PAID;
        } elseif ($this->amount_paid > 0) {
            $newStatus = PaymentStatus::PARTIAL;
        } else {
            $newStatus = PaymentStatus::UNPAID;
        }

        // Only update the status if it has changed
        if ($this->status !== $newStatus) {
            $this->status = $newStatus;
            $this->saveQuietly();
        }
    }

    public function recordPayment(float $amount, ?string $method = null, ?string $notes = null): Payment
    {
        return DB::transaction(function () use ($amount, $method, $notes) {
            $payment = $this->payments()->create([
                'amount' => $amount,
                'payment_date' => now(),
                'payment_method' => $method,
                'notes' => $notes,
            ]);

            $this->amount_paid += $amount;
            $this->calculateStatus();

            return $payment;
        });
    }
}
