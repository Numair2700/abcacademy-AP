@extends('layout')

@section('content')
<div class="fade-in">
    @php($user = auth()->user()->ensureStudentProfile())
    
    <div class="dashboard-header">
        <h1 class="dashboard-title">Welcome back, {{ $user->name }}!</h1>
        <p class="dashboard-subtitle">Manage your learning journey with ABC Academy</p>
    </div>
    
    @if ($user->role === 'Student')
        <div class="grid grid-2 mb-4">
            <!-- Programs Section -->
            <div class="dashboard-section">
                <div class="section-header">
                   
                    <h2 class="section-title">Available Programs</h2>
                </div>
                
                @php($programs = \App\Models\Program::withCount('courses')->take(3)->get())
                @forelse($programs as $program)
                    <div class="item-card">
                        <div class="item-title">{{ $program->title }}</div>
                        <div class="item-meta">{{ $program->courses_count }} Courses Available</div>
                        <div class="item-actions">
                            <a href="{{ route('programs.show', $program) }}" class="btn btn-primary btn-sm">View Program</a>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                    
                        <h3 class="empty-title">No programs available</h3>
                        <p class="empty-text">Check back later for new programs!</p>
                    </div>
                @endforelse
                
                <div class="text-center mt-3">
                    <a href="{{ route('programs.index') }}" class="btn btn-success btn-sm">View All Programs</a>
                </div>
            </div>
            
            <!-- Courses Section -->
            <div class="dashboard-section">
                <div class="section-header">
                  
                    <h2 class="section-title">Available Courses</h2>
                </div>
                
                @php($courses = \App\Models\Course::with('program')->take(3)->get())
                @forelse($courses as $course)
                    <div class="item-card">
                        <div class="item-title">{{ $course->title }}</div>
                        <div class="item-meta">{{ $course->code }} • {{ $course->program?->title ?? 'General Program' }}</div>
                        <div class="item-actions">
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-sm">View Course</a>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                     
                        <h3 class="empty-title">No courses available</h3>
                        <p class="empty-text">Check back later for new courses!</p>
                    </div>
                @endforelse
                
                <div class="text-center mt-3">
                    <a href="{{ route('courses.index') }}" class="btn btn-success btn-sm">View All Courses</a>
                </div>
            </div>
        </div>
        
        <div class="grid grid-2 mb-4">
            <!-- Favorites Section -->
            <div class="dashboard-section">
                <div class="section-header">
                   
                    <h2 class="section-title">Your Favorites</h2>
                </div>
                
                @forelse($user->favoriteCourses as $course)
                    <div class="item-card">
                        <div class="item-title">{{ $course->title }}</div>
                        <div class="item-meta">{{ $course->code }} • {{ $course->program?->title ?? 'General Program' }}</div>
                        <div class="item-actions">
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-sm">View</a>
                            <form method="POST" action="{{ route('favorites.destroy', $course) }}" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Remove from favorites?')"> Remove from Favorites </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">⭐</div>
                        <h3 class="empty-title">No favorites yet</h3>
                        <p class="empty-text">Browse courses to add some!</p>
                        <a href="{{ route('courses.index') }}" class="btn btn-success">Browse Courses</a>
                    </div>
                @endforelse
            </div>
            
            <!-- Enrollments Section -->
            <div class="dashboard-section">
                <div class="section-header">
                 
                    <h2 class="section-title">Your Enrollments</h2>
                </div>
                
                @php($enrollments = $user->student ? \App\Models\Enrollment::with('course')->where('student_id', $user->student->id)->latest()->get() : collect())
                
                @forelse($enrollments as $enrollment)
                    <div class="item-card">
                        <div class="item-title">{{ $enrollment->course->title }}</div>
                        
                        <!-- Enrollment Details with Badges -->
                        <div class="enrollment-badges">
                            <span class="badge badge-primary">{{ $enrollment->session }}</span>
                            <span class="badge badge-success">{{ $enrollment->btec_number }}</span>
                            <span class="badge badge-warning">{{ $enrollment->created_at->format('Y-m-d') }}</span>
                        </div>
                        
                        <div class="item-actions">
                            <a href="{{ route('courses.show', $enrollment->course) }}" class="btn btn-success btn-sm">View</a>
                            <form method="POST" action="{{ route('enrollments.destroy', $enrollment) }}" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to unenroll from this course?')">Unenroll</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">📚</div>
                        <h3 class="empty-title">No enrollments yet</h3>
                        <p class="empty-text">Enroll in courses to start learning!</p>
                        <a href="{{ route('courses.index') }}" class="btn btn-success">Browse Courses</a>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Bottom Action Buttons -->
        <div class="dashboard-bottom-actions">
            <a href="{{ route('programs.index') }}" class="btn btn-primary btn-lg">Browse All Programs</a>
            <a href="{{ route('courses.index') }}" class="btn btn-success btn-lg">Browse All Courses</a>
        </div>
    @else
        <div class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title" >Admin Dashboard</h2></div>
                <p style="text-align: center;">Manage programs, courses, and units for ABC Academy</p>
            
            
            <div class="admin-buttons-grid">
                <a href="{{ route('admin.programs.index') }}" class="admin-button programs">
                 
                    <div class="admin-button-content">
                        <h3>Programs</h3>
                        <p>Manage educational programs</p>
                    </div>
                </a>
                
                <a href="{{ route('admin.courses.index') }}" class="admin-button courses">
                 
                    <div class="admin-button-content">
                        <h3>Courses</h3>
                        <p>Manage course offerings</p>
                    </div>
                </a>
                
                <a href="{{ route('admin.units.index') }}" class="admin-button units">
                
                    <div class="admin-button-content">
                        <h3>Units</h3>
                        <p>Manage course units</p>
                    </div>
                </a>
                
                <a href="{{ route('admin.about.index') }}" class="admin-button about">
                
                    <div class="admin-button-content">
                        <h3>About Page</h3>
                        <p>Manage about content</p>
                    </div>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection