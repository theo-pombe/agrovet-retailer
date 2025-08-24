<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contact_person',
        'company_name',
        'phone',
        'email',
        'address',
        'notes',
        'status'
    ];

    protected $casts = [
        'status' => Status::class,
    ];

    public static function selectSuppliers(): array
    {
        return self::query()
            ->whereIn('status', [Status::ACTIVE->value, Status::PREFERRED->value])
            ->orderBy('contact_person')
            ->orderBy('company_name')
            ->get()
            ->map(fn($supplier) => [
                'value' => $supplier->id,
                'label' => ($supplier->company_name ?: $supplier->contact_person)
                    . ($supplier->status === Status::PREFERRED ? ' (Preferred)' : ''),
            ])
            ->toArray();
    }
}
