<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // 1. Create fixed products
        $products = [
            // Animal Health
            ['name' => 'Oxyvet 10% Injection', 'sku' => 'OXV-1001', 'unit' => 'vial', 'selling_price' => 2500, 'purchasing_price' => 1800, 'category' => 'Animal Health', 'subcategory' => 'Antibiotics'],
            ['name' => 'Nilzan Dewormer', 'sku' => 'NLZ-2002', 'unit' => 'bottle', 'selling_price' => 1200, 'purchasing_price' => 900, 'category' => 'Animal Health', 'subcategory' => 'Dewormers'],

            // Seeds
            ['name' => 'Hybrid Maize Seed H614', 'sku' => 'H614-3001', 'unit' => 'kg', 'selling_price' => 4500, 'purchasing_price' => 3800, 'category' => 'Seeds', 'subcategory' => 'Cereal Seeds'],
            ['name' => 'Kale Sukuma Wiki Seed', 'sku' => 'KSK-3002', 'unit' => 'g', 'selling_price' => 200, 'purchasing_price' => 150, 'category' => 'Seeds', 'subcategory' => 'Vegetable Seeds'],

            // Pesticides
            ['name' => 'Roundup Herbicide 1L', 'sku' => 'RDP-4001', 'unit' => 'ltr', 'selling_price' => 1800, 'purchasing_price' => 1400, 'category' => 'Pesticides & Herbicides', 'subcategory' => 'Herbicides'],
        ];

        foreach ($products as $p) {
            $category = Category::where('name', $p['category'])->first();
            $subcategory = SubCategory::where('name', $p['subcategory'])->first();

            Product::firstOrCreate(
                ['sku' => $p['sku']],
                [
                    'name' => $p['name'],
                    'description' => $p['name'],
                    'unit' => $p['unit'],
                    'selling_price' => $p['selling_price'],
                    'purchasing_price' => $p['purchasing_price'],
                    'category_id' => $category?->id,
                    'subcategory_id' => $subcategory?->id,
                ]
            );
        }

        // 2. Create random products using the factory
        Product::factory()->count(10)->create();
    }
}
