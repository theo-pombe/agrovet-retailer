<?php

namespace App\Enums;

enum Status: string
{
    // Common statuses
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';

        // Special statuses
    case VIP = 'vip';              // For premium customers
    case PREFERRED = 'preferred';  // For trusted suppliers

    /**
     * Returns all statuses with human-readable labels for forms/dropdowns.
     */
    public static function options(): array
    {
        return [
            self::ACTIVE->value => 'Active',
            self::INACTIVE->value => 'Inactive',
            self::SUSPENDED->value => 'Suspended',
            self::VIP->value => 'VIP',
            self::PREFERRED->value => 'Preferred',
        ];
    }

    /**
     * Get the human-readable label for the current enum instance.
     */
    public function label(): string
    {
        return self::options()[$this->value];
    }

    /**
     * General statuses used across the system.
     */
    public static function generalStatuses(): array
    {
        return [
            self::ACTIVE,
            self::INACTIVE,
            self::SUSPENDED,
        ];
    }

    /**
     * Statuses applicable to customers.
     */
    public static function customerStatuses(): array
    {
        return [
            ...self::generalStatuses(),
            self::VIP,
        ];
    }

    public static function customerStatusValues(): array
    {
        return array_map(fn(Status $s) => $s->value, self::customerStatuses());
    }

    /**
     * Statuses applicable to suppliers.
     */
    public static function supplierStatuses(): array
    {
        return [
            ...self::generalStatuses(),
            self::PREFERRED,
        ];
    }

    public static function supplierStatusValues(): array
    {
        return array_map(fn(Status $s) => $s->value, self::supplierStatuses());
    }

    /**
     * Returns an array of all status values as strings.
     */
    public static function values(): array
    {
        return array_map(fn(Status $status) => $status->value, self::cases());
    }
}
