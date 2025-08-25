<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Animal Health', 'description' => 'Vaccines, dewormers, and veterinary medicines'],
            ['name' => 'Veterinary Medicines', 'description' => 'Antibiotics, vitamins, and other vet drugs'],
            ['name' => 'Pesticides & Herbicides', 'description' => 'Crop protection chemicals'],
            ['name' => 'Fertilizers', 'description' => 'Organic and inorganic fertilizers'],
            ['name' => 'Seeds', 'description' => 'Crop and pasture seeds'],
            ['name' => 'Animal Feeds & Supplements', 'description' => 'Concentrates, minerals, premixes'],
            ['name' => 'Farm Tools & Equipment', 'description' => 'Sprayers, protective gear, and small tools'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
