<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class EnrollmentController extends Controller
{
    protected EnrollmentService $enrollmentService;

    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    public function store(Request $request, Course $course)
    {
        $this->authorize('create', \App\Models\Enrollment::class);

        $user = Auth::user();
        
        try {
            $enrollment = $this->enrollmentService->createEnrollment($user, $course);
            
            return Redirect::route('courses.show', $course)
                ->with('status', 'Successfully enrolled in ' . $course->title . '!');
                
        } catch (\Exception $e) {
            return Redirect::route('courses.show', $course)
                ->with('status', $e->getMessage());
        }
    }

    public function destroy(Enrollment $enrollment)
    {
        $this->authorize('delete', $enrollment);

        try {
            $this->enrollmentService->cancelEnrollment($enrollment);
            
            return Redirect::route('courses.show', $enrollment->course_id)
                ->with('status', 'Enrollment cancelled.');
                
        } catch (\Exception $e) {
            return Redirect::route('courses.show', $enrollment->course_id)
                ->with('error', 'Failed to cancel enrollment: ' . $e->getMessage());
        }
    }
}


