@extends('layout')

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <div class="course-icon">{{ substr($program->title, 0, 2) }}</div>
            <h1>{{ $program->title }}</h1>
            <p>Professional Qualification Program</p>
        </div>
        
        <div class="card-body">
            <div class="program-description-section mb-4">
                <h3 class="section-title">Program Description</h3>
                <div class="program-description-content">
                    {{ $program->description }}
                </div>
            </div>
            
            <div class="grid grid-2 mb-4">
                <div>
                    <div class="item-card">
                        <div class="item-title">📚 Program</div>
                        <div class="item-meta">{{ $program->title }}</div>
                    </div>
                    
                    <div class="item-card">
                        <div class="item-title">📊 Courses Available</div>
                        <div class="item-meta">{{ $program->courses->count() }} Courses</div>
                    </div>
                    
                    <div class="item-card">
                        <div class="item-title">🎯 Qualification Level</div>
                        <div class="item-meta">{{ $program->qualification_level }}</div>
                    </div>
                </div>
                
                <div>
                    <h3 class="section-title mb-3">Available Courses</h3>
                    @forelse ($program->courses as $course)
                        <div class="item-card">
                            <div class="item-title">{{ $course->title }}</div>
                            <div class="item-meta">{{ $course->code }} • {{ $course->language }}</div>
                            <div class="item-actions">
                                <span class="badge badge-primary">£{{ number_format($course->price, 2) }}</span>
                                <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-sm">View Course</a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon">📝</div>
                            <p class="empty-text">No courses available in this program yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('programs.index') }}" class="btn btn-primary">
                    ← Back to All Programs
                </a>
                <a href="{{ route('courses.index') }}" class="btn btn-success">
                    Browse All Courses
                </a>
            </div>
        </div>
    </div>
</div>
@endsection