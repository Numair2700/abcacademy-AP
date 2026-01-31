@extends('layout')

@section('content')
<div class="page-header">
    <h1 class="mb-1 mt-1">{{ $program->title }}</h1>
    <div>
        <a href="{{ route('admin.programs.edit', $program) }}" class="btn btn-primary">Edit Program</a>
        <a href="{{ route('admin.programs.index') }}" class="btn btn-success">Back to Programs</a>
    </div>
</div>

<div class="form-container">
    <div class="program-description-section">
        <h3>Program Details</h3>
        <div class="program-description-content">
            <p><strong>Qualification Level:</strong> {{ $program->qualification_level }}</p>
            <p><strong>Status:</strong> 
                @if($program->published)
                    <span class="badge badge-success">Published</span>
                @else
                    <span class="badge badge-secondary">Draft</span>
                @endif
            </p>
            <p><strong>Description:</strong></p>
            <div class="course-description">{{ $program->description }}</div>
        </div>
    </div>
</div>

@if($program->courses->count() > 0)
<div class="form-container">
    <h3>Course Management ({{ $program->courses->count() }} courses)</h3>
    <p class="text-muted mb-3">Manage courses in this program. You can delete courses or reassign them to other programs.</p>
    
    <div class="course-admin-table-container">
        <div style="overflow-x: auto;">
            <table class="course-admin-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Title</th>
                        <th>Language</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($program->courses as $course)
                        <tr>
                            <td>
                                <span class="course-code-badge">{{ $course->code }}</span>
                            </td>
                            <td>
                                <div class="course-title-main">{{ $course->title }}</div>
                            </td>
                            <td>{{ $course->language }}</td>
                            <td class="course-price">£{{ number_format($course->price, 2) }}</td>
                            <td>
                                @if($course->published)
                                    <span class="course-status-published">Published</span>
                                @else
                                    <span class="course-status-draft">Draft</span>
                                @endif
                            </td>
                            <td>
                                <div class="course-actions">
                                    <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-primary">Edit</a>
                                    
                                    <!-- Reassign Course Form -->
                                    <form action="{{ route('admin.programs.reassign-course', $program) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to reassign this course?')">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                                        <select name="new_program_id" class="form-control" style="display: inline-block; width: auto; margin-right: 5px;" required>
                                            <option value="">Move to...</option>
                                            @foreach($programs as $otherProgram)
                                                <option value="{{ $otherProgram->id }}">{{ $otherProgram->title }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-success" >Move</button>
                                    </form>
                                    
                                    <!-- Delete Course Form -->
                                    <form action="{{ route('admin.programs.delete-course', $program) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to DELETE this course? This action cannot be undone.')">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="form-container">
    <div class="course-empty-state">
        <div class="course-empty-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
        </div>
        <h3 class="course-empty-title">No courses in this program</h3>
        <p class="course-empty-text">This program has no courses yet. You can now safely delete this program.</p>
        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">Create Course</a>
    </div>
</div>
@endif

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@endsection
