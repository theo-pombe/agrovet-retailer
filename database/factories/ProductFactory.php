<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\SubCategory;


class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true); // e.g., "Super Maize Fertilizer"

        return [
            'name' => Str::title($name),
            'sku' => strtoupper(Str::random(8)),
            'description' => $this->faker->sentence(),
            'unit' => $this->faker->randomElement(['pcs', 'kg', 'ltr', 'box', 'bag']),
            'selling_price' => $this->faker->randomFloat(2, 1000, 20000),
            'purchasing_price' => $this->faker->randomFloat(2, 500, 15000),
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'subcategory_id' => SubCategory::inRandomOrder()->first()->id ?? null,
        ];
    }
}
