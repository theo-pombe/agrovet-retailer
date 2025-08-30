<?php

namespace App\Enums;

enum CustomerType: string
{
    case INDIVIDUAL = 'individual';
    case ORGANIZATION = 'organization';

    /**
     * Labels for dropdowns/forms.
     */
    public static function labels(): array
    {
        return [
            self::INDIVIDUAL->value => 'Individual',
            self::ORGANIZATION->value => 'Organization',
        ];
    }

    /**
     * Raw string values (for migrations / validation).
     */
    public static function values(): array
    {
        return array_map(fn($type) => $type->value, self::cases());
    }
}
