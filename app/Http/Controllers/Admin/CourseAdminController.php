<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Program;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseAdminController extends Controller
{
    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        abort_if(!$user || $user->role !== 'Admin', 403);
    }

    public function index()
    {
        $this->authorizeAdmin();
        $courses = Course::with('program')->orderByDesc('id')->paginate(15);
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        $programs = Program::orderBy('title')->get();
        return view('admin.courses.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9]{3,10}$/', 'unique:courses,code'],
            'title' => ['required', 'string', 'max:255'],
            'language' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'program_id' => ['required', 'exists:programs,id'],
            'published' => ['sometimes', 'boolean'],
        ]);
        $validated['published'] = (bool)($request->boolean('published'));
        Course::create($validated);
        return redirect()->route('admin.courses.index')->with('status', 'Course created.');
    }

    public function edit(Course $course)
    {
        $this->authorizeAdmin();
        $programs = Program::orderBy('title')->get();
        return view('admin.courses.edit', compact('course', 'programs'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorizeAdmin();
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9]{3,10}$/', 'unique:courses,code,'.$course->id],
            'title' => ['required', 'string', 'max:255'],
            'language' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'program_id' => ['required', 'exists:programs,id'],
            'published' => ['sometimes', 'boolean'],
        ]);
        $validated['published'] = (bool)($request->boolean('published'));
        $course->update($validated);
        return redirect()->route('admin.courses.index')->with('status', 'Course updated.');
    }

    public function destroy(Course $course)
    {
        $this->authorizeAdmin();
        $course->delete();
        return redirect()->route('admin.courses.index')->with('status', 'Course deleted.');
    }

    /**
     * Show course details with unit management.
     */
    public function show(Course $course)
    {
        $this->authorizeAdmin();
        $course->load('units', 'program');
        $units = Unit::orderBy('btec_code')->get(); // All available units
        
        return view('admin.courses.show', compact('course', 'units'));
    }

    /**
     * Assign a unit to a course.
     */
    public function assignUnit(Request $request, Course $course)
    {
        $this->authorizeAdmin();
        
        $request->validate([
            'unit_id' => 'required|exists:units,id'
        ]);

        // Check if unit is already assigned
        if ($course->units()->where('unit_id', $request->unit_id)->exists()) {
            return redirect()->route('admin.courses.show', $course)
                ->with('error', 'Unit is already assigned to this course.');
        }

        $course->units()->attach($request->unit_id);

        return redirect()->route('admin.courses.show', $course)
            ->with('status', 'Unit assigned successfully.');
    }

    /**
     * Remove a unit from a course.
     */
    public function removeUnit(Request $request, Course $course)
    {
        $this->authorizeAdmin();
        
        $request->validate([
            'unit_id' => 'required|exists:units,id'
        ]);

        $course->units()->detach($request->unit_id);

        return redirect()->route('admin.courses.show', $course)
            ->with('status', 'Unit removed successfully.');
    }
}


