<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Program;
use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get the sample student
        $student = User::where('email', 'student@abcacademy.test')->first();
        
        if ($student && $student->student) {
            // Get some courses to favorite and enroll in
            $courses = Course::take(3)->get();
            
            // Add some favorites
            foreach ($courses->take(2) as $course) {
                $student->favoriteCourses()->syncWithoutDetaching([$course->id]);
            }
            
            // Add some enrollments
            if ($courses->count() > 0) {
                Enrollment::create([
                    'student_id' => $student->student->id,
                    'course_id' => $courses->first()->id,
                    'registration_date' => '2024-01-15',
                    'session' => '24-25',
                    'btec_number' => 'BTEC001',
                ]);
            }
            
            if ($courses->count() > 1) {
                Enrollment::create([
                    'student_id' => $student->student->id,
                    'course_id' => $courses->skip(1)->first()->id,
                    'registration_date' => '2024-02-01',
                    'session' => '24-25',
                    'btec_number' => 'BTEC002',
                ]);
            }
        }
    }
}
