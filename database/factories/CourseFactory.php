<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $languages = ['English', 'Arabic', 'French', 'Spanish'];
        
        return [
            'title' => fake()->words(4, true),
            'code' => fake()->unique()->regexify('[A-Z]{2,3}[0-9]{3}'),
            'language' => fake()->randomElement($languages),
            'price' => fake()->randomFloat(2, 100, 2000),
            'program_id' => \App\Models\Program::factory(),
            'published' => fake()->boolean(75), // 75% chance of being published
        ];
    }

    /**
     * Indicate that the course is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => true,
        ]);
    }

    /**
     * Indicate that the course is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => false,
        ]);
    }
}
