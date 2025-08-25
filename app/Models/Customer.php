<?php

namespace App\Models;

use App\Enums\CustomerType;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
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

    public function getDisplayNameAttribute(): string
    {
        if ($this->type === CustomerType::ORGANIZATION) {
            return $this->company_name
                . ($this->contact_person ? " ({$this->contact_person})" : '');
        }
        return $this->full_name;
    }

    public static function selectCustomers(): array
    {
        return self::query()
            ->whereIn('status', [
                Status::ACTIVE->value,
                Status::VIP->value,
            ])
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
