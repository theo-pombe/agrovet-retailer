<?php

namespace App\Enums;

enum CustomerType: string
{
    case INDIVIDUAL = 'individual';
    case ORGANIZATION = 'organization';

    public static function labels(): array
    {
        return [
            self::INDIVIDUAL->value => 'Individual',
            self::ORGANIZATION->value => 'Organization',
        ];
    }

    public static function values(): array
    {
        return array_map(fn($type) => $type->value, self::cases());
    }
}
