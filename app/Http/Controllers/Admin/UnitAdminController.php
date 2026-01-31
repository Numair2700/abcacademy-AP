<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitAdminController extends Controller
{
    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        abort_if(!$user || $user->role !== 'Admin', 403);
    }

    public function index()
    {
        $this->authorizeAdmin();
        $units = Unit::with('tutors')->orderByDesc('id')->get();
        $tutors = Tutor::all();
        return view('admin.units.index', compact('units', 'tutors'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        return view('admin.units.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $validated = $request->validate([
            'btec_code' => ['required', 'string', 'max:20', 'regex:/^[A-Z]\/\d{3}\/\d{4}$/', 'unique:units,btec_code'],
            'title' => ['required', 'string', 'max:255'],
            'credit' => ['required', 'integer', 'min:1'],
            'published' => ['sometimes', 'boolean'],
        ]);
        $validated['published'] = (bool)($request->boolean('published'));
        Unit::create($validated);
        return redirect()->route('admin.units.index')->with('status', 'Unit created.');
    }

    public function edit(Unit $unit)
    {
        $this->authorizeAdmin();
        return view('admin.units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $this->authorizeAdmin();
        $validated = $request->validate([
            'btec_code' => ['required', 'string', 'max:20', 'regex:/^[A-Z]\/\d{3}\/\d{4}$/', 'unique:units,btec_code,'.$unit->id],
            'title' => ['required', 'string', 'max:255'],
            'credit' => ['required', 'integer', 'min:1'],
            'published' => ['sometimes', 'boolean'],
        ]);
        $validated['published'] = (bool)($request->boolean('published'));
        $unit->update($validated);
        return redirect()->route('admin.units.index')->with('status', 'Unit updated.');
    }

    public function destroy(Unit $unit)
    {
        $this->authorizeAdmin();
        $unit->delete();
        return redirect()->route('admin.units.index')->with('status', 'Unit deleted.');
    }

    public function assignTutor(Request $request)
    {
        $this->authorizeAdmin();
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'tutor_id' => 'required|exists:tutors,id',
        ]);

        $unit = Unit::findOrFail($validated['unit_id']);
        $tutor = Tutor::findOrFail($validated['tutor_id']);

        // Detach any existing tutors from this unit first
        $unit->tutors()->detach();
        // Attach the new tutor
        $unit->tutors()->attach($tutor->id);

        return back()->with('status', 'Tutor assigned to unit successfully.');
    }

    public function removeTutor(Request $request)
    {
        $this->authorizeAdmin();
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'tutor_id' => 'required|exists:tutors,id',
        ]);

        $unit = Unit::findOrFail($validated['unit_id']);
        $tutor = Tutor::findOrFail($validated['tutor_id']);

        $unit->tutors()->detach($tutor->id);

        return back()->with('status', 'Tutor removed from unit successfully.');
    }
}


