<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Course;
use App\Models\Enrollment;

test('student can enroll in a course', function () {
    // Create a student user
    $user = User::factory()->create(['role' => 'Student']);
    $student = Student::factory()->create(['user_id' => $user->id]);
    
    // Create a program and course
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    // Enroll in the course
    $response = $this->actingAs($user)
        ->post(route('enrollments.store', $course));

    // Assertions
    $response->assertRedirect();
    $response->assertSessionHas('status', 'Successfully enrolled in ' . $course->title . '!');
    
    // Check enrollment was created
    $this->assertDatabaseHas('enrollments', [
        'student_id' => $student->id,
        'course_id' => $course->id,
    ]);
    
    // Check BTEC number was generated
    $enrollment = Enrollment::where('student_id', $student->id)
        ->where('course_id', $course->id)
        ->first();
    
    expect($enrollment->btec_number)->toContain($course->code);
    expect($enrollment->btec_number)->toContain($student->center_ref);
    expect($enrollment->session)->toMatch('/^\d{2}-\d{2}$/'); // Format: 24-25
});

test('student cannot enroll twice in the same course', function () {
    // Create a student user
    $user = User::factory()->create(['role' => 'Student']);
    $student = Student::factory()->create(['user_id' => $user->id]);
    
    // Create a program and course
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    // First enrollment
    $this->actingAs($user)
        ->post(route('enrollments.store', $course));

    // Second enrollment attempt
    $response = $this->actingAs($user)
        ->post(route('enrollments.store', $course));

    // Should show error message
    $response->assertRedirect();
    $response->assertSessionHas('status', 'You are already enrolled in this course.');
    
    // Should only have one enrollment
    $enrollments = Enrollment::where('student_id', $student->id)
        ->where('course_id', $course->id)
        ->count();
    
    expect($enrollments)->toBe(1);
});

test('only students can enroll in courses', function () {
    // Create an admin user
    $admin = User::factory()->create(['role' => 'Admin']);
    
    // Create a program and course
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    // Admin tries to enroll
    $response = $this->actingAs($admin)
        ->post(route('enrollments.store', $course));

    // Should be forbidden
    $response->assertStatus(403);
    
    // No enrollment should be created
    $this->assertDatabaseMissing('enrollments', [
        'course_id' => $course->id,
    ]);
});

test('guest cannot enroll in courses', function () {
    // Create a program and course
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    // Guest tries to enroll
    $response = $this->post(route('enrollments.store', $course));

    // Should redirect to login
    $response->assertRedirect('/login');
    
    // No enrollment should be created
    $this->assertDatabaseMissing('enrollments', [
        'course_id' => $course->id,
    ]);
});

test('student can cancel their enrollment', function () {
    // Create a student user
    $user = User::factory()->create(['role' => 'Student']);
    $student = Student::factory()->create(['user_id' => $user->id]);
    
    // Create a program and course
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    // Create enrollment
    $enrollment = Enrollment::factory()->create([
        'student_id' => $student->id,
        'course_id' => $course->id,
    ]);

    // Cancel enrollment
    $response = $this->actingAs($user)
        ->delete(route('enrollments.destroy', $enrollment));

    // Should redirect and show success message
    $response->assertRedirect();
    $response->assertSessionHas('status', 'Enrollment cancelled.');
    
    // Enrollment should be deleted
    $this->assertDatabaseMissing('enrollments', [
        'id' => $enrollment->id,
    ]);
});

test('student cannot cancel another students enrollment', function () {
    // Create two students
    $user1 = User::factory()->create(['role' => 'Student']);
    $student1 = Student::factory()->create(['user_id' => $user1->id]);
    
    $user2 = User::factory()->create(['role' => 'Student']);
    $student2 = Student::factory()->create(['user_id' => $user2->id]);
    
    // Create a program and course
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    // Create enrollment for student1
    $enrollment = Enrollment::factory()->create([
        'student_id' => $student1->id,
        'course_id' => $course->id,
    ]);

    // Student2 tries to cancel student1's enrollment
    $response = $this->actingAs($user2)
        ->delete(route('enrollments.destroy', $enrollment));

    // Should be forbidden
    $response->assertStatus(403);
    
    // Enrollment should still exist
    $this->assertDatabaseHas('enrollments', [
        'id' => $enrollment->id,
    ]);
});

test('btec number format is correct', function () {
    // Create a student user
    $user = User::factory()->create(['role' => 'Student']);
    $student = Student::factory()->create(['user_id' => $user->id]);
    
    // Create a program and course
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true,
        'code' => 'ABC123'
    ]);

    // Enroll in the course
    $this->actingAs($user)
        ->post(route('enrollments.store', $course));

    // Get the enrollment
    $enrollment = Enrollment::where('student_id', $student->id)
        ->where('course_id', $course->id)
        ->first();

    // Check BTEC number format: COURSE_CODE-STUDENT_REF-TIMESTAMP
    expect($enrollment->btec_number)->toStartWith('ABC123-');
    expect($enrollment->btec_number)->toContain($student->center_ref);
    expect($enrollment->btec_number)->toMatch('/^ABC123-' . preg_quote($student->center_ref, '/') . '-\d+$/');
});

test('academic session is generated correctly', function () {
    // Create a student user
    $user = User::factory()->create(['role' => 'Student']);
    $student = Student::factory()->create(['user_id' => $user->id]);
    
    // Create a program and course
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    // Enroll in the course
    $this->actingAs($user)
        ->post(route('enrollments.store', $course));

    // Get the enrollment
    $enrollment = Enrollment::where('student_id', $student->id)
        ->where('course_id', $course->id)
        ->first();

    // Check session format (e.g., 24-25 for 2024-2025)
    $currentYear = date('Y');
    $nextYear = $currentYear + 1;
    $expectedSession = substr($currentYear, -2) . '-' . substr($nextYear, -2);
    
    expect($enrollment->session)->toBe($expectedSession);
});
