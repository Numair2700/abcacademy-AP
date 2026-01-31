<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Program;
use App\Models\Course;
use App\Models\Unit;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * AdminWorkflowTest - End-to-End Testing of Admin Management Workflows
 * 
 * Tests complete admin workflows for managing:
 * - Programs (CRUD operations)
 * - Courses (CRUD operations)
 * - Units (assignment and management)
 * - Navigation between admin sections
 */
class AdminWorkflowTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test complete program creation workflow
     */
    public function test_admin_can_create_program_with_courses(): void
    {
        // Arrange
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'role' => 'Admin',
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('email', 'admin@test.com')->first())
                    
                    // Step 1: Navigate to admin dashboard
                    ->visit('/dashboard')
                    ->assertSee('Admin User')
                    ->clickLink('Admin Panel')
                    ->waitForLocation('/admin')
                    ->assertSee('Administration')
                    
                    // Step 2: Create new program
                    ->clickLink('Programs')
                    ->waitForLocation('/admin/programs')
                    ->press('Create Program')
                    ->waitForText('Create New Program')
                    
                    // Step 3: Fill program form
                    ->type('title', 'Business Administration')
                    ->type('description', 'Comprehensive business program covering management and leadership')
                    ->select('qualification_level', 'Degree')
                    ->check('published')
                    ->press('Save Program')
                    ->waitForText('Program created successfully')
                    
                    // Step 4: Verify program appears in list
                    ->assertSee('Business Administration')
                    ->assertSee('Degree')
                    ->assertSee('Published');
        });
        
        // Verify database
        $this->assertDatabaseHas('programs', [
            'title' => 'Business Administration',
            'qualification_level' => 'Degree',
            'published' => true,
        ]);
    }

    /**
     * Test complete course creation and unit assignment workflow
     */
    public function test_admin_can_create_course_and_assign_units(): void
    {
        // Arrange
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'Admin',
        ]);
        
        $program = Program::factory()->create([
            'title' => 'Computer Science',
            'published' => true,
        ]);
        
        $unit1 = Unit::factory()->create(['title' => 'Programming Fundamentals']);
        $unit2 = Unit::factory()->create(['title' => 'Data Structures']);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($program) {
            $browser->loginAs(User::where('email', 'admin@test.com')->first())
                    
                    // Navigate to courses
                    ->visit('/admin/courses')
                    ->press('Create Course')
                    ->waitForText('Create New Course')
                    
                    // Fill course form
                    ->type('title', 'Advanced Programming')
                    ->type('code', 'CS301')
                    ->type('description', 'Advanced programming concepts')
                    ->select('program_id', $program->id)
                    ->check('published')
                    
                    // Assign units
                    ->check('units[]')
                    ->assertChecked('units[]')
                    
                    // Save course
                    ->press('Save Course')
                    ->waitForText('Course created successfully')
                    ->assertSee('Advanced Programming')
                    ->assertSee('CS301');
        });
        
        // Verify database
        $this->assertDatabaseHas('courses', [
            'title' => 'Advanced Programming',
            'code' => 'CS301',
            'program_id' => $program->id,
        ]);
    }

    /**
     * Test program editing workflow
     */
    public function test_admin_can_edit_program(): void
    {
        // Arrange
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'Admin',
        ]);
        
        $program = Program::factory()->create([
            'title' => 'Original Title',
            'published' => false,
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($program) {
            $browser->loginAs(User::where('email', 'admin@test.com')->first())
                    ->visit('/admin/programs')
                    ->assertSee('Original Title')
                    
                    // Click edit button
                    ->press('Edit')
                    ->waitForText('Edit Program')
                    
                    // Update program
                    ->clear('title')
                    ->type('title', 'Updated Title')
                    ->check('published')
                    ->press('Update Program')
                    ->waitForText('Program updated successfully')
                    
                    // Verify changes
                    ->assertSee('Updated Title')
                    ->assertDontSee('Original Title');
        });
        
        // Verify database
        $this->assertDatabaseHas('programs', [
            'id' => $program->id,
            'title' => 'Updated Title',
            'published' => true,
        ]);
    }

    /**
     * Test program deletion with confirmation
     */
    public function test_admin_can_delete_program_with_confirmation(): void
    {
        // Arrange
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'Admin',
        ]);
        
        $program = Program::factory()->create([
            'title' => 'Program to Delete',
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($program) {
            $browser->loginAs(User::where('email', 'admin@test.com')->first())
                    ->visit('/admin/programs')
                    ->assertSee('Program to Delete')
                    
                    // Click delete button
                    ->press('Delete')
                    ->waitForText('Are you sure')
                    
                    // Confirm deletion
                    ->press('Confirm')
                    ->waitForText('Program deleted successfully')
                    
                    // Verify removal
                    ->assertDontSee('Program to Delete');
        });
        
        // Verify database
        $this->assertDatabaseMissing('programs', [
            'id' => $program->id,
        ]);
    }

    /**
     * Test navigation between admin sections
     */
    public function test_admin_can_navigate_between_sections(): void
    {
        // Arrange
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'Admin',
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('email', 'admin@test.com')->first())
                    ->visit('/admin')
                    
                    // Test navigation
                    ->clickLink('Programs')
                    ->waitForLocation('/admin/programs')
                    ->assertSee('Programs Management')
                    
                    ->clickLink('Courses')
                    ->waitForLocation('/admin/courses')
                    ->assertSee('Courses Management')
                    
                    ->clickLink('Units')
                    ->waitForLocation('/admin/units')
                    ->assertSee('Units Management')
                    
                    ->clickLink('Dashboard')
                    ->waitForLocation('/dashboard')
                    ->assertSee('Dashboard');
        });
    }
}

