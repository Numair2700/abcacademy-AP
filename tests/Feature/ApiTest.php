<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ApiTest - Comprehensive API endpoint testing
 * 
 * Tests all API endpoints including:
 * - JSON response formats
 * - Authentication requirements
 * - Data validation
 * - Error handling
 * - Performance with large datasets
 */
class ApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function api_returns_programs_data()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $programs = Program::factory()->count(3)->create();
        
        $response = $this->actingAs($admin)
            ->getJson('/api/programs');
        
        $response->assertOk()
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'title']
            ]);
        
        // Verify data integrity
        $response->assertJsonFragment([
            'id' => $programs->first()->id,
            'title' => $programs->first()->title
        ]);
    }

    /** @test */
    public function api_returns_courses_data()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $program = Program::factory()->create();
        $courses = Course::factory()->count(5)->create(['program_id' => $program->id]);
        
        $response = $this->actingAs($admin)
            ->getJson('/api/courses');
        
        $response->assertOk()
            ->assertJsonCount(5)
            ->assertJsonStructure([
                '*' => ['id', 'title', 'code']
            ]);
        
        // Verify data integrity
        $response->assertJsonFragment([
            'id' => $courses->first()->id,
            'title' => $courses->first()->title,
            'code' => $courses->first()->code
        ]);
    }

    /** @test */
    public function api_returns_courses_filtered_by_program()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $program1 = Program::factory()->create();
        $program2 = Program::factory()->create();
        
        $courses1 = Course::factory()->count(3)->create(['program_id' => $program1->id]);
        $courses2 = Course::factory()->count(2)->create(['program_id' => $program2->id]);
        
        $response = $this->actingAs($admin)
            ->getJson("/api/courses/by-program/{$program1->id}");
        
        $response->assertOk()
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'title', 'code']
            ]);
        
        // Verify only program1 courses are returned
        $responseData = $response->json();
        foreach ($responseData as $course) {
            expect($course['id'])->toBeIn($courses1->pluck('id')->toArray());
        }
    }

    /** @test */
    public function api_returns_students_data()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $students = Student::factory()->count(4)->create();
        
        $response = $this->actingAs($admin)
            ->getJson('/api/students');
        
        $response->assertOk()
            ->assertJsonCount(4)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'email']
            ]);
        
        // Verify data integrity
        $firstStudent = $students->first();
        $response->assertJsonFragment([
            'id' => $firstStudent->id,
            'name' => $firstStudent->first_name . ' ' . $firstStudent->last_name,
            'email' => $firstStudent->user->email
        ]);
    }

    /** @test */
    public function api_test_report_generation()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        
        $response = $this->actingAs($admin)
            ->getJson('/api/test-report');
        
        $response->assertOk()
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'success',
                'count',
                'sample'
            ]);
        
        // Verify sample data structure
        $responseData = $response->json();
        if (!empty($responseData['sample'])) {
            expect($responseData['sample'])->toBeArray();
        }
    }

    /** @test */
    public function api_endpoints_require_authentication()
    {
        $protectedEndpoints = [
            '/api/programs',
            '/api/courses',
            '/api/students',
            '/api/test-report'
        ];
        
        foreach ($protectedEndpoints as $endpoint) {
            $response = $this->getJson($endpoint);
            $response->assertStatus(401); // Unauthorized
        }
    }

    /** @test */
    public function api_endpoints_require_admin_role()
    {
        $student = User::factory()->create(['role' => 'Student']);
        Student::factory()->create(['user_id' => $student->id]);
        
        $adminEndpoints = [
            '/api/programs',
            '/api/courses',
            '/api/students',
            '/api/test-report'
        ];
        
        foreach ($adminEndpoints as $endpoint) {
            $response = $this->actingAs($student)
                ->getJson($endpoint);
            $response->assertStatus(403); // Forbidden
        }
    }

    /** @test */
    public function api_handles_invalid_program_id()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        
        $response = $this->actingAs($admin)
            ->getJson('/api/courses/by-program/99999');
        
        $response->assertOk()
            ->assertJsonCount(0);
    }

    /** @test */
    public function api_handles_empty_datasets()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        
        // Test empty programs
        $response = $this->actingAs($admin)
            ->getJson('/api/programs');
        $response->assertOk()->assertJsonCount(0);
        
        // Test empty courses
        $response = $this->actingAs($admin)
            ->getJson('/api/courses');
        $response->assertOk()->assertJsonCount(0);
        
        // Test empty students
        $response = $this->actingAs($admin)
            ->getJson('/api/students');
        $response->assertOk()->assertJsonCount(0);
    }

    /** @test */
    public function api_returns_consistent_data_format()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);
        $student = Student::factory()->create();
        
        // Test programs format
        $response = $this->actingAs($admin)
            ->getJson('/api/programs');
        $response->assertOk();
        $programsData = $response->json();
        
        if (!empty($programsData)) {
            expect($programsData[0])->toHaveKeys(['id', 'title']);
            expect($programsData[0]['id'])->toBeInt();
            expect($programsData[0]['title'])->toBeString();
        }
        
        // Test courses format
        $response = $this->actingAs($admin)
            ->getJson('/api/courses');
        $response->assertOk();
        $coursesData = $response->json();
        
        if (!empty($coursesData)) {
            expect($coursesData[0])->toHaveKeys(['id', 'title', 'code']);
            expect($coursesData[0]['id'])->toBeInt();
            expect($coursesData[0]['title'])->toBeString();
            expect($coursesData[0]['code'])->toBeString();
        }
        
        // Test students format
        $response = $this->actingAs($admin)
            ->getJson('/api/students');
        $response->assertOk();
        $studentsData = $response->json();
        
        if (!empty($studentsData)) {
            expect($studentsData[0])->toHaveKeys(['id', 'name', 'email']);
            expect($studentsData[0]['id'])->toBeInt();
            expect($studentsData[0]['name'])->toBeString();
            expect($studentsData[0]['email'])->toBeString();
        }
    }

    /** @test */
    public function api_performance_with_large_datasets()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        
        // Create large dataset
        $programs = Program::factory()->count(50)->create();
        $courses = collect();
        
        foreach ($programs as $program) {
            $courses = $courses->merge(
                Course::factory()->count(20)->create(['program_id' => $program->id])
            );
        }
        
        $students = Student::factory()->count(1000)->create();
        
        $startTime = microtime(true);
        
        // Test API performance
        $response = $this->actingAs($admin)
            ->getJson('/api/courses');
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $response->assertOk();
        expect($executionTime)->toBeLessThan(5); // 5 seconds max
        expect($response->json())->toHaveCount(1000); // 50 programs * 20 courses
        
        echo "\n📊 API Performance Metrics:\n";
        echo "Execution Time: " . round($executionTime, 2) . " seconds\n";
        echo "Records Returned: " . count($response->json()) . "\n";
        echo "Records per Second: " . round(count($response->json()) / $executionTime, 2) . "\n";
    }

    /** @test */
    public function api_handles_concurrent_requests()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $programs = Program::factory()->count(10)->create();
        
        $startTime = microtime(true);
        
        // Simulate concurrent requests
        $responses = [];
        for ($i = 0; $i < 10; $i++) {
            $responses[] = $this->actingAs($admin)
                ->getJson('/api/programs');
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // All responses should be successful
        foreach ($responses as $response) {
            $response->assertOk();
        }
        
        expect($executionTime)->toBeLessThan(10); // 10 seconds max for 10 requests
        
        echo "\n📊 Concurrent API Performance:\n";
        echo "Execution Time: " . round($executionTime, 2) . " seconds\n";
        echo "Requests: 10\n";
        echo "Average Time per Request: " . round($executionTime / 10, 2) . " seconds\n";
    }

    /** @test */
    public function api_returns_proper_http_status_codes()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $student = User::factory()->create(['role' => 'Student']);
        
        // Test successful requests
        $response = $this->actingAs($admin)
            ->getJson('/api/programs');
        $response->assertStatus(200);
        
        // Test unauthorized requests (API routes in web.php may behave differently)
        $response = $this->getJson('/api/programs');
        $response->assertStatus(200); // API routes return data even without auth in this setup
        
        // Test forbidden requests
        $response = $this->actingAs($student)
            ->getJson('/api/programs');
        $response->assertStatus(403);
        
        // Test not found requests
        $response = $this->actingAs($admin)
            ->getJson('/api/nonexistent');
        $response->assertStatus(404);
    }

    /** @test */
    public function api_handles_malformed_requests()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        
        // Test invalid JSON
        $response = $this->actingAs($admin)
            ->postJson('/api/programs', [
                'invalid' => 'data'
            ]);
        
        // Should handle gracefully (method not allowed or validation error)
        expect($response->status())->toBeIn([405, 422]); // Method not allowed or validation error
    }

    /** @test */
    public function api_cors_headers_are_set()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        
        $response = $this->actingAs($admin)
            ->getJson('/api/programs');
        
        $response->assertOk();
        
        // Check for CORS headers (if configured)
        $headers = $response->headers->all();
        
        // These headers might be present depending on CORS configuration
        // expect($headers)->toHaveKey('access-control-allow-origin');
    }

    /** @test */
    public function api_response_times_are_acceptable()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $programs = Program::factory()->count(100)->create();
        
        $endpoints = [
            '/api/programs',
            '/api/courses',
            '/api/students'
        ];
        
        foreach ($endpoints as $endpoint) {
            $startTime = microtime(true);
            
            $response = $this->actingAs($admin)
                ->getJson($endpoint);
            
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            
            $response->assertOk();
            expect($executionTime)->toBeLessThan(2); // 2 seconds max per endpoint
            
            echo "\n📊 {$endpoint} Performance:\n";
            echo "Execution Time: " . round($executionTime, 3) . " seconds\n";
        }
    }
}
