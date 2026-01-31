@extends('layout')

@section('content')
<div class="fade-in">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Our Programs</h1>
        <p class="dashboard-subtitle">Discover comprehensive educational programs designed to advance your career</p>
    </div>
    
    <div class="grid grid-2">
        @forelse ($programs as $program)
            <div class="course-card">
                <div class="course-header">
                    <div class="course-icon">{{ strtoupper(substr($program->title, 0, 3)) }}</div>
                   <div><h3>{{ $program->title }}</h3></div> 
                </div>
                <div class="course-body">
                    <h2 class="course-title">{{ $program->title }}</h2>
                    <div class="course-description">
                        {{ Str::limit($program->description, 120) }}
                    </div>
                    <div class="course-meta">
                        <div class="course-meta-item">
                            <span>📊 {{ $program->courses_count }} Courses Available</span>
                        </div>
                        <div class="course-meta-item">
                            <span>🎯 {{ $program->qualification_level }}</span>
                        </div>
                    </div>
                    <div class="item-actions">
                        <a href="{{ route('programs.show', $program) }}" class="btn btn-primary w-full text-center">
                            View Program Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
               
                <h3 class="empty-title">No programs available</h3>
                <p class="empty-text">Check back later for new program offerings.</p>
            </div>
        @endforelse
    </div>
    
    <div class="text-center mt-4">
        <a href="{{ route('courses.index') }}" class="btn btn-success btn-lg">Browse All Courses</a>
    </div>
</div>
@endsection