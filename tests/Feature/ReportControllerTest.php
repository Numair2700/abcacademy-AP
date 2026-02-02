<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Program;
use App\Models\Course;
use App\Models\Student;
use App\Models\Enrollment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * ReportControllerTest - Demonstrates Feature Testing of Reporting System
 * 
 * This test class demonstrates comprehensive feature testing of:
 * - HTTP request/response handling
 * - Authentication and authorization
 * - JSON API responses
 * - File downloads
 * - Error handling
 */
class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function admin_can_access_reports_dashboard()
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($admin)
            ->get(route('reports.index'));

        $response->assertOk();
        $response->assertViewIs('reports.index');
        $response->assertViewHas('reportTypes');
        $response->assertViewHas('formats');
    }

    /** @test */
    public function guest_cannot_access_reports_dashboard()
    {
        $response = $this->get(route('reports.index'));

        $response->assertRedirect('/login');
    }

    /** @test */
    public function admin_can_generate_student_report()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);
        $user = User::factory()->create(['role' => 'Student']);
        $student = Student::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)
            ->postJson(route('reports.generate'), [
                'type' => 'students',
                'format' => 'json',
                'filters' => []
            ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data',
            'metadata' => [
                'type',
                'format',
                'filters',
                'generated_at',
                'record_count'
            ]
        ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertIsArray($responseData['data']);
    }

    /** @test */
    public function admin_can_generate_enrollment_report()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);
        $user = User::factory()->create(['role' => 'Student']);
        $student = Student::factory()->create(['user_id' => $user->id]);
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $course->id
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('reports.generate'), [
                'type' => 'enrollments',
                'format' => 'json',
                'filters' => []
            ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data',
            'metadata'
        ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertIsArray($responseData['data']);
    }

    /** @test */
    public function admin_can_generate_course_report()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);

        $response = $this->actingAs($admin)
            ->postJson(route('reports.generate'), [
                'type' => 'courses',
                'format' => 'json',
                'filters' => []
            ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data',
            'metadata'
        ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertIsArray($responseData['data']);
    }

    /** @test */
    public function admin_can_generate_report_with_filters()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);
        $user = User::factory()->create(['role' => 'Student']);
        $student = Student::factory()->create(['user_id' => $user->id]);
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $course->id
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('reports.generate'), [
                'type' => 'enrollments',
                'format' => 'json',
                'filters' => [
                    'program_id' => $program->id
                ]
            ]);

        $response->assertOk();
        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertIsArray($responseData['data']);
    }

    /** @test */
    public function admin_can_generate_report_in_different_formats()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);

        $formats = ['json', 'csv', 'html'];

        foreach ($formats as $format) {
            $response = $this->actingAs($admin)
                ->postJson(route('reports.generate'), [
                    'type' => 'courses',
                    'format' => $format,
                    'filters' => []
                ]);

            $response->assertOk();
            $responseData = $response->json();
            $this->assertTrue($responseData['success']);
            $this->assertIsArray($responseData['data']);
        }
    }

    /** @test */
    public function admin_can_preview_report()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);

        $response = $this->actingAs($admin)
            ->postJson(route('reports.preview'), [
                'type' => 'courses',
                'filters' => []
            ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'preview',
            'total_records',
            'showing'
        ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertIsArray($responseData['preview']);
        $this->assertLessThanOrEqual(10, $responseData['showing']);
    }

    /** @test */
    public function admin_can_export_report()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);

        $response = $this->actingAs($admin)
            ->post(route('reports.export'), [
                'type' => 'courses',
                'format' => 'json',
                'filters' => []
            ]);

        $response->assertOk();
        $response->assertHeader('content-disposition');
    }

    /** @test */
    public function admin_can_get_available_report_types()
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($admin)
            ->getJson(route('reports.types'));

        $response->assertOk();
        $response->assertJsonStructure([
            'types',
            'formats'
        ]);

        $responseData = $response->json();
        $this->assertArrayHasKey('students', $responseData['types']);
        $this->assertArrayHasKey('enrollments', $responseData['types']);
        $this->assertArrayHasKey('courses', $responseData['types']);
        $this->assertArrayHasKey('json', $responseData['formats']);
        $this->assertArrayHasKey('csv', $responseData['formats']);
        $this->assertArrayHasKey('html', $responseData['formats']);
    }

    /** @test */
    public function report_generation_validates_required_fields()
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($admin)
            ->postJson(route('reports.generate'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['type', 'format']);
    }

    /** @test */
    public function report_generation_validates_report_type()
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($admin)
            ->postJson(route('reports.generate'), [
                'type' => 'invalid_type',
                'format' => 'json',
                'filters' => []
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['type']);
    }

    /** @test */
    public function report_generation_validates_format()
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($admin)
            ->postJson(route('reports.generate'), [
                'type' => 'students',
                'format' => 'invalid_format',
                'filters' => []
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['format']);
    }

    /** @test */
    public function report_generation_handles_unknown_report_type()
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        // Mock the service to throw an exception
        $this->mock(\App\Services\ReportService::class, function ($mock) {
            $mock->shouldReceive('generateReportByType')
                ->andThrow(new \InvalidArgumentException('Unknown report type: invalid_type'));
        });

        $response = $this->actingAs($admin)
            ->postJson(route('reports.generate'), [
                'type' => 'invalid_type',
                'format' => 'json',
                'filters' => []
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function guest_cannot_generate_reports()
    {
        $response = $this->postJson(route('reports.generate'), [
            'type' => 'courses',
            'format' => 'json',
            'filters' => []
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function report_controller_handles_html_response()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);

        $response = $this->actingAs($admin)
            ->post(route('reports.generate'), [
                'type' => 'courses',
                'format' => 'html',
                'filters' => []
            ]);

        $response->assertOk();
        $response->assertViewIs('reports.display');
        $response->assertViewHas('report');
        $response->assertViewHas('type');
        $response->assertViewHas('format');
        $response->assertViewHas('title');
    }

    /** @test */
    public function report_export_returns_file_download()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);

        $response = $this->actingAs($admin)
            ->post(route('reports.export'), [
                'type' => 'courses',
                'format' => 'json',
                'filters' => []
            ]);

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));
    }

    /** @test */
    public function admin_can_generate_report_with_filters_as_json_string()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);

        $response = $this->actingAs($admin)
            ->postJson(route('reports.generate'), [
                'type' => 'courses',
                'format' => 'json',
                'filters' => json_encode(['program_id' => $program->id]) // Sending as string
            ]);

        $response->assertOk();
        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
    }
}

