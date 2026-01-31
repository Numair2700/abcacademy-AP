<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;

class CoursePolicy
{
    public function viewAny(?User $user = null): bool
    {
        return true;
    }

    public function view(?User $user, Course $course): bool
    {
        return $course->published || ($user && $user->role === 'Admin');
    }

    public function create(User $user): bool
    {
        return $user->role === 'Admin';
    }

    public function update(User $user, Course $course): bool
    {
        return $user->role === 'Admin';
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->role === 'Admin';
    }
}


