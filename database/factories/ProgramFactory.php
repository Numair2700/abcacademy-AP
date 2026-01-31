<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Program>
 */
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $qualificationLevels = ['Certificate', 'Diploma', 'Degree'];
        
        return [
            'title' => fake()->words(3, true),
            'description' => fake()->paragraphs(2, true),
            'qualification_level' => fake()->randomElement($qualificationLevels),
            'published' => fake()->boolean(80), // 80% chance of being published
        ];
    }

    /**
     * Indicate that the program is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => true,
        ]);
    }

    /**
     * Indicate that the program is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => false,
        ]);
    }
}
