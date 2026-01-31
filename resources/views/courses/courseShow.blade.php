
@extends('layout')

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header">
           
            <h1>{{ $course->title }}</h1>
           
        </div>
        
        <div class="card-body">
            <div class="grid grid-2 mb-4">
                <div>
                    <div class="item-card">
                        <div class="item-title">📚 Program</div>
                        <div class="item-meta">{{ $course->program?->title ?? 'General Program' }}</div>
                    </div>
                    
                    <div class="item-card">
                        <div class="item-title">💰 Price</div>
                        <div class="item-meta">£{{ number_format($course->price, 2) }}</div>
                    </div>
                    
                    <div class="item-card">
                        <div class="item-title">🌍 Language</div>
                        <div class="item-meta">{{ $course->language }}</div>
                    </div>
                </div>
                
                <div>
                    <h3 class="section-title mb-3">Course Units</h3>
           
                    @forelse ($course->units as $unit)
                        <div class="item-card">
                            <div class="item-title">{{ $unit->title }}</div>
                            <div class="item-meta">{{ $unit->btec_code }}</div>
                            <div class="item-meta">
                                <strong>👨‍🏫 Tutor:</strong> 
                                @if($unit->tutors && $unit->tutors->isNotEmpty())
                                    {{ $unit->tutors->first()->name }}
                                @else
                                    <span class="text-muted">No tutor assigned</span>
                                @endif
                            </div>
                            <div class="item-actions">
                                <span class="badge badge-success">{{ $unit->credit }} credits</span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon">📝</div>
                            <p class="empty-text">No units assigned to this course yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            @auth
                @if (auth()->user()->role === 'Student')
                    @php
                        $user = auth()->user();
                        $isEnrolled = $user->student && \App\Models\Enrollment::where('student_id', $user->student->id)->where('course_id', $course->id)->exists();
                        $isFavorited = $user->favoriteCourses->contains($course->id);
                    @endphp
                    
                    @if($isEnrolled)
                        <div class="alert alert-info mb-4">
                            <span>✓</span>
                            <strong>You are enrolled in this course!</strong> You can view your enrollment details in your dashboard.
                            <a href="/dashboard" class="btn btn-primary btn-sm">View Dashboard →</a>
                        </div>
                    @endif
                    
                    <div class="grid grid-2">
                        <!-- Favorites Section -->
                        <div class="dashboard-section">
                            <div class="section-header">
                                <div class="section-icon favorites">⭐</div>
                                <h3 class="section-title">
                                    @if($isFavorited)
                                        Remove from Favorites
                                    @else
                                        Add to Favorites
                                    @endif
                                </h3>
                            </div>
                            
                            @if($isFavorited)
                                <p class="mb-3">This course is in your favorites.</p>
                                <form method="POST" action="{{ route('favorites.destroy', $course) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-full">
                                        Remove from Favorites
                                    </button>
                                </form>
                            @else
                                <p class="mb-3">Save this course to your favorites for easy access.</p>
                                <form method="POST" action="{{ route('favorites.store', $course) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-full">
                                        Add to Favorites
                                    </button>
                                </form>
                            @endif
                        </div>
                        
                        <!-- Enrollment Section -->
                        <div class="dashboard-section">
                            <div class="section-header">
                                <div class="section-icon enrollments">📚</div>
                                <h3 class="section-title">
                                    @if($isEnrolled)
                                        Enrollment Status
                                    @else
                                        Enroll Now
                                    @endif
                                </h3>
                            </div>
                            
                            @if($isEnrolled)
                                <p class="mb-3">You are already enrolled in this course.</p>
                                <a href="/dashboard" class="btn btn-success w-full text-center">
                                    View Enrollment Details
                                </a>
                            @else
                                <p class="mb-3">Enroll in this course with automatic registration details.</p>
                                <form method="POST" action="{{ route('enrollments.store', $course) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-full">
                                        Enroll in Course
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            @endauth
            
            <div class="text-center mt-4">
                <a href="{{ route('courses.index') }}" class="btn btn-primary">
                    ← Back to All Courses
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
    



