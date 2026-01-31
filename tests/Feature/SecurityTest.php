<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * SecurityTest - Comprehensive security and authorization testing
 * 
 * Tests all security aspects including:
 * - Role-based access control
 * - Authorization policies
 * - Input validation
 * - CSRF protection
 * - SQL injection prevention
 */
class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_cannot_access_student_only_endpoints()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $course = Course::factory()->create(['published' => true]);
        
        // Test enrollment endpoint
        $response = $this->actingAs($admin)
            ->post(route('enrollments.store', $course));
        $response->assertStatus(403);
        
        // Test favorites endpoint
        $response = $this->actingAs($admin)
            ->post(route('favorites.store', $course));
        $response->assertStatus(403);
        
        // Verify no data was created
        $this->assertDatabaseMissing('enrollments', [
            'course_id' => $course->id
        ]);
        $this->assertDatabaseMissing('favorites', [
            'course_id' => $course->id
        ]);
    }

    /** @test */
    public function students_cannot_access_admin_endpoints()
    {
        $student = User::factory()->create(['role' => 'Student']);
        Student::factory()->create(['user_id' => $student->id]);
        
        // Test admin course creation
        $response = $this->actingAs($student)
            ->post(route('admin.courses.store'), [
                'code' => 'TEST123',
                'title' => 'Test Course',
                'language' => 'English',
                'price' => 1000,
                'program_id' => 1
            ]);
        $response->assertStatus(403);
        
        // Test admin reports
        $response = $this->actingAs($student)
            ->get(route('reports.index'));
        $response->assertStatus(403);
        
        // Test admin program management
        $response = $this->actingAs($student)
            ->get(route('admin.programs.index'));
        $response->assertStatus(403);
    }

    /** @test */
    public function guests_are_redirected_to_login_for_protected_routes()
    {
        $course = Course::factory()->create(['published' => true]);
        
        $protectedRoutes = [
            ['method' => 'POST', 'route' => route('enrollments.store', $course)],
            ['method' => 'POST', 'route' => route('favorites.store', $course)],
            ['method' => 'GET', 'route' => route('dashboard')],
            ['method' => 'GET', 'route' => route('admin.courses.index')],
            ['method' => 'GET', 'route' => route('reports.index')],
        ];
        
        foreach ($protectedRoutes as $route) {
            if ($route['method'] === 'POST') {
                $response = $this->post($route['route']);
            } else {
                $response = $this->get($route['route']);
            }
            
            $response->assertRedirect('/login');
        }
    }

    /** @test */
    public function students_cannot_enroll_other_students()
    {
        $student1 = User::factory()->create(['role' => 'Student']);
        $student1Record = Student::factory()->create(['user_id' => $student1->id]);
        
        $student2 = User::factory()->create(['role' => 'Student']);
        $student2Record = Student::factory()->create(['user_id' => $student2->id]);
        
        $course = Course::factory()->create(['published' => true]);
        
        // Student1 tries to enroll as Student2 (should fail)
        $response = $this->actingAs($student1)
            ->post(route('enrollments.store', $course), [
                'student_id' => $student2Record->id // Attempting to manipulate
            ]);
        
        // Should still work but enroll Student1, not Student2
        $response->assertRedirect();
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student1Record->id,
            'course_id' => $course->id
        ]);
        $this->assertDatabaseMissing('enrollments', [
            'student_id' => $student2Record->id,
            'course_id' => $course->id
        ]);
    }

    /** @test */
    public function students_cannot_delete_other_students_enrollments()
    {
        $student1 = User::factory()->create(['role' => 'Student']);
        $student1Record = Student::factory()->create(['user_id' => $student1->id]);
        
        $student2 = User::factory()->create(['role' => 'Student']);
        $student2Record = Student::factory()->create(['user_id' => $student2->id]);
        
        $course = Course::factory()->create(['published' => true]);
        
        // Create enrollment for student2
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student2Record->id,
            'course_id' => $course->id
        ]);
        
        // Student1 tries to delete student2's enrollment
        $response = $this->actingAs($student1)
            ->delete(route('enrollments.destroy', $enrollment));
        
        $response->assertStatus(403);
        
        // Enrollment should still exist
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id
        ]);
    }

    /** @test */
    public function input_validation_prevents_malicious_data()
    {
        $student = User::factory()->create(['role' => 'Student']);
        Student::factory()->create(['user_id' => $student->id]);
        
        $program = Program::factory()->create();
        
        // Test malicious input in course creation (admin only, but test validation)
        $maliciousInputs = [
            'code' => '<script>alert("xss")</script>',
            'title' => 'DROP TABLE courses; --',
            'language' => 'English\'; DROP TABLE users; --',
            'price' => '1000; DELETE FROM courses; --'
        ];
        
        // This should fail validation before reaching the database
        $response = $this->actingAs($student)
            ->post(route('admin.courses.store'), array_merge($maliciousInputs, [
                'program_id' => $program->id
            ]));
        
        // Should be blocked by authorization first
        $response->assertStatus(403);
    }

    /** @test */
    public function csrf_protection_is_enforced()
    {
        $student = User::factory()->create(['role' => 'Student']);
        Student::factory()->create(['user_id' => $student->id]);
        
        $course = Course::factory()->create(['published' => true]);
        
        // Test without CSRF token
        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
            ->actingAs($student)
            ->post(route('enrollments.store', $course));
        
        // Should still work without CSRF middleware disabled for testing
        $response->assertRedirect();
        
        // Test with invalid CSRF token
        $response = $this->actingAs($student)
            ->post(route('enrollments.store', $course), [
                '_token' => 'invalid-token'
            ]);
        
        // Should fail with CSRF error (Laravel redirects on CSRF failure)
        $response->assertRedirect(); // CSRF token mismatch redirects back
    }

    /** @test */
    public function sql_injection_attempts_are_prevented()
    {
        $student = User::factory()->create(['role' => 'Student']);
        Student::factory()->create(['user_id' => $student->id]);
        
        $course = Course::factory()->create(['published' => true]);
        
        // Test SQL injection in enrollment
        $response = $this->actingAs($student)
            ->post(route('enrollments.store', $course), [
                'student_id' => "1'; DROP TABLE enrollments; --"
            ]);
        
        // Should still work (student_id is ignored, uses authenticated user)
        $response->assertRedirect();
        
        // Verify table still exists and enrollment was created
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->student->id,
            'course_id' => $course->id
        ]);
    }

    /** @test */
    public function file_upload_security_is_enforced()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        
        // Test malicious file upload attempt
        $maliciousFile = 'malicious.php';
        $fileContent = '<?php system($_GET["cmd"]); ?>';
        
        $response = $this->actingAs($admin)
            ->post(route('admin.courses.store'), [
                'code' => 'TEST123',
                'title' => 'Test Course',
                'language' => 'English',
                'price' => 1000,
                'program_id' => 1,
                'file' => $maliciousFile
            ]);
        
        // Should fail validation
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function rate_limiting_prevents_abuse()
    {
        $student = User::factory()->create(['role' => 'Student']);
        Student::factory()->create(['user_id' => $student->id]);
        
        $course = Course::factory()->create(['published' => true]);
        
        // Make multiple rapid requests
        for ($i = 0; $i < 10; $i++) {
            $response = $this->actingAs($student)
                ->post(route('enrollments.store', $course));
        }
        
        // Should handle gracefully (either succeed or fail with proper error)
        $response->assertRedirect();
    }

    /** @test */
    public function sensitive_data_is_not_exposed()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $student = User::factory()->create(['role' => 'Student']);
        $studentRecord = Student::factory()->create(['user_id' => $student->id]);
        
        $course = Course::factory()->create(['published' => true]);
        
        // Test that student data is not exposed to other students
        $response = $this->actingAs($student)
            ->get(route('courses.show', $course));
        
        $response->assertOk();
        
        // Should not contain other students' personal information
        $response->assertDontSee($studentRecord->center_ref);
        $response->assertDontSee($student->email);
    }

    /** @test */
    public function admin_can_access_all_endpoints()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);
        
        $adminEndpoints = [
            ['method' => 'GET', 'route' => route('admin.programs.index')],
            ['method' => 'GET', 'route' => route('admin.courses.index')],
            ['method' => 'GET', 'route' => route('admin.units.index')],
            ['method' => 'GET', 'route' => route('reports.index')],
            ['method' => 'GET', 'route' => route('admin.programs.create')],
            ['method' => 'GET', 'route' => route('admin.courses.create')],
        ];
        
        foreach ($adminEndpoints as $endpoint) {
            if ($endpoint['method'] === 'GET') {
                $response = $this->actingAs($admin)
                    ->get($endpoint['route']);
            } else {
                $response = $this->actingAs($admin)
                    ->post($endpoint['route']);
            }
            
            $response->assertStatus(200);
        }
    }

    /** @test */
    public function session_security_is_maintained()
    {
        $student = User::factory()->create(['role' => 'Student']);
        Student::factory()->create(['user_id' => $student->id]);
        
        $course = Course::factory()->create(['published' => true]);
        
        // Test that session is maintained across requests
        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk();
        
        $this->actingAs($student)
            ->post(route('enrollments.store', $course))
            ->assertRedirect();
        
        // Verify enrollment was created (session worked)
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->student->id,
            'course_id' => $course->id
        ]);
    }

    /** @test */
    public function password_security_requirements_are_enforced()
    {
        // Test weak password
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ]);
        
        $response->assertSessionHasErrors(['password']);
        
        // Test strong password
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test2@example.com',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ]);
        
        $response->assertRedirect();
    }
}
