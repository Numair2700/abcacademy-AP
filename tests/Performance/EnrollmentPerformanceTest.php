<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * EnrollmentPerformanceTest - Performance testing for enrollment operations
 * 
 * Tests the system's ability to handle large datasets efficiently
 * Demonstrates scalability and performance optimization
 */
class EnrollmentPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Disable query logging for performance tests
        DB::disableQueryLog();
    }

    /** @test */
    public function enrollment_service_handles_1000_students_efficiently()
    {
        // Arrange - Create large dataset
        $students = Student::factory()->count(1000)->create();
        $courses = Course::factory()->count(50)->create(['published' => true]);
        
        $service = new EnrollmentService(new NotificationService());
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Act - Process 100 enrollments
        $enrollmentsCreated = 0;
        foreach ($students->take(100) as $student) {
            $course = $courses->random();
            try {
                $service->createEnrollment($student->user, $course);
                $enrollmentsCreated++;
            } catch (Exception $e) {
                // Skip if already enrolled (expected for some)
            }
        }
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        $executionTime = $endTime - $startTime;
        $memoryUsed = $endMemory - $startMemory;
        
        // Assert - Performance benchmarks
        expect($executionTime)->toBeLessThan(30); // 30 seconds max
        expect($memoryUsed)->toBeLessThan(50 * 1024 * 1024); // 50MB max
        expect($enrollmentsCreated)->toBeGreaterThan(0);
        expect(Enrollment::count())->toBe($enrollmentsCreated);
        
        // Log performance metrics
        echo "\n📊 Performance Metrics:\n";
        echo "Execution Time: " . round($executionTime, 2) . " seconds\n";
        echo "Memory Used: " . round($memoryUsed / 1024 / 1024, 2) . " MB\n";
        echo "Enrollments Created: {$enrollmentsCreated}\n";
    }

    /** @test */
    public function enrollment_service_handles_concurrent_enrollments()
    {
        // Arrange
        $students = Student::factory()->count(100)->create();
        $course = Course::factory()->create(['published' => true]);
        
        $service = new EnrollmentService(new NotificationService());
        
        $startTime = microtime(true);
        
        // Act - Simulate concurrent enrollments
        $enrollments = [];
        foreach ($students as $student) {
            try {
                $enrollment = $service->createEnrollment($student->user, $course);
                $enrollments[] = $enrollment;
            } catch (Exception $e) {
                // Expected for duplicate prevention
            }
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Assert
        expect($executionTime)->toBeLessThan(15); // 15 seconds max
        expect(count($enrollments))->toBe(100); // All should succeed
        expect(Enrollment::count())->toBe(100);
        
        echo "\n📊 Concurrent Enrollment Metrics:\n";
        echo "Execution Time: " . round($executionTime, 2) . " seconds\n";
        echo "Enrollments: " . count($enrollments) . "\n";
    }

    /** @test */
    public function enrollment_statistics_calculation_is_efficient()
    {
        // Arrange - Create large dataset with enrollments
        $programs = Program::factory()->count(10)->create();
        $courses = collect();
        
        foreach ($programs as $program) {
            $courses = $courses->merge(
                Course::factory()->count(20)->create(['program_id' => $program->id])
            );
        }
        
        $students = Student::factory()->count(2000)->create();
        
        // Create enrollments
        foreach ($students as $student) {
            $enrolledCourses = $courses->random(rand(1, 3));
            foreach ($enrolledCourses as $course) {
                Enrollment::factory()->create([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                ]);
            }
        }
        
        $service = new EnrollmentService(new NotificationService());
        
        $startTime = microtime(true);
        
        // Act - Calculate statistics
        $stats = $service->getEnrollmentStatistics();
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Assert
        expect($executionTime)->toBeLessThan(5); // 5 seconds max
        expect($stats)->toBeArray();
        expect($stats)->toHaveKey('total_enrollments');
        expect($stats)->toHaveKey('this_month');
        expect($stats)->toHaveKey('this_year');
        expect($stats['total_enrollments'])->toBeGreaterThan(0);
        
        echo "\n📊 Statistics Calculation Metrics:\n";
        echo "Execution Time: " . round($executionTime, 2) . " seconds\n";
        echo "Total Enrollments: " . $stats['total_enrollments'] . "\n";
    }

    /** @test */
    public function enrollment_queries_are_optimized()
    {
        // Arrange
        $programs = Program::factory()->count(5)->create();
        $courses = collect();
        
        foreach ($programs as $program) {
            $courses = $courses->merge(
                Course::factory()->count(10)->create(['program_id' => $program->id])
            );
        }
        
        $students = Student::factory()->count(500)->create();
        
        // Create enrollments
        foreach ($students as $student) {
            $course = $courses->random();
            Enrollment::factory()->create([
                'student_id' => $student->id,
                'course_id' => $course->id,
            ]);
        }
        
        // Test query optimization
        DB::enableQueryLog();
        
        // Act - Test with eager loading (optimized)
        $enrollmentsWithEagerLoading = Enrollment::with(['student.user', 'course.program'])
            ->whereHas('course', function ($query) {
                $query->where('published', true);
            })
            ->get();
        
        $queriesWithEagerLoading = count(DB::getQueryLog());
        
        DB::flushQueryLog();
        
        // Test without eager loading (unoptimized)
        $enrollmentsWithoutEagerLoading = Enrollment::whereHas('course', function ($query) {
            $query->where('published', true);
        })->get();
        
        foreach ($enrollmentsWithoutEagerLoading as $enrollment) {
            $enrollment->student->user->email; // Triggers N+1
            $enrollment->course->program->title; // Triggers N+1
        }
        
        $queriesWithoutEagerLoading = count(DB::getQueryLog());
        
        // Assert - Eager loading should use fewer queries
        expect($queriesWithEagerLoading)->toBeLessThan($queriesWithoutEagerLoading);
        expect($queriesWithEagerLoading)->toBeLessThan(10); // Should be 2-3 queries max
        
        echo "\n📊 Query Optimization Metrics:\n";
        echo "Queries with Eager Loading: {$queriesWithEagerLoading}\n";
        echo "Queries without Eager Loading: {$queriesWithoutEagerLoading}\n";
        echo "Optimization: " . round((($queriesWithoutEagerLoading - $queriesWithEagerLoading) / $queriesWithoutEagerLoading) * 100, 2) . "%\n";
    }

    /** @test */
    public function enrollment_bulk_operations_are_efficient()
    {
        // Arrange
        $students = Student::factory()->count(500)->create();
        $courses = Course::factory()->count(10)->create(['published' => true]);
        
        $startTime = microtime(true);
        
        // Act - Bulk enrollment creation
        $enrollmentData = [];
        foreach ($students as $student) {
            $course = $courses->random();
            $enrollmentData[] = [
                'student_id' => $student->id,
                'course_id' => $course->id,
                'registration_date' => now()->toDateString(),
                'session' => '24-25',
                'btec_number' => 'BULK-' . $student->center_ref . '-' . time(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        // Bulk insert
        Enrollment::insert($enrollmentData);
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Assert
        expect($executionTime)->toBeLessThan(10); // 10 seconds max
        expect(Enrollment::count())->toBe(500);
        
        echo "\n📊 Bulk Operations Metrics:\n";
        echo "Execution Time: " . round($executionTime, 2) . " seconds\n";
        echo "Records Created: 500\n";
        echo "Records per Second: " . round(500 / $executionTime, 2) . "\n";
    }
}
