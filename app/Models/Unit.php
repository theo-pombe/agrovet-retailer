<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Unit extends Model
{
    protected $fillable = [
        "name",
        "symbol",
        "is_fractional",
    ];

    /**
     * Units that should not be pluralized in the agrovet domain.
     */
    protected static array $unpluralizables = [
        'kg',
        'g',
        'mg',
        'l',
        'ml',
        'ton',
        'oz',
        'lb',
        'm',
        'cm',
        'mm',
        'sqm',
        'ha',
        'doz'
    ];

    /**
     * Returns the name in proper case.
     */
    public function getFormattedNameAttribute(): string
    {
        return ucwords($this->name);
    }

    /**
     * Returns the symbol in lowercase.
     */
    public function getFormattedSymbolAttribute(): string
    {
        return strtolower($this->symbol);
    }

    /**
     * Returns the plural form of the symbol, respecting unpluralizable units.
     */
    public function getPluralSymbolAttribute(): string
    {
        return in_array($this->formatted_symbol, self::$unpluralizables, true)
            ? $this->formatted_symbol
            : Str::plural($this->formatted_symbol);
    }

    /**
     * Returns a display name combining the formatted name and symbol.
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->formatted_name} ({$this->formatted_symbol})";
    }

    public function getFractionalTextAttribute(): string
    {
        return $this->is_fractional ? 'Yes' : 'No';
    }

    /**
     * Returns an array suitable for dropdowns or selects.
     */
    public static function selectUnits(): array
    {
        return self::query()
            ->orderBy('name')
            ->get() // fetch models so accessors work
            ->pluck('display_name', 'id')
            ->toArray();
    }

    /**
     * Returns a formatted quantity string for business use.
     * Example: 1 pc, 5 pcs, 2 kg, 500 ml
     */
    public function formatQuantity(int|float $amount): string
    {
        $symbol = ($amount == 1) ? $this->formatted_symbol : $this->plural_symbol;
        return "{$amount} {$symbol}";
    }
}
