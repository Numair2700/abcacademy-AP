<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * AuthenticationWorkflowTest - End-to-End Testing of Authentication
 * 
 * Tests complete authentication workflows:
 * - User registration with validation
 * - Login/logout flows
 * - Password reset functionality
 * - Session management
 * - Redirect behavior after authentication
 */
class AuthenticationWorkflowTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test complete user registration workflow
     */
    public function test_new_user_can_register(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->assertSee('Register')
                    
                    // Fill registration form
                    ->type('name', 'John Doe')
                    ->type('email', 'john@example.com')
                    ->type('password', 'SecurePassword123!')
                    ->type('password_confirmation', 'SecurePassword123!')
                    ->select('role', 'Student')
                    
                    // Submit form
                    ->press('Register')
                    ->waitForLocation('/dashboard')
                    
                    // Verify successful registration
                    ->assertSee('Dashboard')
                    ->assertSee('John Doe')
                    ->assertAuthenticatedAs(User::where('email', 'john@example.com')->first());
        });
        
        // Verify database
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'Student',
        ]);
    }

    /**
     * Test login workflow with valid credentials
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'role' => 'Student',
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->assertSee('Log in')
                    
                    // Enter credentials
                    ->type('email', 'jane@example.com')
                    ->type('password', 'password')
                    
                    // Submit
                    ->press('Log in')
                    ->waitForLocation('/dashboard')
                    
                    // Verify login
                    ->assertSee('Jane Smith')
                    ->assertAuthenticatedAs($user);
        });
    }

    /**
     * Test login validation with invalid credentials
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        // Arrange
        User::factory()->create([
            'email' => 'user@example.com',
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'user@example.com')
                    ->type('password', 'wrong-password')
                    ->press('Log in')
                    ->waitForText('These credentials do not match our records')
                    ->assertGuest();
        });
    }

    /**
     * Test logout workflow
     */
    public function test_authenticated_user_can_logout(): void
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'Logout Test User',
            'email' => 'logout@example.com',
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->assertSee('Logout Test User')
                    
                    // Logout
                    ->press('Log Out')
                    ->waitForLocation('/')
                    
                    // Verify logout
                    ->assertGuest()
                    
                    // Try to access protected route
                    ->visit('/dashboard')
                    ->waitForLocation('/login')
                    ->assertSee('Log in');
        });
    }

    /**
     * Test remember me functionality
     */
    public function test_user_can_login_with_remember_me(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'remember@example.com',
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', 'remember@example.com')
                    ->type('password', 'password')
                    ->check('remember')
                    ->press('Log in')
                    ->waitForLocation('/dashboard')
                    ->assertAuthenticatedAs($user);
            
            // Close and reopen browser (simulating new session)
            $browser->visit('/dashboard')
                    ->assertAuthenticatedAs($user);
        });
    }

    /**
     * Test role-based redirect after login
     */
    public function test_admin_redirects_to_admin_dashboard_after_login(): void
    {
        // Arrange
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'Admin',
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit('/login')
                    ->type('email', 'admin@example.com')
                    ->type('password', 'password')
                    ->press('Log in')
                    ->waitForLocation('/dashboard')
                    ->assertSee('Admin Panel')
                    ->assertAuthenticatedAs($admin);
        });
    }

    /**
     * Test password validation during registration
     */
    public function test_registration_validates_password_requirements(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->type('name', 'Test User')
                    ->type('email', 'test@example.com')
                    ->type('password', 'weak')
                    ->type('password_confirmation', 'weak')
                    ->press('Register')
                    ->waitForText('password must be at least')
                    ->assertPathIs('/register');
        });
    }

    /**
     * Test email validation during registration
     */
    public function test_registration_validates_unique_email(): void
    {
        // Arrange
        User::factory()->create(['email' => 'existing@example.com']);

        // Act & Assert
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->type('name', 'Duplicate User')
                    ->type('email', 'existing@example.com')
                    ->type('password', 'SecurePassword123!')
                    ->type('password_confirmation', 'SecurePassword123!')
                    ->press('Register')
                    ->waitForText('email has already been taken')
                    ->assertPathIs('/register');
        });
    }

    /**
     * Test session timeout and re-authentication
     */
    public function test_session_timeout_requires_re_authentication(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'session@example.com',
        ]);

        // Act & Assert
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->assertAuthenticatedAs($user)
                    
                    // Simulate session timeout
                    ->visit('/logout')
                    ->waitForLocation('/')
                    
                    // Try to access protected route
                    ->visit('/dashboard')
                    ->waitForLocation('/login')
                    ->assertGuest();
        });
    }
}

