@extends('layout')

@section('content')
<div class="page-header">
    <h1 class="mb-1 mt-1">{{ $course->title }}</h1>
    <div>
        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-primary">Edit Course</a>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-success">Back to Courses</a>
    </div>
</div>

<div class="form-container">
    <div class="program-description-section">
        <h3>Course Details</h3>
        <div class="program-description-content">
            <p><strong>Code:</strong> {{ $course->code }}</p>
            <p><strong>Language:</strong> {{ $course->language }}</p>
            <p><strong>Price:</strong> £{{ number_format($course->price, 2) }}</p>
            <p><strong>Program:</strong> {{ $course->program->title }}</p>
            <p><strong>Status:</strong> 
                @if($course->published)
                    <span class="badge badge-success">Published</span>
                @else
                    <span class="badge badge-secondary">Draft</span>
                @endif
            </p>
        </div>
    </div>
</div>

<!-- Unit Assignment Section -->
<div class="form-container">
    <h3>Assign New Unit</h3>
    <p class="text-muted mb-3">Add units to this course from the available units list.</p>
    
    <form method="POST" action="{{ route('admin.courses.assign-unit', $course) }}">
        @csrf
        <div class="form-group">
            <label for="unit_id">Select Unit to Assign:</label>
            <select id="unit_id" name="unit_id" class="form-control" required>
                <option value="">Choose a unit...</option>
                @foreach($units as $unit)
                    @if(!$course->units->contains($unit->id))
                        <option value="{{ $unit->id }}">{{ $unit->btec_code }} - {{ $unit->title }} ({{ $unit->credit }} credits)</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Assign Unit</button>
        </div>
    </form>
</div>

@if($course->units->count() > 0)
<div class="form-container">
    <h3>Assigned Units ({{ $course->units->count() }} units)</h3>
    <p class="text-muted mb-3">Manage units assigned to this course. You can remove units from this course.</p>
    
    <div class="course-admin-table-container">
        <div style="overflow-x: auto;">
            <table class="course-admin-table">
                <thead>
                    <tr>
                        <th>BTEC Code</th>
                        <th>Title</th>
                        <th>Credit</th>
                        <th>Tutor</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($course->units as $unit)
                        <tr>
                            <td>
                                <span class="course-code-badge">{{ $unit->btec_code }}</span>
                            </td>
                            <td>
                                <div class="course-title-main">{{ $unit->title }}</div>
                            </td>
                            <td class="course-price">
                                {{ $unit->credit }} credits
                            </td>
                            <td>
                                @if($unit->tutors->isNotEmpty())
                                    <div class="course-title-main">{{ $unit->tutors->first()->name }}</div>
                                    <div class="course-title-sub">{{ $unit->tutors->first()->specialization }}</div>
                                @else
                                    <span class="course-title-sub">No tutor assigned</span>
                                @endif
                            </td>
                            <td>
                                @if($unit->published)
                                    <span class="course-status-published">Published</span>
                                @else
                                    <span class="course-status-draft">Draft</span>
                                @endif
                            </td>
                            <td>
                                <div class="course-actions">
                                    <a href="{{ route('admin.units.edit', $unit) }}" class="btn btn-primary">Edit</a>
                                    
                                    <!-- Remove Unit Form -->
                                    <form action="{{ route('admin.courses.remove-unit', $course) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to remove this unit from the course?')">
                                        @csrf
                                        <input type="hidden" name="unit_id" value="{{ $unit->id }}">
                                        <button type="submit" class="btn btn-sm btn-danger">Remove</button>
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
        <h3 class="course-empty-title">No units assigned to this course</h3>
        <p class="course-empty-text">This course has no units yet. Use the form above to assign units.</p>
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
