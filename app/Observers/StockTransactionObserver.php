<?php

namespace App\Observers;

use App\Enums\TransactionType;
use App\Models\StockTransaction;

class StockTransactionObserver
{
    /**
     * Handle the StockTransaction "created" event.
     */
    public function created(StockTransaction $transaction): void
    {
        $this->updateStockLevel($transaction);
    }

    /**
     * Handle the StockTransaction "deleted" event (soft delete).
     */
    public function deleted(StockTransaction $transaction): void
    {
        $this->updateStockLevel($transaction, reverse: true);
    }

    /**
     * Handle the StockTransaction "updated" event.
     */
    public function updated(StockTransaction $transaction): void
    {
        $originalQty = $transaction->getOriginal('quantity');
        $newQty = $transaction->quantity;

        if ($originalQty === $newQty) {
            return;
        }

        $diff = $newQty - $originalQty;
        $this->updateStockLevel($transaction, quantityOverride: $diff);
    }

    /**
     * Update stock level depending on transaction type.
     *
     * @param StockTransaction $transaction
     * @param bool $reverse Flip operation (e.g., for deletion)
     * @param int|null $quantityOverride Use this quantity instead of transaction->quantity
     */
    protected function updateStockLevel(StockTransaction $transaction, bool $reverse = false, ?int $quantityOverride = null): void
    {
        $stock = $transaction->stock;
        if (!$stock) {
            return;
        }

        $qty = $quantityOverride ?? $transaction->quantity;
        $operation = null;

        switch ($transaction->type) {
            case TransactionType::PURCHASE:
            case TransactionType::RETURN:
                $operation = $reverse ? 'decrement' : 'increment';
                break;

            case TransactionType::SALE:
            case TransactionType::ADJUSTMENT:
                $operation = $reverse ? 'increment' : 'decrement';
                break;

            default:
                return; // No valid operation for this transaction type
        }

        if ($operation) {
            $stock->{$operation}('stock_level', $qty);
        }
    }
}
