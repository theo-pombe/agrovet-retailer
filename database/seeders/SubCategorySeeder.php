<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;


class SubCategorySeeder extends Seeder
{
    public function run()
    {
        $subCategories = [
            'Animal Health' => [
                ['name' => 'Vaccines', 'description' => 'Livestock vaccines for disease prevention'],
                ['name' => 'Dewormers', 'description' => 'Internal parasite control'],
                ['name' => 'Antibiotics', 'description' => 'Treatment of bacterial infections'],
            ],
            'Veterinary Medicines' => [
                ['name' => 'Vitamins & Minerals', 'description' => 'Supplements for animal nutrition'],
                ['name' => 'Pain Relief & Anti-inflammatory', 'description' => 'Analgesics and NSAIDs'],
                ['name' => 'Topical Treatments', 'description' => 'Ointments, sprays, and dips'],
            ],
            'Pesticides & Herbicides' => [
                ['name' => 'Insecticides', 'description' => 'Crop pest control'],
                ['name' => 'Herbicides', 'description' => 'Weed management solutions'],
                ['name' => 'Fungicides', 'description' => 'Control of fungal diseases'],
            ],
            'Fertilizers' => [
                ['name' => 'Organic Fertilizers', 'description' => 'Compost, manure, biofertilizers'],
                ['name' => 'Inorganic Fertilizers', 'description' => 'NPK, urea, DAP, CAN'],
                ['name' => 'Foliar Fertilizers', 'description' => 'Liquid nutrient sprays'],
            ],
            'Seeds' => [
                ['name' => 'Cereal Seeds', 'description' => 'Maize, rice, wheat seeds'],
                ['name' => 'Vegetable Seeds', 'description' => 'Tomatoes, onions, cabbages'],
                ['name' => 'Pasture Seeds', 'description' => 'Napier grass, brachiaria, alfalfa'],
            ],
            'Animal Feeds & Supplements' => [
                ['name' => 'Dairy Feeds', 'description' => 'Concentrates for milk production'],
                ['name' => 'Poultry Feeds', 'description' => 'Starter, grower, layer feeds'],
                ['name' => 'Mineral Supplements', 'description' => 'Salt licks, premixes, additives'],
            ],
            'Farm Tools & Equipment' => [
                ['name' => 'Sprayers', 'description' => 'Knapsack and motorized sprayers'],
                ['name' => 'Protective Gear', 'description' => 'Gloves, masks, overalls'],
                ['name' => 'Hand Tools', 'description' => 'Hoes, pangas, slashers'],
            ],
        ];

        foreach ($subCategories as $categoryName => $items) {
            $category = Category::where('name', $categoryName)->first();

            if ($category) {
                foreach ($items as $subCategory) {
                    SubCategory::firstOrCreate(
                        [
                            'category_id' => $category->id,
                            'name' => $subCategory['name'],
                        ],
                        [
                            'description' => $subCategory['description'],
                        ]
                    );
                }
            }
        }
    }
}
