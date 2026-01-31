<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Enrollment;

class EnrollmentPolicy
{
    public function create(User $user): bool
    {
        return $user->role === 'Student' && $user->student !== null;
    }

    public function delete(User $user, Enrollment $enrollment): bool
    {
        return $user->role === 'Student' && $user->student && $user->student->id === $enrollment->student_id;
    }
}


