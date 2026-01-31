<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Unit;
use App\Policies\CoursePolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\UnitPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Course::class => CoursePolicy::class,
        Unit::class => UnitPolicy::class,
        Enrollment::class => EnrollmentPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}


