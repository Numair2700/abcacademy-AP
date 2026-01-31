@extends('layout')

@section('content')
<div class="page-header">
    <h1>Create New Unit</h1>
    <a href="{{ route('admin.units.index') }}" class="btn btn-primary mt-1">Back to Units</a>
</div>

<div class="form-container">
    <form method="POST" action="{{ route('admin.units.store') }}">
        @csrf
        
        <div class="form-group">
            <label for="btec_code">BTEC Code</label>
            <input type="text" id="btec_code" name="btec_code" class="form-control" value="{{ old('btec_code') }}" placeholder="e.g., A/123/4567" required>
            @error('btec_code')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="title">Unit Title</label>
            <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required>
            @error('title')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="credit">Credit Value</label>
            <select id="credit" name="credit" class="form-control" required>
                <option value="">Choose credit value...</option>
                <option value="15" {{ old('credit') == '15' ? 'selected' : '' }}>15 Credits</option>
                <option value="60" {{ old('credit') == '60' ? 'selected' : '' }}>60 Credits</option>
                <option value="120" {{ old('credit') == '120' ? 'selected' : '' }}>120 Credits</option>
            </select>
            @error('credit')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="published" value="1" {{ old('published', true) ? 'checked' : '' }}>
                Published
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn  btn-primary">Create Unit</button>
            <a href="{{ route('admin.units.index') }}" class="btn  btn-danger">Cancel</a>
        </div>
    </form>
</div>
@endsection