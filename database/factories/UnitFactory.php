<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $credits = [15, 60, 120];
        
        return [
            'title' => fake()->words(5, true),
            'btec_code' => fake()->unique()->regexify('[A-Z]{2}[0-9]{4}'),
            'credit' => fake()->randomElement($credits),
            'published' => fake()->boolean(85), // 85% chance of being published
        ];
    }

    /**
     * Indicate that the unit is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => true,
        ]);
    }

    /**
     * Indicate that the unit is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => false,
        ]);
    }
}
