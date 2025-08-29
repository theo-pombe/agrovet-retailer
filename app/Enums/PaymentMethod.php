<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';
    case CREDIT_CARD = 'credit_card';
    case MOBILE_MONEY = 'mobile_money';
    case CHEQUE = 'cheque';
    case OTHER = 'other';

    /**
     * Returns all methods with human-readable labels for forms/dropdowns.
     */
    public static function options(): array
    {
        return [
            self::CASH->value => 'Cash',
            self::BANK_TRANSFER->value => 'Bank Transfer',
            self::CREDIT_CARD->value => 'Credit Card',
            self::MOBILE_MONEY->value => 'Mobile Money',
            self::CHEQUE->value => 'Cheque',
            self::OTHER->value => 'Other',
        ];
    }

    /**
     * Returns all method values as an array of strings.
     */
    public static function values(): array
    {
        return array_map(fn(PaymentMethod $method) => $method->value, self::cases());
    }
}
