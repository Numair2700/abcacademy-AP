<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class FavoriteController extends Controller
{
    public function store(Course $course)
    {
        $user = Auth::user();
        abort_if(!$user || $user->role !== 'Student', 403);
        $user->favoriteCourses()->syncWithoutDetaching([$course->id]);
        return Redirect::route('courses.show', $course)->with('status', 'Added to favorites.');
    }

    public function destroy(Course $course)
    {
        $user = Auth::user();
        abort_if(!$user || $user->role !== 'Student', 403);
        $user->favoriteCourses()->detach($course->id);
        return Redirect::route('courses.show', $course)->with('status', 'Removed from favorites.');
    }
}

