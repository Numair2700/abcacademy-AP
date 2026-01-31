<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AboutController extends Controller
{
    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        abort_if(!$user || $user->role !== 'Admin', 403);
    }

    public function index()
    {
        $this->authorizeAdmin();
        $aboutContent = AboutContent::ordered()->get()->groupBy('section');
        
        return view('admin.about.index', compact('aboutContent'));
    }

    public function editContent(Request $request)
    {
        $this->authorizeAdmin();
        
        $request->validate([
            'section' => 'required|string',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'required|integer|min:0',
            'published' => 'boolean'
        ]);

        AboutContent::updateOrCreate(
            ['section' => $request->section],
            [
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'order' => $request->input('order'),
                'published' => $request->boolean('published', true)
            ]
        );

        return redirect()->route('admin.about.index')->with('status', 'About content updated successfully.');
    }
}
