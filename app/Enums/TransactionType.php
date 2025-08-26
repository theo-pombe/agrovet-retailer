<?php

namespace App\Enums;

enum TransactionType: string
{
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case RETURN = 'return';
    case ADJUSTMENT = 'adjustment';

    /**
     * Return array of enum values for migrations.
     */
    public static function values(): array
    {
        return array_map(fn(self $type) => $type->value, self::cases());
    }

    /**
     * Return human-friendly labels for dropdowns or forms.
     */
    public static function options(): array
    {
        return [
            self::PURCHASE->value   => 'Purchase',
            self::SALE->value       => 'Sale',
            self::RETURN->value     => 'Return',
            self::ADJUSTMENT->value => 'Adjustment',
        ];
    }
}
