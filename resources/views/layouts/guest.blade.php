<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ABC Academy') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
<div class="card">
    <div class="card-header">
        <h1>Welcome to ABC Academy</h1>
        <p>Empowering students through quality education and innovative learning</p>
    </div>
    <div class="card-body">
        <div class="grid grid-3 mb-4">
            <div class="text-center">
                <h3 class="section-title">Quality Programs</h3>
                <p>Comprehensive courses designed by industry experts</p>
            </div>
        </div>
        <div class="text-center">
            <a href="{{ route('programs.index') }}" class="btn btn-primary btn-lg">Explore Programs</a>
            <a href="{{ route('courses.index') }}" class="btn btn-primary btn-lg">Explore Courses</a>
            @auth
                <a href="/dashboard" class="btn btn-success">Go to Dashboard -</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                <a href="{{ route('register') }}" class="btn btn-success">Register</a>
            @endauth
        </div>
    </div>

            </div>
    </body>
    </html>
    <div class="card">
        <div class="card-header">
            <h1>Welcome to ABC Academy</h1>
            <p>Empowering students through quality education and innovative learning</p>
        </div>
        <div class="card-body">
            <div class="grid grid-3 mb-4">
                <div class="text-center">
                    <h3 class="section-title">Quality Programs</h3>
                    <p>Comprehensive courses designed by industry experts</p>
                </div>
            </div>
            <div class="text-center">
                <a href="{{ route('programs.index') }}" class="btn btn-primary btn-lg">Explore Programs</a>
                <a href="{{ route('courses.index') }}" class="btn btn-primary btn-lg">Explore Courses</a>
                @auth
                    <a href="/dashboard" class="btn btn-success">Go to Dashboard -</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-success">Register</a>
                @endauth
            </div>
        </div>
