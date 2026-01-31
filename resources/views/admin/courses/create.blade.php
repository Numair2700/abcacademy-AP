@extends('layout')

@section('content')

    <div class="page-header">
        <h1>Create New Course</h1>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-primary mt-1">Back to Courses</a>
    </div>
    <div class="form-container">
    <form action="{{ route('admin.courses.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="code">Course Code</label>
            <input id="code" name="code" type="text" class="form-control" required placeholder="e.g., HMHN1" value="{{ old('code') }}">
            @error('code')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="title">Course Title</label>
            <input id="title" name="title" type="text" class="form-control" required placeholder="e.g., Advanced Web Development" value="{{ old('title') }}">
            @error('title')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="language">Language</label>
            <input id="language" name="language" type="text" class="form-control" required placeholder="e.g., English" value="{{ old('language') }}">
            @error('language')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="price">Price (£)</label>
            <input id="price" name="price" type="number" step="0.01" min="0" class="form-control" required placeholder="0.00" value="{{ old('price') }}">
            @error('price')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="program_id">Program</label>
            <select id="program_id" name="program_id" class="form-control" required>
                <option value="">Select a program</option>
                @foreach($programs as $program)
                    <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>{{ $program->title }}</option>
                @endforeach
            </select>
            @error('program_id')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="published" value="1" checked>
                Published (visible to students)
            </label>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn  btn-primary">Create Course</button>
            <a href="{{ route('admin.courses.index') }}" class="btn  btn-danger">Cancel</a>
        </div>
    </form>
</div>
@endsection

