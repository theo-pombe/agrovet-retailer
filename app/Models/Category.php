<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "categories";

    protected $fillable = [
        'name',
        "description"
    ];

    public function getFormattedNameAttribute(): string
    {
        return ucwords($this->name); // Keeps raw 'name' intact
    }

    public function getFormattedDescriptionAttribute(): ?string
    {
        return $this->description ? ucfirst($this->description) : null;
    }

    public static function selectCategories(): array
    {
        $data = ['' => 'Select category'];

        self::query()->orderBy('name')->get()->each(function ($item) use (&$data) {
            $data[$item->id] = $item->formatted_name;
        });


        return $data;
    }
}
