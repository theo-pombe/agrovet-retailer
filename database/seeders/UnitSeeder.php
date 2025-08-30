<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Piece', 'symbol' => 'pcs', 'is_fractional' => false],
            ['name' => 'Kilogram', 'symbol' => 'kg', 'is_fractional' => true],
            ['name' => 'Gram', 'symbol' => 'g', 'is_fractional' => true],
            ['name' => 'Liter', 'symbol' => 'ltr', 'is_fractional' => true],
            ['name' => 'Milliliter', 'symbol' => 'ml', 'is_fractional' => true],
            ['name' => 'Box', 'symbol' => 'box', 'is_fractional' => false],
            ['name' => 'Bag', 'symbol' => 'bag', 'is_fractional' => false],
            ['name' => 'Sachet', 'symbol' => 'sachet', 'is_fractional' => false],
            ['name' => 'Pack', 'symbol' => 'pack', 'is_fractional' => false],
            ['name' => 'Vial', 'symbol' => 'vial', 'is_fractional' => false],
            ['name' => 'Bottle', 'symbol' => 'bottle', 'is_fractional' => false],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['symbol' => $unit['symbol']], $unit);
        }
    }
}
