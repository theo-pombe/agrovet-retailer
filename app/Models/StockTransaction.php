<?php

namespace App\Models;

use App\Enums\Status;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransaction extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'stock_id',
        'type',
        'quantity',
        'purchase_price',
        'supplier_id',
        'retail_price',
        'customer_id',
        'transactionable_type',
        'transactionable_id',
        'transacted_date',
        'status',
    ];

    protected $casts = [
        'type' => TransactionType::class,
        'transacted_date' => 'date',
        'status' => Status::class,
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function transactionable()
    {
        return $this->morphTo();
    }

    protected static function booted()
    {
        static::created(function ($transaction) {
            $stock = $transaction->stock;
            if ($transaction->type === TransactionType::PURCHASE) {
                $stock->increment('stock_level', $transaction->quantity);
            } elseif ($transaction->type === TransactionType::SALE) {
                $stock->decrement('stock_level', $transaction->quantity);
            }
        });

        static::deleted(function ($transaction) {
            $stock = $transaction->stock;
            if ($transaction->type === TransactionType::PURCHASE) {
                $stock->decrement('stock_level', $transaction->quantity);
            } elseif ($transaction->type === TransactionType::SALE) {
                $stock->increment('stock_level', $transaction->quantity);
            }
        });
    }
}
