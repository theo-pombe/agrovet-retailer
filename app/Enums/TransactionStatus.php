<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING = "pending";
    case ORDERED   = "ordered";
    case COMPLETED = "completed";
    case CANCELLED = "cancelled";

    /**
     * Returns all statuses with human-readable labels for forms/dropdowns.
     */
    public static function options(): array
    {
        return [
            self::PENDING->value => 'Pending',
            self::ORDERED->value => 'Ordered',
            self::COMPLETED->value => 'Completed',
            self::CANCELLED->value => 'Cancelled',
        ];
    }

    /**
     * Returns an array of all status values as strings.
     */
    public static function values(): array
    {
        return array_map(fn(TransactionStatus $status) => $status->value, self::cases());
    }
}
