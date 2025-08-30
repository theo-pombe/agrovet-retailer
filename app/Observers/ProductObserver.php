<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Validation\ValidationException;

class ProductObserver
{
    /**
     * Handle the Product "saving" event.
     */
    public function saving(Product $product): void
    {
        // 1. Ensure sub-category belongs to the selected category
        $this->validateSubCategory($product);

        // 2. Ensure product name is unique within category + subcategory
        $this->validateUniqueName($product);
    }

    /**
     * Ensure the subcategory belongs to the selected category.
     */
    protected function validateSubCategory(Product $product): void
    {
        if ($product->sub_category_id) {
            $subCategory = SubCategory::find($product->sub_category_id);

            if ($subCategory && $subCategory->category_id !== $product->category_id) {
                throw ValidationException::withMessages([
                    'sub_category_id' => 'SubCategory does not belong to the selected Category.'
                ]);
            }
        }
    }

    /**
     * Ensure the product name is unique within the category + subcategory.
     */
    protected function validateUniqueName(Product $product): void
    {
        $query = Product::where('name', $product->name)
            ->where('category_id', $product->category_id)
            ->where('sub_category_id', $product->sub_category_id);

        // Exclude current product if updating
        if ($product->exists) {
            $query->where('id', '!=', $product->id);
        }

        if ($query->query()->exists()) {
            throw ValidationException::withMessages([
                'name' => 'A product with this name already exists in the selected category and subcategory.'
            ]);
        }
    }
}
