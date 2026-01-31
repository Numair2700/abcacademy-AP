<?php

namespace Tests\Unit;

use App\Services\ReportService;
use App\Services\Reports\StudentReportGenerator;
use App\Services\Reports\EnrollmentReportGenerator;
use App\Services\Reports\CourseReportGenerator;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\Program;
use App\Models\Enrollment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

/**
 * ReportServiceTest - Demonstrates Unit Testing of Service Layer Pattern
 * 
 * This test class demonstrates comprehensive unit testing of:
 * - Service Layer Pattern
 * - Dependency Injection
 * - Factory Method Pattern
 * - Strategy Pattern usage
 */
class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // Load test data
        Storage::fake('local');
    }

    /** @test */
    public function report_service_can_be_instantiated_with_default_generator()
    {
        $service = new ReportService();
        
        $this->assertInstanceOf(ReportService::class, $service);
    }

    /** @test */
    public function report_service_can_be_instantiated_with_custom_generator()
    {
        $generator = new StudentReportGenerator();
        $service = new ReportService($generator);
        
        $this->assertInstanceOf(ReportService::class, $service);
    }

    /** @test */
    public function report_service_generates_student_report()
    {
        // Create test data
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);
        $user = User::factory()->create(['role' => 'Student']);
        $student = Student::factory()->create(['user_id' => $user->id]);
        
        $service = new ReportService();
        $report = $service->generateStudentReport();
        
        $this->assertIsArray($report);
    }

    /** @test */
    public function report_service_generates_enrollment_report()
    {
        // Create test data
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);
        $user = User::factory()->create(['role' => 'Student']);
        $student = Student::factory()->create(['user_id' => $user->id]);
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $course->id
        ]);
        
        $service = new ReportService();
        $report = $service->generateEnrollmentReport();
        
        $this->assertIsArray($report);
    }

    /** @test */
    public function report_service_generates_course_report()
    {
        // Create test data
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);
        
        $service = new ReportService();
        $report = $service->generateCourseReport();
        
        $this->assertIsArray($report);
    }

    /** @test */
    public function report_service_applies_filters_to_student_report()
    {
        // Create test data
        $program1 = Program::factory()->create(['title' => 'Program 1']);
        $program2 = Program::factory()->create(['title' => 'Program 2']);
        
        $course1 = Course::factory()->create(['program_id' => $program1->id]);
        $course2 = Course::factory()->create(['program_id' => $program2->id]);
        
        $user1 = User::factory()->create(['role' => 'Student']);
        $user2 = User::factory()->create(['role' => 'Student']);
        $student1 = Student::factory()->create(['user_id' => $user1->id]);
        $student2 = Student::factory()->create(['user_id' => $user2->id]);
        
        $enrollment1 = Enrollment::factory()->create([
            'student_id' => $student1->id,
            'course_id' => $course1->id
        ]);
        $enrollment2 = Enrollment::factory()->create([
            'student_id' => $student2->id,
            'course_id' => $course2->id
        ]);
        
        $service = new ReportService();
        $report = $service->generateStudentReport(['program_id' => $program1->id]);
        
        $this->assertIsArray($report);
    }

    /** @test */
    public function report_service_generates_reports_in_different_formats()
    {
        $service = new ReportService();
        
        $jsonReport = $service->generateStudentReport([], 'json');
        $csvReport = $service->generateStudentReport([], 'csv');
        $htmlReport = $service->generateStudentReport([], 'html');
        
        $this->assertIsArray($jsonReport);
        $this->assertIsArray($csvReport);
        $this->assertIsArray($htmlReport);
        
        // CSV should be an array of strings
        if (!empty($csvReport)) {
            $this->assertIsString($csvReport[0]);
        }
        
        // HTML should be an array of strings containing HTML
        if (!empty($htmlReport)) {
            $this->assertIsString($htmlReport[0]);
        }
    }

    /** @test */
    public function report_service_generates_multiple_reports()
    {
        $service = new ReportService();
        $reportTypes = ['students', 'enrollments', 'courses'];
        
        $reports = $service->generateMultipleReports($reportTypes);
        
        $this->assertIsArray($reports);
        $this->assertArrayHasKey('students', $reports);
        $this->assertArrayHasKey('enrollments', $reports);
        $this->assertArrayHasKey('courses', $reports);
        
        $this->assertIsArray($reports['students']);
        $this->assertIsArray($reports['enrollments']);
        $this->assertIsArray($reports['courses']);
    }

    /** @test */
    public function report_service_generates_report_by_type()
    {
        $service = new ReportService();
        
        $studentReport = $service->generateReportByType('students');
        $enrollmentReport = $service->generateReportByType('enrollments');
        $courseReport = $service->generateReportByType('courses');
        
        $this->assertIsArray($studentReport);
        $this->assertIsArray($enrollmentReport);
        $this->assertIsArray($courseReport);
    }

    /** @test */
    public function report_service_throws_exception_for_unknown_report_type()
    {
        $service = new ReportService();
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown report type: unknown_type');
        
        $service->generateReportByType('unknown_type');
    }

    /** @test */
    public function report_service_exports_report_to_file()
    {
        $service = new ReportService();
        
        $filename = $service->exportReportToFile('students', [], 'json');
        
        $this->assertIsString($filename);
        $this->assertStringContainsString('students_report_', $filename);
        $this->assertStringEndsWith('.json', $filename);
        
        // Check that file was created (using actual filesystem)
        $filePath = storage_path("app/reports/{$filename}");
        $this->assertTrue(file_exists($filePath), "File should exist at: {$filePath}");
    }

    /** @test */
    public function report_service_exports_different_formats()
    {
        $service = new ReportService();
        
        $jsonFile = $service->exportReportToFile('students', [], 'json');
        $csvFile = $service->exportReportToFile('students', [], 'csv');
        $htmlFile = $service->exportReportToFile('students', [], 'html');
        
        $this->assertStringEndsWith('.json', $jsonFile);
        $this->assertStringEndsWith('.csv', $csvFile);
        $this->assertStringEndsWith('.html', $htmlFile);
        
        $this->assertTrue(file_exists(storage_path("app/reports/{$jsonFile}")));
        $this->assertTrue(file_exists(storage_path("app/reports/{$csvFile}")));
        $this->assertTrue(file_exists(storage_path("app/reports/{$htmlFile}")));
    }

    /** @test */
    public function report_service_returns_available_report_types()
    {
        $service = new ReportService();
        
        $types = $service->getAvailableReportTypes();
        
        $this->assertIsArray($types);
        $this->assertArrayHasKey('students', $types);
        $this->assertArrayHasKey('enrollments', $types);
        $this->assertArrayHasKey('courses', $types);
        
        $this->assertEquals('Student Reports', $types['students']);
        $this->assertEquals('Enrollment Reports', $types['enrollments']);
        $this->assertEquals('Course Reports', $types['courses']);
    }

    /** @test */
    public function report_service_returns_available_formats()
    {
        $service = new ReportService();
        
        $formats = $service->getAvailableFormats();
        
        $this->assertIsArray($formats);
        $this->assertArrayHasKey('json', $formats);
        $this->assertArrayHasKey('csv', $formats);
        $this->assertArrayHasKey('html', $formats);
        
        $this->assertEquals('JSON', $formats['json']);
        $this->assertEquals('CSV', $formats['csv']);
        $this->assertEquals('HTML', $formats['html']);
    }

    /** @test */
    public function report_service_generates_unique_filenames()
    {
        $service = new ReportService();
        
        $filename1 = $service->exportReportToFile('students', [], 'json');
        sleep(1); // Ensure different timestamp
        $filename2 = $service->exportReportToFile('students', [], 'json');
        
        $this->assertNotEquals($filename1, $filename2);
    }

    /** @test */
    public function report_service_handles_empty_reports()
    {
        // Clear existing data without refresh
        \DB::table('enrollments')->delete();
        \DB::table('favorites')->delete();
        \DB::table('course_unit')->delete();
        \DB::table('courses')->delete();
        \DB::table('students')->delete();
        \DB::table('users')->where('role', '!=', 'Admin')->delete();
        
        $service = new ReportService();
        
        $studentReport = $service->generateStudentReport();
        $enrollmentReport = $service->generateEnrollmentReport();
        $courseReport = $service->generateCourseReport();
        
        $this->assertIsArray($studentReport);
        $this->assertIsArray($enrollmentReport);
        $this->assertIsArray($courseReport);
        
        // Reports should be empty but still valid arrays
        $this->assertEmpty($studentReport);
        $this->assertEmpty($enrollmentReport);
        $this->assertEmpty($courseReport);
    }
}
// Example from: tests/Unit/ReportServiceTest.php
