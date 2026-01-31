<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramAdminController extends Controller
{
    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        abort_if(!$user || $user->role !== 'Admin', 403);
    }

    /**
     * Display a listing of programs for admin management.
     */
    public function index()
    {
        $this->authorizeAdmin();
        $programs = Program::withCount('courses')->get();
        
        return view('admin.programs.index', compact('programs'));
    }

    /**
     * Show the form for creating a new program.
     */
    public function create()
    {
        $this->authorizeAdmin();
        return view('admin.programs.create');
    }

    /**
     * Store a newly created program in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();
        
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'qualification_level' => ['required', 'in:Certificate,Diploma,Degree'],
            'published' => ['sometimes', 'boolean'],
        ]);

        Program::create($validated);

        return redirect()->route('admin.programs.index')
            ->with('status', 'Program created successfully.');
    }

    /**
     * Show the form for editing the specified program.
     */
    public function edit(Program $program)
    {
        $this->authorizeAdmin();
        return view('admin.programs.edit', compact('program'));
    }

    /**
     * Update the specified program in storage.
     */
    public function update(Request $request, Program $program)
    {
        $this->authorizeAdmin();
        
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'qualification_level' => ['required', 'in:Certificate,Diploma,Degree'],
            'published' => ['sometimes', 'boolean'],
        ]);

        $program->update($validated);

        return redirect()->route('admin.programs.index')
            ->with('status', 'Program updated successfully.');
    }

    /**
     * Remove the specified program from storage.
     */
    public function destroy(Program $program)
    {
        $this->authorizeAdmin();
        
        // Check if program has courses
        if ($program->courses()->count() > 0) {
            return redirect()->route('admin.programs.index')
                ->with('error', 'Cannot delete program. The program must be empty of courses before deletion. Please manage courses first by clicking the "Manage" button.');
        }

        $program->delete();

        return redirect()->route('admin.programs.index')
            ->with('status', 'Program deleted successfully.');
    }

    /**
     * Show program details with course management.
     */
    public function show(Program $program)
    {
        $this->authorizeAdmin();
        $program->load('courses');
        $programs = Program::where('id', '!=', $program->id)->get(); // For reassignment
        
        return view('admin.programs.show', compact('program', 'programs'));
    }

    /**
     * Delete a course from a program.
     */
    public function deleteCourse(Request $request, Program $program)
    {
        $this->authorizeAdmin();
        
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $course = $program->courses()->findOrFail($request->course_id);
        $course->delete();

        return redirect()->route('admin.programs.show', $program)
            ->with('status', 'Course deleted successfully.');
    }

    /**
     * Reassign a course to a different program.
     */
    public function reassignCourse(Request $request, Program $program)
    {
        $this->authorizeAdmin();
        
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'new_program_id' => 'required|exists:programs,id'
        ]);

        $course = $program->courses()->findOrFail($request->course_id);
        $course->update(['program_id' => $request->new_program_id]);

        return redirect()->route('admin.programs.show', $program)
            ->with('status', 'Course reassigned successfully.');
    }
}


