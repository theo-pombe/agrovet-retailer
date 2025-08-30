<?php

namespace App\Models;

use App\Enums\CustomerType;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'full_name',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'notes',
        'status',
        'total_spent',
        'total_visits',
    ];

    protected $casts = [
        'type' => CustomerType::class,
        'status' => Status::class,
    ];


    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Dynamically resolve customer display name.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->type === CustomerType::ORGANIZATION) {
            return $this->company_name
                . ($this->contact_person ? " ({$this->contact_person})" : '');
        }
        return $this->full_name;
    }


    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeSelectable($query)
    {
        return $query->whereIn('status', Status::customerStatusValues());
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ACTIVE->value);
    }

    public function scopeVip($query)
    {
        return $query->where('status', Status::VIP->value);
    }


    /*
    |--------------------------------------------------------------------------
    | Utility
    |--------------------------------------------------------------------------
    */
    public static function selectCustomers(): array
    {
        return self::query()
            ->whereIn('status', Status::customerStatusValues())
            ->orderBy('type')
            ->orderBy('full_name')
            ->orderBy('company_name')
            ->get()
            ->map(fn($customer) => [
                'value' => $customer->id,
                'label' => $customer->display_name
                    . ($customer->status === Status::VIP ? ' (VIP)' : ''),
            ])
            ->toArray();
    }
}
