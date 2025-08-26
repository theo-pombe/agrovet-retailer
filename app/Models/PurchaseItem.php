<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
    {
        static::saving(function (PurchaseItem $item) {
            $item->subtotal = $item->quantity * $item->unit_price;
        });

        static::saved(function (PurchaseItem $item) {
            $item->purchase->calculateTotals();
        });

        static::deleted(function (PurchaseItem $item) {
            $item->purchase->calculateTotals();
        });
    }
}
