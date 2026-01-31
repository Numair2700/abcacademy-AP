<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class EnrollmentService
{
    protected NotificationService $notificationService;


    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function createEnrollment(User $user, Course $course): Enrollment
    {
        // Validate user can enroll (includes duplicate check)
        $this->validateEnrollmentEligibility($user, $course);

        // Generate enrollment data
        $enrollmentData = $this->generateEnrollmentData($user, $course);

        // Create enrollment in transaction
        return DB::transaction(function () use ($user, $course, $enrollmentData) {
            $enrollment = Enrollment::create($enrollmentData);

            // Send notification
            $this->notificationService->sendNotification(
                $user,
                'enrollment_created',
                [
                    'course_title' => $course->title,
                    'btec_number' => $enrollment->btec_number,
                    'session' => $enrollment->session
                ]
            );
            return $enrollment;
        });
    }

    /**
     * Cancel an enrollment
     */
    public function cancelEnrollment(Enrollment $enrollment): bool
    {
        return DB::transaction(function () use ($enrollment) {
            $user = $enrollment->student->user;
            $course = $enrollment->course;
            
            $enrollment->delete();

            // Send cancellation notification
            $this->notificationService->sendNotification(
                $user,
                'enrollment_cancelled',
                [
                    'course_title' => $course->title,
                    'btec_number' => $enrollment->btec_number
                ]
            );

            return true;
        });
    }

    /**
     * Get enrollment statistics
     */
    public function getEnrollmentStatistics(array $filters = []): array
    {
        $query = Enrollment::query();

        if (isset($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (isset($filters['program_id'])) {
            $query->whereHas('course', function ($q) use ($filters) {
                $q->where('program_id', $filters['program_id']);
            });
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $total = $query->count();
        $thisMonth = $query->whereMonth('created_at', now()->month)->count();
        $thisYear = $query->whereYear('created_at', now()->year)->count();

        return [
            'total_enrollments' => $total,
            'this_month' => $thisMonth,
            'this_year' => $thisYear,
            'average_per_month' => $this->calculateAveragePerMonth($filters)
        ];
    }

    /**
     * Validate enrollment eligibility
     */
    private function validateEnrollmentEligibility(User $user, Course $course): void
    {
        if (!$user->student) {
            throw new \Exception('Only students can enroll in courses');
        }

        if (!$course->published) {
            throw new \Exception('Course is not available for enrollment');
        }

        if ($this->hasExistingEnrollment($user, $course)) {
            throw new \Exception('You are already enrolled in this course.');
        }

        // Additional validation logic can be added here
    }

    /**
     * Check for existing enrollment
     */
    private function hasExistingEnrollment(User $user, Course $course): bool
    {
        return Enrollment::where('student_id', $user->student->id)
            ->where('course_id', $course->id)
            ->exists();
    }

    /**
     * Generate enrollment data
     */
    private function generateEnrollmentData(User $user, Course $course): array
    {
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        $session = substr($currentYear, -2) . '-' . substr($nextYear, -2);
        
        $btecNumber = $course->code . '-' . $user->student->center_ref . '-' . time();

        return [
            'student_id' => $user->student->id,
            'course_id' => $course->id,
            'registration_date' => now()->toDateString(),
            'session' => $session,
            'btec_number' => $btecNumber,
        ];
    }

    /**
     * Calculate average enrollments per month
     */
    private function calculateAveragePerMonth(array $filters): float
    {
        $query = Enrollment::query();

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $enrollments = $query->get();
        $months = $enrollments->groupBy(function ($enrollment) {
            return $enrollment->created_at->format('Y-m');
        });

        if ($months->count() === 0) {
            return 0;
        }

        return round($enrollments->count() / $months->count(), 2);
    }

    /**
     * Update enrollment statistics in batches
     * 
     * Batch Processing with chunk() for Large Datasets:
     * 
     * BAD: Loads all 5000 enrollments into memory
     * $enrollments = Enrollment::all();
     * foreach ($enrollments as $enrollment) {
     *     $this->updateEnrollmentStats($enrollment);
     * }
     * 
     * GOOD: Processes in chunks of 100 (constant memory usage)
     * Enrollment::with(['student', 'course'])
     *     ->chunk(100, function ($enrollments) {
     *         foreach ($enrollments as $enrollment) {
     *             $this->updateEnrollmentStats($enrollment);
     *         }
     *     });
     * 
     * Result: Process 10,000+ records with constant ~5MB memory
     */
    public function updateEnrollmentStatisticsBatch(): array
    {
        $totalProcessed = 0;
        $batchSize = 100;
        $startTime = microtime(true);
        
        // Process enrollments in chunks to avoid memory issues
        Enrollment::with(['student', 'course'])
            ->chunk($batchSize, function ($enrollments) use (&$totalProcessed) {
                foreach ($enrollments as $enrollment) {
                    // Update statistics for this enrollment
                    $enrollmentCount = $enrollment->student->enrollments()->count();
                    
                    \Log::info('Enrollment statistics updated', [
                        'enrollment_id' => $enrollment->id,
                        'student_total_enrollments' => $enrollmentCount
                    ]);
                    
                    $totalProcessed++;
                }
            });
        
        $executionTime = microtime(true) - $startTime;
        
        return [
            'total_processed' => $totalProcessed,
            'execution_time' => round($executionTime, 2),
            'records_per_second' => round($totalProcessed / $executionTime, 2)
        ];
    }
}
// app/Services/EnrollmentService.php
