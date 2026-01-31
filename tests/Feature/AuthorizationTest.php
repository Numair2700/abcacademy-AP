<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Course;

test('enrollment policy allows students to create enrollments', function () {
    $user = User::factory()->create(['role' => 'Student']);
    $student = Student::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    expect($user->can('create', Enrollment::class))->toBeTrue();
});

test('enrollment policy denies non-students from creating enrollments', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $this->actingAs($admin);

    expect($admin->can('create', Enrollment::class))->toBeFalse();
});

test('enrollment policy denies users without student profile from creating enrollments', function () {
    $user = User::factory()->create(['role' => 'Student']);
    // No student profile created

    $this->actingAs($user);

    expect($user->can('create', Enrollment::class))->toBeFalse();
});

test('enrollment policy allows students to delete their own enrollments', function () {
    $user = User::factory()->create(['role' => 'Student']);
    $student = Student::factory()->create(['user_id' => $user->id]);
    
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    $enrollment = Enrollment::factory()->create([
        'student_id' => $student->id,
        'course_id' => $course->id,
    ]);

    $this->actingAs($user);

    expect($user->can('delete', $enrollment))->toBeTrue();
});

test('enrollment policy denies students from deleting other students enrollments', function () {
    $user1 = User::factory()->create(['role' => 'Student']);
    $student1 = Student::factory()->create(['user_id' => $user1->id]);
    
    $user2 = User::factory()->create(['role' => 'Student']);
    $student2 = Student::factory()->create(['user_id' => $user2->id]);
    
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    $enrollment = Enrollment::factory()->create([
        'student_id' => $student1->id,
        'course_id' => $course->id,
    ]);

    $this->actingAs($user2);

    expect($user2->can('delete', $enrollment))->toBeFalse();
});

test('enrollment policy denies admins from deleting student enrollments', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    
    $user = User::factory()->create(['role' => 'Student']);
    $student = Student::factory()->create(['user_id' => $user->id]);
    
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    $enrollment = Enrollment::factory()->create([
        'student_id' => $student->id,
        'course_id' => $course->id,
    ]);

    $this->actingAs($admin);

    expect($admin->can('delete', $enrollment))->toBeFalse();
});

test('admin authorization works correctly', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $student = User::factory()->create(['role' => 'Student']);

    // Test admin access
    $this->actingAs($admin);
    $response = $this->get(route('admin.programs.index'));
    $response->assertOk();

    // Test student access
    $this->actingAs($student);
    $response = $this->get(route('admin.programs.index'));
    $response->assertStatus(403);
});

test('student role is assigned by default during registration', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->post(route('register'), $userData);

    $response->assertRedirect(route('dashboard'));

    $user = User::where('email', 'test@example.com')->first();
    expect($user->role)->toBe('Student');
    expect($user->student)->not->toBeNull();
});

test('student profile is created automatically during registration', function () {
    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->post(route('register'), $userData);

    $user = User::where('email', 'john@example.com')->first();
    
    expect($user->student)->not->toBeNull();
    expect($user->student->first_name)->toBe('John');
    expect($user->student->last_name)->toBe('Doe');
    expect($user->student->center_ref)->toMatch('/^A\d{3}B\d{3}C$/');
});

test('center reference format is correct', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $this->post(route('register'), $userData);

    $user = User::where('email', 'test@example.com')->first();
    $centerRef = $user->student->center_ref;
    
    // Format: A123B456C
    expect($centerRef)->toMatch('/^A\d{3}B\d{3}C$/');
    expect(strlen($centerRef))->toBe(9);
});

test('duplicate center references are prevented', function () {
    // Create first user
    $user1 = User::factory()->create(['role' => 'Student']);
    $student1 = Student::factory()->create(['user_id' => $user1->id]);
    
    // Create second user
    $user2 = User::factory()->create(['role' => 'Student']);
    $student2 = Student::factory()->create(['user_id' => $user2->id]);
    
    // Center references should be different
    expect($student1->center_ref)->not->toBe($student2->center_ref);
});

test('admin can access all admin routes', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $this->actingAs($admin);

    // Test all admin routes
    $this->get(route('admin.programs.index'))->assertOk();
    $this->get(route('admin.programs.create'))->assertOk();
    $this->get(route('admin.courses.index'))->assertOk();
    $this->get(route('admin.courses.create'))->assertOk();
    $this->get(route('admin.units.index'))->assertOk();
    $this->get(route('admin.units.create'))->assertOk();
});

test('student cannot access any admin routes', function () {
    $student = User::factory()->create(['role' => 'Student']);

    $this->actingAs($student);

    // Test all admin routes should be forbidden
    $this->get(route('admin.programs.index'))->assertStatus(403);
    $this->get(route('admin.programs.create'))->assertStatus(403);
    $this->get(route('admin.courses.index'))->assertStatus(403);
    $this->get(route('admin.courses.create'))->assertStatus(403);
    $this->get(route('admin.units.index'))->assertStatus(403);
    $this->get(route('admin.units.create'))->assertStatus(403);
});

test('guest cannot access any admin routes', function () {
    // Test all admin routes should redirect to login
    $this->get(route('admin.programs.index'))->assertRedirect('/login');
    $this->get(route('admin.programs.create'))->assertRedirect('/login');
    $this->get(route('admin.courses.index'))->assertRedirect('/login');
    $this->get(route('admin.courses.create'))->assertRedirect('/login');
    $this->get(route('admin.units.index'))->assertRedirect('/login');
    $this->get(route('admin.units.create'))->assertRedirect('/login');
});

test('student can access student-only routes', function () {
    $student = User::factory()->create(['role' => 'Student']);

    $this->actingAs($student);

    // Test student routes
    $this->get(route('dashboard'))->assertOk();
});

test('admin can also access student routes', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $this->actingAs($admin);

    // Admin should be able to access dashboard
    $this->get(route('dashboard'))->assertOk();
});
