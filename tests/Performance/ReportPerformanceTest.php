<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\ReportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ReportPerformanceTest - Performance testing for report generation
 * 
 * Tests the system's ability to generate reports efficiently with large datasets
 * Demonstrates query optimization and memory management
 */
class ReportPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::disableQueryLog();
    }

    /** @test */
    public function student_report_generation_scales_with_large_datasets()
    {
        // Arrange - Create large dataset
        $this->createLargeDataset(5000); // 5000 students, 500 courses
        
        $service = new ReportService();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Act
        $report = $service->generateStudentReport([], 'json');
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        $executionTime = $endTime - $startTime;
        $memoryUsed = $endMemory - $startMemory;
        
        // Assert
        expect($executionTime)->toBeLessThan(15); // 15 seconds max
        expect($memoryUsed)->toBeLessThan(100 * 1024 * 1024); // 100MB max
        expect(count($report))->toBe(5000);
        
        echo "\n📊 Student Report Performance Metrics:\n";
        echo "Execution Time: " . round($executionTime, 2) . " seconds\n";
        echo "Memory Used: " . round($memoryUsed / 1024 / 1024, 2) . " MB\n";
        echo "Records Processed: " . count($report) . "\n";
        echo "Records per Second: " . round(count($report) / $executionTime, 2) . "\n";
    }

    /** @test */
    public function enrollment_report_generation_is_efficient()
    {
        // Arrange - Create large enrollment dataset
        $this->createLargeEnrollmentDataset(10000); // 10,000 enrollments
        
        $service = new ReportService();
        
        $startTime = microtime(true);
        
        // Act
        $report = $service->generateEnrollmentReport([], 'json');
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Assert
        expect($executionTime)->toBeLessThan(20); // 20 seconds max
        expect(count($report))->toBe(10000);
        
        echo "\n📊 Enrollment Report Performance Metrics:\n";
        echo "Execution Time: " . round($executionTime, 2) . " seconds\n";
        echo "Records Processed: " . count($report) . "\n";
        echo "Records per Second: " . round(count($report) / $executionTime, 2) . "\n";
    }

    /** @test */
    public function course_report_generation_scales_well()
    {
        // Arrange - Create large course dataset
        $programs = Program::factory()->count(50)->create();
        $courses = collect();
        
        foreach ($programs as $program) {
            $courses = $courses->merge(
                Course::factory()->count(100)->create(['program_id' => $program->id])
            );
        }
        
        $service = new ReportService();
        
        $startTime = microtime(true);
        
        // Act
        $report = $service->generateCourseReport([], 'json');
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Assert
        expect($executionTime)->toBeLessThan(10); // 10 seconds max
        expect(count($report))->toBe(5000); // 50 programs * 100 courses
        
        echo "\n📊 Course Report Performance Metrics:\n";
        echo "Execution Time: " . round($executionTime, 2) . " seconds\n";
        echo "Records Processed: " . count($report) . "\n";
        echo "Records per Second: " . round(count($report) / $executionTime, 2) . "\n";
    }

    /** @test */
    public function report_generation_with_filters_is_optimized()
    {
        // Arrange
        $this->createLargeDataset(3000);
        
        $service = new ReportService();
        
        // Test with filters
        $filters = [
            'program_id' => 1,
            'enrollment_date_from' => '2024-01-01',
            'enrollment_date_to' => '2024-12-31'
        ];
        
        $startTime = microtime(true);
        
        // Act
        $report = $service->generateStudentReport($filters, 'json');
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Assert
        expect($executionTime)->toBeLessThan(5); // 5 seconds max with filters
        expect(count($report))->toBeLessThan(3000); // Should be filtered
        
        echo "\n📊 Filtered Report Performance Metrics:\n";
        echo "Execution Time: " . round($executionTime, 2) . " seconds\n";
        echo "Records Processed: " . count($report) . "\n";
        echo "Filter Efficiency: " . round((1 - count($report) / 3000) * 100, 2) . "% reduction\n";
    }

    /** @test */
    public function report_export_performance_is_acceptable()
    {
        // Arrange
        $this->createLargeDataset(2000);
        
        $service = new ReportService();
        
        $startTime = microtime(true);
        
        // Act - Export to different formats
        $jsonFile = $service->exportReportToFile('students', [], 'json');
        $csvFile = $service->exportReportToFile('students', [], 'csv');
        $htmlFile = $service->exportReportToFile('students', [], 'html');
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Assert
        expect($executionTime)->toBeLessThan(30); // 30 seconds max for all formats
        expect($jsonFile)->toBeString();
        expect($csvFile)->toBeString();
        expect($htmlFile)->toBeString();
        
        // Check files exist
        expect(file_exists(storage_path("app/reports/{$jsonFile}")))->toBeTrue();
        expect(file_exists(storage_path("app/reports/{$csvFile}")))->toBeTrue();
        expect(file_exists(storage_path("app/reports/{$htmlFile}")))->toBeTrue();
        
        echo "\n📊 Report Export Performance Metrics:\n";
        echo "Execution Time: " . round($executionTime, 2) . " seconds\n";
        echo "Files Created: 3 (JSON, CSV, HTML)\n";
        echo "Average Time per Format: " . round($executionTime / 3, 2) . " seconds\n";
    }

    /** @test */
    public function multiple_report_generation_is_efficient()
    {
        // Arrange
        $this->createLargeDataset(1500);
        
        $service = new ReportService();
        
        $startTime = microtime(true);
        
        // Act - Generate multiple reports
        $reports = $service->generateMultipleReports(['students', 'enrollments', 'courses']);
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Assert
        expect($executionTime)->toBeLessThan(25); // 25 seconds max
        expect($reports)->toBeArray();
        expect($reports)->toHaveKey('students');
        expect($reports)->toHaveKey('enrollments');
        expect($reports)->toHaveKey('courses');
        
        echo "\n📊 Multiple Report Performance Metrics:\n";
        echo "Execution Time: " . round($executionTime, 2) . " seconds\n";
        echo "Reports Generated: 3\n";
        echo "Average Time per Report: " . round($executionTime / 3, 2) . " seconds\n";
    }

    /** @test */
    public function report_generation_memory_usage_is_controlled()
    {
        // Arrange
        $this->createLargeDataset(1000);
        
        $service = new ReportService();
        
        $initialMemory = memory_get_usage();
        
        // Act - Generate report
        $report = $service->generateStudentReport([], 'json');
        
        $peakMemory = memory_get_peak_usage();
        $memoryUsed = $peakMemory - $initialMemory;
        
        // Assert
        expect($memoryUsed)->toBeLessThan(50 * 1024 * 1024); // 50MB max
        expect(count($report))->toBe(1000);
        
        echo "\n📊 Memory Usage Metrics:\n";
        echo "Initial Memory: " . round($initialMemory / 1024 / 1024, 2) . " MB\n";
        echo "Peak Memory: " . round($peakMemory / 1024 / 1024, 2) . " MB\n";
        echo "Memory Used: " . round($memoryUsed / 1024 / 1024, 2) . " MB\n";
        echo "Memory per Record: " . round($memoryUsed / count($report), 2) . " bytes\n";
    }

    /**
     * Helper method to create large dataset for testing
     */
    private function createLargeDataset(int $studentCount): void
    {
        $programs = Program::factory()->count(10)->create();
        $courses = collect();
        
        foreach ($programs as $program) {
            $courses = $courses->merge(
                Course::factory()->count(50)->create(['program_id' => $program->id])
            );
        }
        
        $students = Student::factory()->count($studentCount)->create();
        
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
    }

    /**
     * Helper method to create large enrollment dataset
     */
    private function createLargeEnrollmentDataset(int $enrollmentCount): void
    {
        // Create programs first
        $programs = Program::factory()->count(20)->create();
        
        // Create courses for each program
        $courses = collect();
        foreach ($programs as $program) {
            $courses = $courses->merge(
                Course::factory()->count(25)->create(['program_id' => $program->id])
            );
        }
        
        // Create students
        $students = Student::factory()->count(2000)->create();
        
        // Create unique enrollments using a more efficient approach
        $createdEnrollments = 0;
        $maxAttempts = $enrollmentCount * 3; // Reasonable limit
        $attempts = 0;
        
        while ($createdEnrollments < $enrollmentCount && $attempts < $maxAttempts) {
            $student = $students->random();
            $course = $courses->random();
            
            // Check if this combination already exists
            $exists = Enrollment::where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->exists();
                
            if (!$exists) {
                try {
                    Enrollment::factory()->create([
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                    ]);
                    $createdEnrollments++;
                } catch (\Exception $e) {
                    // Skip if any other constraint fails
                }
            }
            $attempts++;
        }
        
        // If we couldn't create enough unique enrollments, create some with different sessions
        if ($createdEnrollments < $enrollmentCount) {
            $remaining = $enrollmentCount - $createdEnrollments;
            $sessions = ['2023-2024', '2024-2025', '2025-2026'];
            
            for ($i = 0; $i < $remaining && $i < 100; $i++) {
                $student = $students->random();
                $course = $courses->random();
                $session = $sessions[array_rand($sessions)];
                
                try {
                    Enrollment::factory()->create([
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                        'session' => $session,
                    ]);
                } catch (\Exception $e) {
                    // Skip if still fails
                }
            }
        }
    }
}
