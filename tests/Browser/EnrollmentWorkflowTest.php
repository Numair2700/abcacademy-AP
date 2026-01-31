<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Course;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * EnrollmentWorkflowTest - End-to-End Testing of Student Enrollment
 * 
 * This test demonstrates TRUE E2E testing using browser automation:
 * - Tests complete user workflows from browser perspective
 * - Validates UI interactions (clicks, forms, navigation)
 * - Tests JavaScript functionality
 * - Verifies visual elements and user feedback
 * - Simulates real user behavior
 */
class EnrollmentWorkflowTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test complete student enrollment workflow from login to enrollment confirmation
     */
    public function test_student_can_complete_enrollment_workflow(): void
    {
        // Arrange: Create test data
        $user = User::factory()->create([
            'name' => 'Test Student',
            'email' => 'student@test.com',
            'role' => 'Student',
        ]);
        
        $student = Student::factory()->create([
            'user_id' => $user->id,
            'center_ref' => 'CTR001',
        ]);
        
        $program = Program::factory()->create([
            'title' => 'Computer Science Degree',
            'published' => true,
        ]);
        
        $course = Course::factory()->create([
            'title' => 'Introduction to Programming',
            'code' => 'CS101',
            'program_id' => $program->id,
            'published' => true,
        ]);

        // Act & Assert: Test complete workflow
        $this->browse(function (Browser $browser) use ($user, $course) {
            $browser->visit('/login')
                    // Step 1: Login
                    ->assertSee('Log in')
                    ->type('email', 'student@test.com')
                    ->type('password', 'password')
                    ->press('Log in')
                    ->waitForLocation('/dashboard')
                    
                    // Step 2: Navigate to courses
                    ->assertSee('Dashboard')
                    ->assertSee('Test Student')
                    ->clickLink('Programs & Courses')
                    ->waitForText('Computer Science Degree')
                    
                    // Step 3: View course details
                    ->assertSee('Introduction to Programming')
                    ->assertSee('CS101')
                    ->press('View Details')
                    ->waitForText('Course Information')
                    
                    // Step 4: Enroll in course
                    ->press('Enroll Now')
                    ->waitForText('Successfully enrolled')
                    ->assertSee('BTEC')
                    
                    // Step 5: Verify enrollment in student dashboard
                    ->visit('/dashboard')
                    ->waitForText('My Enrollments')
                    ->assertSee('Introduction to Programming')
                    ->assertSee('Active');
        });
        
        // Verify database state
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'Active',
        ]);
    }

    /**
     * Test enrollment validation and error handling
     */
    public function test_student_cannot_enroll_in_unpublished_course(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'student@test.com',
            'role' => 'Student',
        ]);
        
        Student::factory()->create(['user_id' => $user->id]);
        
        $program = Program::factory()->create(['published' => true]);
        
        $course = Course::factory()->create([
            'title' => 'Unpublished Course',
            'program_id' => $program->id,
            'published' => false, // Not published
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('email', 'student@test.com')->first())
                    ->visit('/programs')
                    ->assertDontSee('Unpublished Course');
        });
    }

    /**
     * Test duplicate enrollment prevention
     */
    public function test_student_cannot_enroll_twice_in_same_course(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'student@test.com',
            'role' => 'Student',
        ]);
        
        $student = Student::factory()->create(['user_id' => $user->id]);
        
        $program = Program::factory()->create(['published' => true]);
        
        $course = Course::factory()->create([
            'title' => 'Test Course',
            'program_id' => $program->id,
            'published' => true,
        ]);
        
        // First enrollment
        $student->enrollments()->create([
            'course_id' => $course->id,
            'status' => 'Active',
            'btec_number' => 'BTEC001',
            'session' => '24-25',
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($course) {
            $browser->loginAs(User::where('email', 'student@test.com')->first())
                    ->visit('/courses/' . $course->id)
                    ->assertSee('Already Enrolled')
                    ->assertDontSee('Enroll Now');
        });
    }

    /**
     * Test enrollment with favorites functionality
     */
    public function test_student_can_favorite_and_enroll_in_course(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'student@test.com',
            'role' => 'Student',
        ]);
        
        Student::factory()->create(['user_id' => $user->id]);
        
        $program = Program::factory()->create(['published' => true]);
        
        $course = Course::factory()->create([
            'title' => 'Favorite Test Course',
            'program_id' => $program->id,
            'published' => true,
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($course) {
            $browser->loginAs(User::where('email', 'student@test.com')->first())
                    ->visit('/courses/' . $course->id)
                    
                    // Test favorite functionality
                    ->press('Add to Favorites')
                    ->waitForText('Added to favorites')
                    ->pause(500)
                    
                    // Navigate to favorites
                    ->visit('/favorites')
                    ->waitForText('Favorite Test Course')
                    
                    // Enroll from favorites
                    ->press('Enroll')
                    ->waitForText('Successfully enrolled')
                    ->assertSee('BTEC');
        });
    }
}

