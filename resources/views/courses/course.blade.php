@extends('layout')

@section('content')
<div class="fade-in">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Our Courses</h1>
        <p class="dashboard-subtitle">Discover comprehensive programs designed to advance your career</p>
    </div>
    
    <div class="grid grid-3">
        @forelse ($courses as $course)
            <div class="course-card">
                <div class="course-header">
                    
                    <h3>{{ $course->title}}</h3>
                </div>
                <div class="course-body">
                    <h2 class="course-title">{{ $course->title }}</h2>
                    <div class="course-meta">
                        <div class="course-meta-item">
                            <span class="course-meta-icon">📚</span>
                            <span>{{ $course->program?->title ?? 'General Program' }}</span>
                        </div>
                        <div class="course-meta-item">
                            <span class="course-meta-icon">💰</span>
                            <span>£{{ number_format($course->price, 2) }}</span>
                        </div>
                        <div class="course-meta-item">
                            <span class="course-meta-icon">🌍</span>
                            <span>{{ $course->language }}</span>
                        </div>
                    </div>
                    <a href="{{ route('courses.show', $course) }}" class="btn btn-primary w-full text-center">
                        View Details
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">📚</div>
                <h3 class="empty-title">No courses available</h3>
                <p class="empty-text">Check back later for new course offerings.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection