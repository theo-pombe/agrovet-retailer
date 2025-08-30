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

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope suppliers that are allowed to be selectable
     * (active or preferred).
     */
    public function scopeSelectable($query)
    {
        return $query->whereIn('status', Status::supplierStatusValues());
    }

    /**
     * Scope only active suppliers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', Status::ACTIVE->value);
    }

    /**
     * Scope only preferred suppliers.
     */
    public function scopePreferred($query)
    {
        return $query->where('status', Status::PREFERRED->value);
    }

    /*
    |--------------------------------------------------------------------------
    | Utility
    |--------------------------------------------------------------------------
    */

    public static function selectSuppliers(): array
    {
        return self::query()
            ->whereIn('status', Status::supplierStatusValues())
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
