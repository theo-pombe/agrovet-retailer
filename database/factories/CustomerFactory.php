<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\CustomerType;
use App\Enums\Status;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement(CustomerType::values());

        return [
            'type' => $type,
            'full_name' => $type === CustomerType::INDIVIDUAL->value ? $this->faker->name() : null,
            'company_name' => $type === CustomerType::ORGANIZATION->value ? $this->faker->company() : null,
            'contact_person' => $type === CustomerType::ORGANIZATION->value ? $this->faker->name() : null,
            'phone' => $this->faker->unique()->phoneNumber(),
            'email' => $this->faker->optional()->safeEmail(),
            'address' => $this->faker->optional()->address(),
            'notes' => $this->faker->optional()->sentence(),
            'status' => $this->faker->randomElement(Status::customerStatuses())->value,
            'total_spent' => $this->faker->randomFloat(2, 0, 50000),
            'total_visits' => $this->faker->numberBetween(0, 50),
        ];
    }
}
