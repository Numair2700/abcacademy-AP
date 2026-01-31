<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    public function help()
    {
        return view('static.help');
    }

    public function contact()
    {
        return view('static.contact');
    }

    public function faq()
    {
        return view('static.faq');
    }

    public function privacy()
    {
        return view('static.privacy');
    }

    public function terms()
    {
        return view('static.terms');
    }

    public function cookies()
    {
        return view('static.cookies');
    }
}