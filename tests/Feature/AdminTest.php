<?php

use App\Models\User;
use App\Models\Program;
use App\Models\Course;
use App\Models\Unit;

test('admin can create a program', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $programData = [
        'title' => 'Computer Science',
        'description' => 'Comprehensive computer science program',
        'qualification_level' => 'Degree',
        'published' => true,
    ];

    $response = $this->actingAs($admin)
        ->post(route('admin.programs.store'), $programData);

    $response->assertRedirect(route('admin.programs.index'));
    $response->assertSessionHas('status', 'Program created successfully.');

    $this->assertDatabaseHas('programs', [
        'title' => 'Computer Science',
        'description' => 'Comprehensive computer science program',
        'qualification_level' => 'Degree',
        'published' => true,
    ]);
});

test('admin can update a program', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $program = Program::factory()->create();

    $updateData = [
        'title' => 'Updated Computer Science',
        'description' => 'Updated description',
        'qualification_level' => 'Degree',
        'published' => false,
    ];

    $response = $this->actingAs($admin)
        ->put(route('admin.programs.update', $program), $updateData);

    $response->assertRedirect(route('admin.programs.index'));
    $response->assertSessionHas('status', 'Program updated successfully.');

    $this->assertDatabaseHas('programs', [
        'id' => $program->id,
        'title' => 'Updated Computer Science',
        'description' => 'Updated description',
        'published' => false,
    ]);
});

test('admin can delete a program', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $program = Program::factory()->create();

    $response = $this->actingAs($admin)
        ->delete(route('admin.programs.destroy', $program));

    $response->assertRedirect(route('admin.programs.index'));
    $response->assertSessionHas('status', 'Program deleted successfully.');

    $this->assertDatabaseMissing('programs', [
        'id' => $program->id,
    ]);
});

test('admin can create a course with valid data', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $program = Program::factory()->create();

    $courseData = [
        'code' => 'CS101',
        'title' => 'Programming Fundamentals',
        'language' => 'English',
        'price' => 500.00,
        'program_id' => $program->id,
        'published' => true,
    ];

    $response = $this->actingAs($admin)
        ->post(route('admin.courses.store'), $courseData);

    $response->assertRedirect(route('admin.courses.index'));
    $response->assertSessionHas('status', 'Course created.');

    $this->assertDatabaseHas('courses', [
        'code' => 'CS101',
        'title' => 'Programming Fundamentals',
        'language' => 'English',
        'price' => 500.00,
        'program_id' => $program->id,
        'published' => true,
    ]);
});

test('course creation fails with invalid code format', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $program = Program::factory()->create();

    $courseData = [
        'code' => 'invalid-code', // Invalid format (lowercase and hyphen)
        'title' => 'Programming Fundamentals',
        'language' => 'English',
        'price' => 500.00,
        'program_id' => $program->id,
        'published' => true,
    ];

    $response = $this->actingAs($admin)
        ->post(route('admin.courses.store'), $courseData);

    $response->assertSessionHasErrors(['code']);
    $this->assertDatabaseMissing('courses', [
        'code' => 'invalid-code',
    ]);
});

test('course creation fails with duplicate code', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $program = Program::factory()->create();
    
    // Create first course
    Course::factory()->create([
        'code' => 'CS101',
        'program_id' => $program->id,
    ]);

    $courseData = [
        'code' => 'CS101', // Duplicate code
        'title' => 'Another Course',
        'language' => 'English',
        'price' => 300.00,
        'program_id' => $program->id,
        'published' => true,
    ];

    $response = $this->actingAs($admin)
        ->post(route('admin.courses.store'), $courseData);

    $response->assertSessionHasErrors(['code']);
});

test('course creation fails with negative price', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $program = Program::factory()->create();

    $courseData = [
        'code' => 'CS101',
        'title' => 'Programming Fundamentals',
        'language' => 'English',
        'price' => -100.00, // Negative price
        'program_id' => $program->id,
        'published' => true,
    ];

    $response = $this->actingAs($admin)
        ->post(route('admin.courses.store'), $courseData);

    $response->assertSessionHasErrors(['price']);
});

test('admin can soft delete a course', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $program = Program::factory()->create();
    $course = Course::factory()->create(['program_id' => $program->id]);

    $response = $this->actingAs($admin)
        ->delete(route('admin.courses.destroy', $course));

    $response->assertRedirect(route('admin.courses.index'));
    $response->assertSessionHas('status', 'Course deleted.');

    // Course should be soft deleted (deleted_at should not be null)
    $course->refresh();
    expect($course->deleted_at)->not->toBeNull();
    
    // But should still exist in database
    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
    ]);
});

test('admin can create a unit', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $unitData = [
        'btec_code' => 'A/123/2024',
        'title' => 'Introduction to Programming',
        'credit' => 3,
        'published' => true,
    ];

    $response = $this->actingAs($admin)
        ->post(route('admin.units.store'), $unitData);

    $response->assertRedirect(route('admin.units.index'));
    $response->assertSessionHas('status', 'Unit created.');

    $this->assertDatabaseHas('units', [
        'btec_code' => 'A/123/2024',
        'title' => 'Introduction to Programming',
        'credit' => 3,
        'published' => true,
    ]);
});

test('unit creation fails with invalid credits', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $unitData = [
        'btec_code' => 'B/456/2024',
        'title' => 'Introduction to Programming',
        'credit' => 0, // Invalid credit (must be at least 1)
        'published' => true,
    ];

    $response = $this->actingAs($admin)
        ->post(route('admin.units.store'), $unitData);

    $response->assertSessionHasErrors(['credit']);
});

test('student cannot access admin routes', function () {
    $student = User::factory()->create(['role' => 'Student']);

    // Try to access admin programs
    $response = $this->actingAs($student)
        ->get(route('admin.programs.index'));

    $response->assertStatus(403);

    // Try to create a program
    $response = $this->actingAs($student)
        ->post(route('admin.programs.store'), [
            'title' => 'Test Program',
            'description' => 'Test Description',
            'qualification_level' => 'Certificate',
        ]);

    $response->assertStatus(403);
});

test('guest cannot access admin routes', function () {
    // Try to access admin programs
    $response = $this->get(route('admin.programs.index'));
    $response->assertRedirect('/login');

    // Try to create a program
    $response = $this->post(route('admin.programs.store'), [
        'title' => 'Test Program',
        'description' => 'Test Description',
        'qualification_level' => 'Certificate',
    ]);

    $response->assertRedirect('/login');
});

test('admin can assign units to courses', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $program = Program::factory()->create();
    $course = Course::factory()->create(['program_id' => $program->id]);
    $unit = Unit::factory()->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.courses.assign-unit', $course), [
            'unit_id' => $unit->id,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('status', 'Unit assigned successfully.');

    // Check the relationship was created
    $this->assertDatabaseHas('course_unit', [
        'course_id' => $course->id,
        'unit_id' => $unit->id,
    ]);
});

test('admin can remove units from courses', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $program = Program::factory()->create();
    $course = Course::factory()->create(['program_id' => $program->id]);
    $unit = Unit::factory()->create();

    // First assign the unit
    $course->units()->attach($unit->id);

    // Then remove it
    $response = $this->actingAs($admin)
        ->post(route('admin.courses.remove-unit', $course), [
            'unit_id' => $unit->id,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('status', 'Unit removed successfully.');

    // Check the relationship was removed
    $this->assertDatabaseMissing('course_unit', [
        'course_id' => $course->id,
        'unit_id' => $unit->id,
    ]);
});
