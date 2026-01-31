<?php

namespace App\Observers;

use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

/**
 * EnrollmentObserver - Demonstrates Observer Pattern
 * 
 * This observer listens to enrollment events and performs actions
 * when enrollments are created, updated, or deleted.
 * 
 * Demonstrates:
 * - Observer Pattern
 * - Single Responsibility Principle
 * - Event-driven architecture
 */
class EnrollmentObserver
{
    /**
     * Handle the Enrollment "created" event
     */
    public function created(Enrollment $enrollment): void
    {
        // Log enrollment creation
        Log::info('New enrollment created', [
            'enrollment_id' => $enrollment->id,
            'student_id' => $enrollment->student_id,
            'course_id' => $enrollment->course_id,
            'btec_number' => $enrollment->btec_number,
            'session' => $enrollment->session
        ]);

        // Update student statistics
        $this->updateStudentStatistics($enrollment->student);
    }

    /**
     * Handle the Enrollment "updated" event
     */
    public function updated(Enrollment $enrollment): void
    {
        // Log enrollment updates
        Log::info('Enrollment updated', [
            'enrollment_id' => $enrollment->id,
            'changes' => $enrollment->getChanges()
        ]);
    }

    /**
     * Handle the Enrollment "deleted" event
     */
    public function deleted(Enrollment $enrollment): void
    {
        // Log enrollment deletion
        Log::info('Enrollment deleted', [
            'enrollment_id' => $enrollment->id,
            'student_id' => $enrollment->student_id,
            'course_id' => $enrollment->course_id
        ]);

        // Update student statistics if student still exists
        if ($enrollment->student) {
            $this->updateStudentStatistics($enrollment->student);
        }
    }

    /**
     * Update student enrollment statistics
     * Demonstrates encapsulation of business logic
     */
    private function updateStudentStatistics(Student $student): void
    {
        $enrollmentCount = $student->enrollments()->count();
        
        // This could be stored in a cache or separate statistics table
        // For now, we'll just log it
        Log::info('Student enrollment statistics updated', [
            'student_id' => $student->id,
            'total_enrollments' => $enrollmentCount
        ]);
    }
}

