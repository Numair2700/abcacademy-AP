<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
{

    public function definition(): array
    {
        $sessions = ['2024-2025', '2025-2026', '2026-2027'];
        
        return [
            'student_id' => \App\Models\Student::factory(),
            'course_id' => \App\Models\Course::factory(),
            'btec_number' => fake()->unique()->regexify('[A-Z]{2}[0-9]{6}'),
            'session' => fake()->randomElement($sessions),
            'registration_date' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
