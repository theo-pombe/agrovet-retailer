<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sub_categories';

    protected $fillable = [
        'category_id',
        'name',
        'description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getFormattedNameAttribute(): string
    {
        return ucwords($this->name); // Keeps raw 'name' intact
    }

    public function getFormattedDescriptionAttribute(): ?string
    {
        return $this->description ? ucfirst($this->description) : null;
    }

    public static function selectSubCategories($categoryId = null): array
    {
        $data = [];

        $query = self::query()->orderBy('name');
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $query->get()->each(function ($item) use (&$data) {
            $data[$item->id] = $item->formatted_name;
        });

        return $data;
    }
}
