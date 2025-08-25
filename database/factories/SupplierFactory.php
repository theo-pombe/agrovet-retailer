<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\Status;

class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'contact_person' => $this->faker->name(),
            'company_name' => $this->faker->company(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->address(),
            'notes' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(Status::supplierStatuses())->value,
        ];
    }
}
