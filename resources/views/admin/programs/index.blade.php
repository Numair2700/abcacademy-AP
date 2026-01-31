@extends('layout')

@section('content')
<div class="page-header">
    <h1>Program Management</h1>
    <a href="{{ route('admin.programs.create') }}" class="btn btn-primary mt-2">Create New Program</a>
</div>

@if(session('status'))
    <div class="alert alert-success ">{{ session('status') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger mb-1 mt-2">{{ session('error') }}</div>
@endif

<div class="programs-grid">
    @forelse ($programs as $program)
        <div class="program-card">
            <div class="program-header">
                <h3 class="program-title">{{ $program->title }}</h3>
                <div class="program-status">
                    @if($program->published)
                        <span class="badge badge-success">Published</span>
                    @else
                        <span class="badge badge-secondary">Draft</span>
                    @endif
                </div>
            </div>
            
            <div class="program-description">
                {{ Str::limit($program->description, 150) }}
            </div>
            
            <div class="program-meta">
                <div class="meta-item">
                    <strong>Courses:</strong> {{ $program->courses_count }}
                </div>
                <div class="meta-item">
                    <strong>Qualification:</strong> {{ $program->qualification_level }}
                </div>
            </div>
            
            <div class="program-actions">
                <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-primary">Manage</a>
                <a href="{{ route('admin.programs.edit', $program) }}" class="btn  btn-primary">Edit</a>
                <form method="POST" action="{{ route('admin.programs.destroy', $program) }}" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <h3>No Programs Found</h3>
            <p>Create your first program to get started.</p>
            <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">Create Program</a>
        </div>
    @endforelse
</div>
@endsection