<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Enums\Status;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create fixed suppliers
        $suppliers = [
            [
                'contact_person' => 'John Mwangi',
                'company_name' => 'VetCare Distributors Ltd',
                'phone' => '0712345678',
                'email' => 'info@vetcare.co.ke',
                'address' => 'Nairobi, Kenya',
                'notes' => 'Specialized in veterinary medicines',
                'status' => Status::ACTIVE->value,
            ],
            [
                'contact_person' => 'Mary Achieng',
                'company_name' => 'AgroSeed Supplies',
                'phone' => '0722334455',
                'email' => 'sales@agroseed.com',
                'address' => 'Kisumu, Kenya',
                'notes' => 'Seeds and fertilizers supplier',
                'status' => Status::ACTIVE->value,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(['phone' => $supplier['phone']], $supplier);
        }

        // 2. Create random suppliers using the factory
        Supplier::factory()->count(10)->create();
    }
}
