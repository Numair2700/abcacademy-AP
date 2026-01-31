<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
   
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'center_ref' => fake()->unique()->regexify('[A-Z0-9]{8,10}'),
        ];
    }
}
