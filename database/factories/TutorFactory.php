<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tutor>
 */
class TutorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specializations = [
            'Computer Science',
            'Business Administration',
            'Engineering',
            'Mathematics',
            'English Literature',
            'Psychology',
            'Economics',
            'Physics',
            'Chemistry',
            'Biology',
            'History',
            'Art and Design'
        ];
        
        return [
            'name' => fake()->name(),
            'employee_id' => fake()->unique()->regexify('[A-Z]{2}[0-9]{4}'),
            'specialization' => fake()->randomElement($specializations),
            'qualifications' => fake()->sentence(),
            'experience_years' => fake()->numberBetween(1, 20),
            'bio' => fake()->paragraph(),
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }
}
