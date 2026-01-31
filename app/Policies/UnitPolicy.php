<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Unit;

class UnitPolicy
{
    public function viewAny(?User $user = null): bool
    {
        return true;
    }

    public function view(?User $user, Unit $unit): bool
    {
        return $unit->published || ($user && $user->role === 'Admin');
    }

    public function create(User $user): bool
    {
        return $user->role === 'Admin';
    }

    public function update(User $user, Unit $unit): bool
    {
        return $user->role === 'Admin';
    }

    public function delete(User $user, Unit $unit): bool
    {
        return $user->role === 'Admin';
    }
}


