<?php

namespace App\Services\Reports;

use App\Models\Enrollment;
use App\Services\ReportGenerator;
use Illuminate\Support\Collection;

/**
 * EnrollmentReportGenerator - Concrete implementation of ReportGenerator
 * 
 * Demonstrates Strategy Pattern and Single Responsibility Principle.
 * Each report generator has a single, well-defined responsibility.
 */
class EnrollmentReportGenerator extends ReportGenerator
{
    /**
     * Collect enrollment data based on filters
     */
    public function collectData(): Collection
    {
        $query = Enrollment::with(['student.user', 'course.program']);

        // Apply filters without modifying the class structure
        if (isset($this->filters['student_id'])) {
            $query->where('student_id', $this->filters['student_id']);
        }
        
        if (isset($this->filters['course_id'])) {
            $query->where('course_id', $this->filters['course_id']);
        }

        if (isset($this->filters['program_id'])) {
            $query->whereHas('course', function ($q) {
                $q->where('program_id', $this->filters['program_id']);
            });
        }

        if (isset($this->filters['registration_date_from'])) {
            $query->where('registration_date', '>=', $this->filters['registration_date_from']);
        }

        if (isset($this->filters['registration_date_to'])) {
            $query->where('registration_date', '<=', $this->filters['registration_date_to']);
        }

        if (isset($this->filters['session'])) {
            $query->where('session', $this->filters['session']);
        }

        return $query->get();
    }

    /**
     * Process enrollment data into report format
     */
    public function processData(Collection $enrollments): Collection
    {
        return $enrollments->map(function ($enrollment) {
            return collect([
                'enrollment_id' => $enrollment->id,
                'btec_number' => $enrollment->btec_number,
                'student_name' => $enrollment->student->first_name . ' ' . $enrollment->student->last_name,
                'student_email' => $enrollment->student->user->email,
                'center_reference' => $enrollment->student->center_ref,
                'course_title' => $enrollment->course->title,
                'course_code' => $enrollment->course->code,
                'program_title' => $enrollment->course->program->title,
                'registration_date' => $enrollment->registration_date,
                'academic_session' => $enrollment->session,
                'course_price' => $enrollment->course->price,
                'course_language' => $enrollment->course->language,
                'enrollment_status' => 'Active'
            ]);
        });
    }
}
