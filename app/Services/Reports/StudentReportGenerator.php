<?php

namespace App\Services\Reports;

use App\Models\Enrollment;
use App\Models\Student;
use App\Services\ReportGenerator;
use Illuminate\Support\Collection;

/**
 * StudentReportGenerator - Concrete implementation of ReportGenerator
 * 
 * Demonstrates Strategy Pattern, Open/Closed Principle, and Single Responsibility Principle.
 * This class is open for extension but closed for modification.
 */
class StudentReportGenerator extends ReportGenerator
{
    /**
     * Collect student data based on filters
     * Demonstrates data abstraction and encapsulation
     */
    public function collectData(): Collection
    {
        $query = Student::with(['user', 'enrollments.course.program']);

        // Apply filters - demonstrates flexibility without modifying the class
        if (isset($this->filters['student_id'])) {
            $query->where('id', $this->filters['student_id']);
        }
        
        if (isset($this->filters['program_id'])) {
            $query->whereHas('enrollments.course', function ($q) {
                $q->where('program_id', $this->filters['program_id']);
            });
        }

        if (isset($this->filters['enrollment_date_from'])) {
            $query->whereHas('enrollments', function ($q) {
                $q->where('registration_date', '>=', $this->filters['enrollment_date_from']);
            });
        }

        if (isset($this->filters['enrollment_date_to'])) {
            $query->whereHas('enrollments', function ($q) {
                $q->where('registration_date', '<=', $this->filters['enrollment_date_to']);
            });
        }

        return $query->get();
    }

    /**
     * Process student data into report format
     * Demonstrates data transformation and business logic encapsulation
     */
    public function processData(Collection $students): Collection
    {
        return $students->map(function ($student) {
            $enrollments = $student->enrollments;
            $totalEnrollments = $enrollments->count();
            $activeEnrollments = $enrollments->where('created_at', '>=', now()->subYear())->count();
            
            // Get enrolled programs
            $enrolledPrograms = $enrollments->pluck('course.program.title')->unique()->filter()->implode(', ');
            if (empty($enrolledPrograms)) {
                $enrolledPrograms = 'None';
            }

            return collect([
                'student_id' => $student->id,
                'center_reference' => $student->center_ref,
                'full_name' => $student->first_name . ' ' . $student->last_name,
                'email' => $student->user->email,
                'registration_date' => $student->user->created_at->format('Y-m-d'),
                'total_enrollments' => $totalEnrollments,
                'active_enrollments' => $activeEnrollments,
                'enrolled_programs' => $enrolledPrograms,
                'last_enrollment_date' => $enrollments->max('registration_date') ?? 'Never',
                'status' => $activeEnrollments > 0 ? 'Active' : 'Inactive'
            ]);
        });
    }
}
