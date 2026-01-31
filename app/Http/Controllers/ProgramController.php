<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display a listing of programs for public viewing.
     */
    public function index()
    {
        $programs = Program::withCount('courses')->get();
        
        return view('programs.index', compact('programs'));
    }

    /**
     * Display the specified program.
     */
    public function show(Program $program)
    {
        $program->load('courses');
        
        return view('programs.show', compact('program'));
    }
}