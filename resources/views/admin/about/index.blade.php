@extends('layout')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title mb-3 mt-2">Admin - About Page Management</h1>
       
    </div>

    <!-- About Content Management -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">📝 About Page Content</h2>
        </div>
        <div class="card-body">
            @if(isset($aboutContent['mission']))
                <div class="item-card">
                    <h3>{{ $aboutContent['mission']->first()->title }}</h3>
                    <p>{{ $aboutContent['mission']->first()->content }}</p>
                    <form method="POST" action="{{ route('admin.about.edit-content') }}" class="mt-3">
                        @csrf
                        <input type="hidden" name="section" value="mission">
                        <div class="form-group">
                            <label for="mission_title">Title:</label>
                            <input type="text" id="mission_title" name="title" value="{{ $aboutContent['mission']->first()->title }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="mission_content">Content:</label>
                            <textarea id="mission_content" name="content" class="form-control" rows="4" required>{{ $aboutContent['mission']->first()->content }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="mission_order">Order:</label>
                            <input type="number" id="mission_order" name="order" value="{{ $aboutContent['mission']->first()->order }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="published" value="1" {{ $aboutContent['mission']->first()->published ? 'checked' : '' }}>
                                Published
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Mission</button>
                    </form>
                </div>
            @endif

            @if(isset($aboutContent['vision']))
                <div class="item-card">
                    <h3>{{ $aboutContent['vision']->first()->title }}</h3>
                    <p>{{ $aboutContent['vision']->first()->content }}</p>
                    <form method="POST" action="{{ route('admin.about.edit-content') }}" class="mt-3">
                        @csrf
                        <input type="hidden" name="section" value="vision">
                        <div class="form-group">
                            <label for="vision_title">Title:</label>
                            <input type="text" id="vision_title" name="title" value="{{ $aboutContent['vision']->first()->title }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="vision_content">Content:</label>
                            <textarea id="vision_content" name="content" class="form-control" rows="4" required>{{ $aboutContent['vision']->first()->content }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="vision_order">Order:</label>
                            <input type="number" id="vision_order" name="order" value="{{ $aboutContent['vision']->first()->order }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="published" value="1" {{ $aboutContent['vision']->first()->published ? 'checked' : '' }}>
                                Published
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Vision</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('about.index') }}" class="btn btn-primary">View Public About Page</a>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>
@endsection
