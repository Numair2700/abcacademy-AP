@extends('layout')

@section('content')
<div class="page-header">
    
        <div>
            <h1 class="course-admin-title">Course Management</h1>
                    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary mt-1 mb-3">
            Create New Course
        </a>
        </div>

    
    
    <div class="course-admin-table-container">
        <div style="overflow-x: auto;">
            <table class="course-admin-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Title</th>
                        <th>Program</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($courses as $course)
                        <tr>
                            <td>
                                <span class="course-code-badge">
                                    {{ $course->code }}
                                </span>
                            </td>
                            <td>
                                <div class="course-title-main">{{ $course->title }}</div>
                                <div class="course-title-sub">{{ $course->language }}</div>
                            </td>
                            <td>
                                {{ $course->program?->title ?? 'No Program' }}
                            </td>
                            <td class="course-price">
                                £{{ number_format($course->price, 2) }}
                            </td>
                            <td>
                                @if($course->published)
                                    <span class="course-status-published">
                                        Published
                                    </span>
                                @else
                                    <span class="course-status-draft">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="course-actions">
                                    <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-primary">Manage</a>
                                    <a href="{{ route('admin.courses.edit', $course) }}" class="btn  btn-primary">Edit</a>
                                    <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this course?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="course-empty-state">
                                <div class="course-empty-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <h3 class="course-empty-title">No courses found</h3>
                                <p class="course-empty-text">Get started by creating your first course.</p>
                                <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                                    Create Course
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

