<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
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
            'color' => $this->faker->colorName(),
            'default_next_followup_days' => 1,
            'need_followup' => 'yes',
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
