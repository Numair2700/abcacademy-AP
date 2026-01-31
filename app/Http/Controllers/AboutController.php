<?php

namespace App\Http\Controllers;

use App\Models\AboutContent;
use App\Models\Tutor;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $aboutContent = AboutContent::published()
            ->ordered()
            ->get()
            ->groupBy('section');

        $tutors = Tutor::where('status', 'active')->get();

        return view('about.index', compact('aboutContent', 'tutors'));
    }
}
