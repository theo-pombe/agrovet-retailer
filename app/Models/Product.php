<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'unit_id',
        'category_id',
        'sub_category_id',
        'description',
        'selling_price',
        'purchasing_price',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function getFormattedNameAttribute(): string
    {
        return ucwords($this->name);
    }

    public function getFormattedDescriptionAttribute(): ?string
    {
        return $this->description ? ucfirst($this->description) : null;
    }

    public static function selectProducts($categoryId = null, $subcategoryId = null, $withSku = true): array
    {
        $data = [];

        $query = self::query()->orderBy('name');

        if ($categoryId)
            $query->where('category_id', $categoryId);

        if ($subcategoryId)
            $query->where('sub_category_id', $subcategoryId);

        $query->get()->each(function ($item) use (&$data, $withSku) {
            $label = $item->formatted_name;
            if ($withSku)
                $label .= " ({$item->sku})";

            $data[$item->id] = $label;
        });

        return $data;
    }
}
