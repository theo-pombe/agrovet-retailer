<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'stock_level',
        're_order_level',
        'status',
    ];

    protected $casts = [
        'status' => Status::class,
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function isBelowReorderLevel(): bool
    {
        return $this->stock_level <= $this->re_order_level;
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ACTIVE);
    }
}
