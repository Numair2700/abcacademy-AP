@extends('layout')

@section('content')
<div class="form-container">
    <div class="page-header">
        <h1>Edit Course</h1>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-primary mt-1">Back to Courses</a>
    </div>
    
    <form action="{{ route('admin.courses.update', $course) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="code">Course Code</label>
            <input id="code" name="code" type="text" class="form-control" value="{{ old('code', $course->code) }}" required>
            @error('code')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="title">Course Title</label>
            <input id="title" name="title" type="text" class="form-control" value="{{ old('title', $course->title) }}" required>
            @error('title')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="language">Language</label>
            <input id="language" name="language" type="text" class="form-control" value="{{ old('language', $course->language) }}" required>
            @error('language')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="price">Price (£)</label>
            <input id="price" name="price" type="number" step="0.01" min="0" class="form-control" value="{{ old('price', $course->price) }}" required>
            @error('price')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="program_id">Program</label>
            <select id="program_id" name="program_id" class="form-control" required>
                <option value="">Select a program</option>
                @foreach($programs as $program)
                    <option value="{{ $program->id }}" {{ $program->id == $course->program_id ? 'selected' : '' }}>{{ $program->title }}</option>
                @endforeach
            </select>
            @error('program_id')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="published" value="1" {{ $course->published ? 'checked' : '' }}>
                Published (visible to students)
            </label>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Course</button>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

