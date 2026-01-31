@extends('layout')

@section('content')
<div class="page-header">
    <h1>Edit Program</h1>
    <a href="{{ route('admin.programs.index') }}" class="btn btn-primary mt-1">Back to Programs</a>
</div>

<div class="form-container">
    <form method="POST" action="{{ route('admin.programs.update', $program) }}">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="title">Program Title</label>
            <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $program->title) }}" required>
            @error('title')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" rows="6" required>{{ old('description', $program->description) }}</textarea>
            @error('description')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="qualification_level">Qualification Level</label>
            <select id="qualification_level" name="qualification_level" class="form-control" required>
                <option value="">Choose qualification level...</option>
                <option value="Certificate" {{ old('qualification_level', $program->qualification_level) == 'Certificate' ? 'selected' : '' }}>Certificate</option>
                <option value="Diploma" {{ old('qualification_level', $program->qualification_level) == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                <option value="Degree" {{ old('qualification_level', $program->qualification_level) == 'Degree' ? 'selected' : '' }}>Degree</option>
            </select>
            @error('qualification_level')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="published" value="1" {{ old('published', $program->published) ? 'checked' : '' }}>
                Published
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Program</button>
            <a href="{{ route('admin.programs.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection