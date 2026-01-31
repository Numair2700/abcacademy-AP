<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Course;

test('student can add course to favorites', function () {
    $user = User::factory()->create(['role' => 'Student']);
    $student = Student::factory()->create(['user_id' => $user->id]);
    
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    $response = $this->actingAs($user)
        ->post(route('favorites.store', $course));

    $response->assertRedirect();
    $response->assertSessionHas('status', 'Added to favorites.');

    // Check the favorite was created
    $this->assertDatabaseHas('favorites', [
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);

    // Check the relationship
    expect($user->favoriteCourses)->toHaveCount(1);
    expect($user->favoriteCourses->first()->id)->toBe($course->id);
});

test('student can remove course from favorites', function () {
    $user = User::factory()->create(['role' => 'Student']);
    $student = Student::factory()->create(['user_id' => $user->id]);
    
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    // First add to favorites
    $user->favoriteCourses()->attach($course->id);

    // Then remove from favorites
    $response = $this->actingAs($user)
        ->delete(route('favorites.destroy', $course));

    $response->assertRedirect();
    $response->assertSessionHas('status', 'Removed from favorites.');

    // Check the favorite was removed
    $this->assertDatabaseMissing('favorites', [
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);

    // Check the relationship
    expect($user->fresh()->favoriteCourses)->toHaveCount(0);
});

test('student cannot add same course to favorites twice', function () {
    $user = User::factory()->create(['role' => 'Student']);
    $student = Student::factory()->create(['user_id' => $user->id]);
    
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    // Add to favorites first time
    $this->actingAs($user)
        ->post(route('favorites.store', $course));

    // Try to add again
    $response = $this->actingAs($user)
        ->post(route('favorites.store', $course));

    $response->assertRedirect();
    $response->assertSessionHas('status', 'Added to favorites.');

    // Should still only have one favorite
    expect($user->fresh()->favoriteCourses)->toHaveCount(1);
    $this->assertDatabaseCount('favorites', 1);
});

test('admin cannot add courses to favorites', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    $response = $this->actingAs($admin)
        ->post(route('favorites.store', $course));

    $response->assertStatus(403);

    // No favorite should be created
    $this->assertDatabaseMissing('favorites', [
        'user_id' => $admin->id,
        'course_id' => $course->id,
    ]);
});

test('guest cannot add courses to favorites', function () {
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    $response = $this->post(route('favorites.store', $course));

    $response->assertRedirect('/login');

    // No favorite should be created
    $this->assertDatabaseMissing('favorites', [
        'course_id' => $course->id,
    ]);
});

test('student can view their favorite courses', function () {
    $user = User::factory()->create(['role' => 'Student']);
    $student = Student::factory()->create(['user_id' => $user->id]);
    
    $program = Program::factory()->create(['published' => true]);
    $course1 = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true,
        'title' => 'Course 1'
    ]);
    $course2 = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true,
        'title' => 'Course 2'
    ]);

    // Add courses to favorites
    $user->favoriteCourses()->attach([$course1->id, $course2->id]);

    // Access dashboard (which should show favorites)
    $response = $this->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
    // The dashboard view should contain the favorite courses
    $response->assertSee('Course 1');
    $response->assertSee('Course 2');
});

test('favorites are deleted when user is deleted', function () {
    $user = User::factory()->create(['role' => 'Student']);
    $student = Student::factory()->create(['user_id' => $user->id]);
    
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    // Add to favorites
    $user->favoriteCourses()->attach($course->id);

    // Verify favorite exists
    $this->assertDatabaseHas('favorites', [
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);

    // Delete user
    $user->delete();

    // Favorite should be deleted (cascade delete)
    $this->assertDatabaseMissing('favorites', [
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);
});

test('favorites are deleted when course is deleted', function () {
    $user = User::factory()->create(['role' => 'Student']);
    $student = Student::factory()->create(['user_id' => $user->id]);
    
    $program = Program::factory()->create(['published' => true]);
    $course = Course::factory()->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    // Add to favorites
    $user->favoriteCourses()->attach($course->id);

    // Verify favorite exists
    $this->assertDatabaseHas('favorites', [
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);

    // Soft delete course
    $course->delete();

    // Favorite should be deleted (cascade delete)
    $this->assertDatabaseMissing('favorites', [
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);
});

test('student can have multiple favorite courses', function () {
    $user = User::factory()->create(['role' => 'Student']);
    $student = Student::factory()->create(['user_id' => $user->id]);
    
    $program = Program::factory()->create(['published' => true]);
    $courses = Course::factory()->count(5)->create([
        'program_id' => $program->id,
        'published' => true
    ]);

    // Add all courses to favorites
    foreach ($courses as $course) {
        $this->actingAs($user)
            ->post(route('favorites.store', $course));
    }

    // Check all favorites were created
    expect($user->fresh()->favoriteCourses)->toHaveCount(5);
    $this->assertDatabaseCount('favorites', 5);

    // Remove one favorite
    $this->actingAs($user)
        ->delete(route('favorites.destroy', $courses->first()));

    // Check count decreased
    expect($user->fresh()->favoriteCourses)->toHaveCount(4);
    $this->assertDatabaseCount('favorites', 4);
});
