<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('published', true)->with('program')->latest()->get();

        return view('courses.course', compact('courses' ));
    }
    public function show($id)
    {
        $course = Course::with(['program', 'units.tutors'])->findOrFail($id);

        return view('courses.courseShow', compact('course'));
    }
}