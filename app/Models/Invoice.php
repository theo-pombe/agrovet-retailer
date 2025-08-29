<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;

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
        if ($this->amount_paid >= $this->total_amount) {
            $this->status = PaymentStatus::PAID;
        } elseif ($this->amount_paid > 0) {
            $this->status = PaymentStatus::PARTIAL;
        } else {
            $this->status = PaymentStatus::UNPAID;
        }

        $this->saveQuietly();
    }
}
