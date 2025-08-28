<?php

namespace App\Enums;

enum PaymentStatus: string
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
        return array_map(fn(PaymentStatus $status) => $status->value, self::cases());
    }
}
