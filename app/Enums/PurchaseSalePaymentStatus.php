<?php

namespace App\Enums;

enum PurchaseSalePaymentStatus: string
{
    case UNPAID = 'unpaid';
    case PARTIAL = 'partial';
    case PAID = 'paid';

    /**
     * Returns all statuses with human-readable labels for forms/dropdowns.
     */
    public static function options(): array
    {
        return [
            self::UNPAID->value => 'Unpaid',
            self::PARTIAL->value => 'Partial',
            self::PAID->value => 'Paid',
        ];
    }

    /**
     * Returns an array of all status values as strings.
     */
    public static function values(): array
    {
        return array_map(fn(PurchaseSalePaymentStatus $status) => $status->value, self::cases());
    }
}
