<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_date',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'payment_method' => PaymentMethod::class,
    ];

    /*
    |----------------------------------------------------------------------
    | Relationships
    |----------------------------------------------------------------------
    */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /*
    |----------------------------------------------------------------------
    | Helper Methods
    |----------------------------------------------------------------------
    */

    /**
     * Update the invoice status after payment is recorded
     */
    public function updateInvoiceStatus(): void
    {
        $this->invoice->calculateStatus(); // Recalculate invoice status after payment
    }
}
