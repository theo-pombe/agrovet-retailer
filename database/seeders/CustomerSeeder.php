<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Enums\CustomerType;
use App\Enums\Status;

class CustomerSeeder extends Seeder
{
    // 1. Create fixed customers
    public function run(): void
    {
        $customers = [
            [
                'type' => CustomerType::INDIVIDUAL->value,
                'full_name' => 'Juma Mwinyi',
                'phone' => '0711223344',
                'email' => 'juma@example.com',
                'status' => Status::ACTIVE->value,
            ],
            [
                'type' => CustomerType::ORGANIZATION->value,
                'company_name' => 'AgroVet Supplies Ltd',
                'contact_person' => 'Mary Achieng',
                'phone' => '0722334455',
                'email' => 'info@agrovet.com',
                'status' => Status::ACTIVE->value,
            ],
        ];

        foreach ($customers as $c) {
            Customer::firstOrCreate(['phone' => $c['phone']], $c);
        }

        // 2. Create random customers using the factory
        Customer::factory()->count(10)->create();
    }
}
