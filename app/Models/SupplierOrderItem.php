<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierOrderItem extends Model
{
    protected $fillable = [
        'supplier_order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    public function order()
    {
        return $this->belongsTo(SupplierOrder::class, 'supplier_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->subtotal = $item->quantity * $item->price;
        });
    }
}
