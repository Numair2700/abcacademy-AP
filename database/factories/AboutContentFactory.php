<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AboutContent>
 */
class AboutContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sections = ['mission', 'vision', 'history', 'values', 'achievements'];
        
        return [
            'section' => fake()->randomElement($sections),
            'title' => fake()->words(3, true),
            'content' => fake()->paragraphs(3, true),
            'order' => fake()->numberBetween(1, 10),
            'published' => true,
        ];
    }

    /**
     * Create content for a specific section.
     */
    public function section(string $section): static
    {
        return $this->state(fn (array $attributes) => [
            'section' => $section,
        ]);
    }
}
