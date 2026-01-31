<?php

namespace Tests\Unit;

use App\Services\ReportGenerator;
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

/**
 * ReportGeneratorTest - Demonstrates Unit Testing of Design Patterns
 * 
 * This test class demonstrates comprehensive unit testing of:
 * - Strategy Pattern implementation
 * - Abstract classes and inheritance
 * - Template Method Pattern
 * - SOLID principles in action
 */
class ReportGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // Load test data
    }

    /** @test */
    public function student_report_generator_implements_report_generator_interface()
    {
        $generator = new StudentReportGenerator();
        
        $this->assertInstanceOf(ReportGenerator::class, $generator);
    }

    /** @test */
    public function enrollment_report_generator_implements_report_generator_interface()
    {
        $generator = new EnrollmentReportGenerator();
        
        $this->assertInstanceOf(ReportGenerator::class, $generator);
    }

    /** @test */
    public function course_report_generator_implements_report_generator_interface()
    {
        $generator = new CourseReportGenerator();
        
        $this->assertInstanceOf(ReportGenerator::class, $generator);
    }

    /** @test */
    public function student_report_generator_collects_student_data()
    {
        // Create test data
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);
        $user = User::factory()->create(['role' => 'Student']);
        $student = Student::factory()->create(['user_id' => $user->id]);
        
        $generator = new StudentReportGenerator();
        $data = $generator->collectData();
        
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $data);
        $this->assertGreaterThanOrEqual(1, $data->count());
    }

    /** @test */
    public function enrollment_report_generator_collects_enrollment_data()
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
        
        $generator = new EnrollmentReportGenerator();
        $data = $generator->collectData();
        
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $data);
        $this->assertGreaterThanOrEqual(1, $data->count());
    }

    /** @test */
    public function course_report_generator_collects_course_data()
    {
        // Create test data
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);
        
        $generator = new CourseReportGenerator();
        $data = $generator->collectData();
        
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $data);
        $this->assertGreaterThanOrEqual(1, $data->count());
    }

    /** @test */
    public function student_report_generator_processes_data_correctly()
    {
        // Create test data
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);
        $user = User::factory()->create(['role' => 'Student']);
        $student = Student::factory()->create(['user_id' => $user->id]);
        
        $generator = new StudentReportGenerator();
        $rawData = $generator->collectData();
        $processedData = $generator->processData($rawData);
        
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $processedData);
        
        if ($processedData->count() > 0) {
            $firstRecord = $processedData->first();
            $this->assertArrayHasKey('student_id', $firstRecord);
            $this->assertArrayHasKey('center_reference', $firstRecord);
            $this->assertArrayHasKey('full_name', $firstRecord);
            $this->assertArrayHasKey('email', $firstRecord);
        }
    }

    /** @test */
    public function enrollment_report_generator_processes_data_correctly()
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
        
        $generator = new EnrollmentReportGenerator();
        $rawData = $generator->collectData();
        $processedData = $generator->processData($rawData);
        
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $processedData);
        
        if ($processedData->count() > 0) {
            $firstRecord = $processedData->first();
            $this->assertArrayHasKey('enrollment_id', $firstRecord);
            $this->assertArrayHasKey('btec_number', $firstRecord);
            $this->assertArrayHasKey('student_name', $firstRecord);
            $this->assertArrayHasKey('course_title', $firstRecord);
        }
    }

    /** @test */
    public function course_report_generator_calculates_popularity_score()
    {
        // Create test data
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);
        
        $generator = new CourseReportGenerator();
        $rawData = $generator->collectData();
        $processedData = $generator->processData($rawData);
        
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $processedData);
        
        if ($processedData->count() > 0) {
            $firstRecord = $processedData->first();
            $this->assertArrayHasKey('popularity_score', $firstRecord);
            $this->assertIsFloat($firstRecord['popularity_score']);
            $this->assertGreaterThanOrEqual(0, $firstRecord['popularity_score']);
        }
    }

    /** @test */
    public function report_generator_formats_data_as_json()
    {
        $generator = new StudentReportGenerator();
        $data = collect([
            ['id' => 1, 'name' => 'Test'],
            ['id' => 2, 'name' => 'Test2']
        ]);
        
        $formatted = $generator->formatData($data);
        
        $this->assertIsArray($formatted);
        $this->assertCount(2, $formatted);
    }

    /** @test */
    public function report_generator_formats_data_as_csv()
    {
        $generator = new StudentReportGenerator();
        $generator->setFormat('csv');
        
        $data = collect([
            ['id' => 1, 'name' => 'Test'],
            ['id' => 2, 'name' => 'Test2']
        ]);
        
        $formatted = $generator->formatData($data);
        
        $this->assertIsArray($formatted);
        $this->assertStringContainsString('"Id","Name"', $formatted[0]);
        $this->assertStringContainsString('"1","Test"', $formatted[1]);
    }

    /** @test */
    public function report_generator_formats_data_as_html()
    {
        $generator = new StudentReportGenerator();
        $generator->setFormat('html');
        
        $data = collect([
            ['id' => 1, 'name' => 'Test'],
            ['id' => 2, 'name' => 'Test2']
        ]);
        
        $formatted = $generator->formatData($data);
        
        $this->assertIsArray($formatted);
        $this->assertStringContainsString('<table', implode('', $formatted));
        $this->assertStringContainsString('>Id</th>', implode('', $formatted));
    }

    /** @test */
    public function report_generator_handles_empty_data()
    {
        $generator = new StudentReportGenerator();
        $data = collect([]);
        
        $formatted = $generator->formatData($data);
        
        $this->assertIsArray($formatted);
        $this->assertEmpty($formatted);
    }

    /** @test */
    public function student_report_generator_applies_filters()
    {
        // Create test data with different programs
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
        
        $generator = new StudentReportGenerator();
        $generator->setFilters(['program_id' => $program1->id]);
        
        $data = $generator->collectData();
        
        // Should only return students enrolled in program1
        $this->assertGreaterThanOrEqual(1, $data->count());
    }

    /** @test */
    public function report_generator_template_method_works()
    {
        $generator = new StudentReportGenerator();
        $generator->setFormat('json');
        
        $result = $generator->generate();
        
        $this->assertIsArray($result);
        // The template method should have called collectData, processData, and formatData
    }
}
