<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'national' => $this->faker->uuid(),
            'member_code' => $this->faker->phoneNumber(),
            'status_id' => 1,
            'sales_by_id' => 1,
            'gender' => 'male',
            'type' => 'lead',
            'source_id' => 1,
            'dob' => '2021-01-01',
            'notes' => $this->faker->text(),
            'address_id' => rand(1,3)
        ];
    }
}
