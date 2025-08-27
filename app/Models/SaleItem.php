<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
    {
        static::saving(function (SaleItem $item) {
            $item->subtotal = $item->quantity * $item->unit_price;
        });

        static::saved(function (SaleItem $item) {
            $item->sale->calculateTotals();
        });

        static::deleted(function (SaleItem $item) {
            $item->sale->calculateTotals();
        });
    }
}
